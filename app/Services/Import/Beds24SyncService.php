<?php

namespace App\Services\Import;

use App\Models\Departamento;
use App\Models\Importacion;
use App\Models\Plataforma;
use App\Models\Reserva;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Cliente de sincronización contra la API v2 de Beds24 (https://api.beds24.com/v2).
 *
 * NOTA: el mapeo de campos de la respuesta de /bookings (invoiceItems, referer de la OTA,
 * etc.) está basado en la documentación pública de Beds24 y debe verificarse/ajustarse
 * contra respuestas reales de la cuenta conectada. El departamento se resuelve por
 * coincidencia exacta de `beds24_prop_id`/`beds24_room_id` (configurados en cada
 * Departamento), no por nombre.
 */
class Beds24SyncService
{
    private const CACHE_KEY_TOKEN = 'beds24_access_token';

    public function sync(): Importacion
    {
        $bookings = $this->fetchBookings();

        $creadas = 0;
        $actualizadas = 0;
        $errores = 0;
        $detalleErrores = [];

        foreach ($bookings as $booking) {
            try {
                $resultado = $this->upsertBooking($booking);
                $resultado ? $creadas++ : $actualizadas++;
            } catch (\Throwable $e) {
                $errores++;
                $detalleErrores[] = ['booking_id' => $booking['id'] ?? null, 'error' => $e->getMessage()];
            }
        }

        return Importacion::create([
            'tipo' => 'beds24_sync',
            'origen' => 'beds24',
            'usuario_id' => null,
            'total_filas' => count($bookings),
            'total_creadas' => $creadas,
            'total_actualizadas' => $actualizadas,
            'total_error' => $errores,
            'detalle_errores' => $detalleErrores,
        ]);
    }

    /**
     * Procesa un único booking recibido (vía webhook o polling). Devuelve true si se creó.
     */
    public function upsertBooking(array $booking): bool
    {
        $plataforma = $this->resolverPlataforma($booking['referer'] ?? $booking['channel'] ?? null);
        $departamento = $this->resolverDepartamento($booking['propId'] ?? null, $booking['roomId'] ?? null);

        if (! $plataforma || ! $departamento) {
            throw new \RuntimeException('No se pudo determinar la plataforma o el departamento del booking.');
        }

        [$montoBruto, $comisionPlataforma, $moneda] = $this->extraerImportes($booking);

        $pctCoanfitrion = (float) ($departamento->comision_coanfitrion_pct ?? 0);
        $comisionCoanfitrion = round($montoBruto * $pctCoanfitrion / 100, 2);
        $ingresoLiquido = $montoBruto - $comisionPlataforma - $comisionCoanfitrion;

        $reserva = Reserva::withoutGlobalScopes()->updateOrCreate(
            [
                'plataforma_id' => $plataforma->id,
                'codigo_externo' => (string) $booking['id'],
            ],
            [
                'departamento_id' => $departamento->id,
                'huesped' => trim(($booking['firstName'] ?? '').' '.($booking['lastName'] ?? '')) ?: null,
                'fecha_checkin' => Carbon::parse($booking['arrival']),
                'fecha_checkout' => Carbon::parse($booking['departure']),
                'noches' => Carbon::parse($booking['arrival'])->diffInDays(Carbon::parse($booking['departure'])) ?: null,
                'fecha_reserva' => isset($booking['bookingTime']) ? Carbon::parse($booking['bookingTime']) : null,
                'estado' => ($booking['status'] ?? '') === 'cancelled' ? 'cancelada' : 'confirmada',
                'monto_bruto' => $montoBruto,
                'comision_plataforma' => $comisionPlataforma,
                'comision_coanfitrion' => $comisionCoanfitrion,
                'ingreso_liquido_propietario' => $ingresoLiquido,
                'moneda' => $moneda,
                'origen' => 'beds24',
                'payload_origen' => $booking,
            ]
        );

        return $reserva->wasRecentlyCreated;
    }

    /**
     * Sin filtro de fecha, la API solo devuelve reservas desde "ayer" en adelante.
     * Se pide explícitamente un rango amplio hacia atrás para traer también el historial.
     */
    private function fetchBookings(): array
    {
        $bookings = [];
        $page = 1;

        do {
            $response = Http::withHeaders(['token' => $this->getAccessToken()])
                ->get(config('services.beds24.base_url').'/bookings', [
                    'includeInvoiceItems' => 'true',
                    'arrivalFrom' => now()->subYears(2)->toDateString(),
                    'page' => $page,
                ])
                ->throw();

            $bookings = array_merge($bookings, $response->json('data', []));
            $hayMasPaginas = $response->json('pages.nextPageExists', false);
            $page++;
        } while ($hayMasPaginas);

        return $bookings;
    }

    private function getAccessToken(): string
    {
        return Cache::remember(self::CACHE_KEY_TOKEN, now()->addMinutes(20), function () {
            $response = Http::withHeaders(['refreshToken' => config('services.beds24.refresh_token')])
                ->get(config('services.beds24.base_url').'/authentication/token')
                ->throw();

            return $response->json('token');
        });
    }

    private function resolverPlataforma(?string $referer): ?Plataforma
    {
        if (! $referer) {
            return null;
        }

        $slug = match (true) {
            Str::contains(Str::lower($referer), 'airbnb') => 'airbnb',
            Str::contains(Str::lower($referer), 'booking') => 'booking',
            Str::contains(Str::lower($referer), 'vrbo'), Str::contains(Str::lower($referer), 'expedia') => 'vrbo',
            default => null,
        };

        return $slug ? Plataforma::where('slug', $slug)->first() : null;
    }

    private function resolverDepartamento(?int $propId, ?int $roomId): ?Departamento
    {
        if (! $propId) {
            return null;
        }

        $query = Departamento::withoutGlobalScopes()->where('beds24_prop_id', $propId);

        if ($roomId) {
            $query->where(function ($q) use ($roomId) {
                $q->where('beds24_room_id', $roomId)->orWhereNull('beds24_room_id');
            });
        }

        return $query->first();
    }

    /**
     * Lista las propiedades (y sus room IDs, si existen) de la cuenta Beds24 conectada,
     * para que el administrador pueda copiar los IDs correctos a cada Departamento.
     */
    public function fetchProperties(): array
    {
        $response = Http::withHeaders(['token' => $this->getAccessToken()])
            ->get(config('services.beds24.base_url').'/properties', [
                'includeAllRooms' => 'true',
            ])
            ->throw();

        return $response->json('data', []);
    }

    private function extraerImportes(array $booking): array
    {
        $montoBruto = (float) ($booking['price'] ?? 0);
        $comisionPlataforma = 0.0;
        $moneda = $booking['currency'] ?? 'USD';

        foreach ($booking['invoiceItems'] ?? [] as $item) {
            if (Str::contains(Str::lower($item['type'] ?? ''), 'commission')) {
                $comisionPlataforma += abs((float) ($item['amount'] ?? 0));
            }
        }

        return [$montoBruto, $comisionPlataforma, $moneda];
    }
}
