<?php

namespace App\Services\Import;

use App\Models\Departamento;
use App\Models\Importacion;
use App\Models\Plataforma;
use App\Models\Reserva;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class CsvReservaImporter
{
    /**
     * Campos internos requeridos para poder procesar una fila.
     */
    private const CAMPOS_REQUERIDOS = ['codigo_externo', 'fecha_checkin', 'fecha_checkout', 'monto_bruto'];

    public function import(UploadedFile $file, Plataforma $plataforma, ?Departamento $departamentoDefault, User $usuario): Importacion
    {
        $mapeoConfig = config("reservas.mapeos.{$plataforma->slug}", []);

        $handle = fopen($file->getRealPath(), 'r');
        $encabezados = fgetcsv($handle);

        if ($encabezados === false) {
            fclose($handle);

            return $this->registrarImportacion($plataforma, $usuario, 0, 0, 0, 0, [
                ['fila' => 0, 'error' => 'El archivo está vacío o no se pudo leer.'],
            ]);
        }

        $encabezados = array_map(fn ($h) => trim((string) $h), $encabezados);
        $indices = $this->resolverIndices($encabezados, $mapeoConfig);

        $faltantes = array_diff(self::CAMPOS_REQUERIDOS, array_keys($indices));
        if (! empty($faltantes)) {
            fclose($handle);

            return $this->registrarImportacion($plataforma, $usuario, 0, 0, 0, 0, [
                ['fila' => 0, 'error' => 'No se encontraron las columnas requeridas: '.implode(', ', $faltantes)],
            ]);
        }

        $totalFilas = 0;
        $creadas = 0;
        $actualizadas = 0;
        $errores = 0;
        $detalleErrores = [];

        while (($fila = fgetcsv($handle)) !== false) {
            $datos = $this->mapearFila($fila, $encabezados, $indices);

            // Filas sin código de confirmación no son reservas (ej. líneas de tipo "Payout"
            // en el historial de transacciones de Airbnb) — se ignoran silenciosamente.
            if (empty($datos['codigo_externo'])) {
                continue;
            }

            $totalFilas++;

            try {
                $departamento = $this->resolverDepartamento($datos['listado'] ?? null, $departamentoDefault);

                if (! $departamento) {
                    throw new \RuntimeException('No se pudo determinar el departamento (verifique el nombre del anuncio o seleccione un departamento por defecto).');
                }

                $montoBruto = $this->parseMonto($datos['monto_bruto'] ?? '0');
                $comisionPlataforma = $this->parseMonto($datos['comision_plataforma'] ?? '0');
                $pctCoanfitrion = (float) ($departamento->comision_coanfitrion_pct ?? 0);
                $comisionCoanfitrion = round($montoBruto * $pctCoanfitrion / 100, 2);
                $ingresoLiquido = $montoBruto - $comisionPlataforma - $comisionCoanfitrion;

                $fechaCheckin = $this->parseFecha($datos['fecha_checkin'] ?? null);
                $fechaCheckout = $this->parseFecha($datos['fecha_checkout'] ?? null);

                if (! $fechaCheckin || ! $fechaCheckout) {
                    throw new \RuntimeException('Fechas de check-in/check-out inválidas.');
                }

                $reserva = Reserva::withoutGlobalScopes()->updateOrCreate(
                    [
                        'plataforma_id' => $plataforma->id,
                        'codigo_externo' => $datos['codigo_externo'],
                    ],
                    [
                        'departamento_id' => $departamento->id,
                        'huesped' => $datos['huesped'] ?? null,
                        'fecha_checkin' => $fechaCheckin,
                        'fecha_checkout' => $fechaCheckout,
                        'noches' => $fechaCheckin->diffInDays($fechaCheckout) ?: null,
                        'fecha_reserva' => $this->parseFecha($datos['fecha_reserva'] ?? null),
                        'estado' => 'confirmada',
                        'monto_bruto' => $montoBruto,
                        'comision_plataforma' => $comisionPlataforma,
                        'comision_coanfitrion' => $comisionCoanfitrion,
                        'ingreso_liquido_propietario' => $ingresoLiquido,
                        'moneda' => $datos['moneda'] ?? 'USD',
                        'origen' => 'csv_'.$plataforma->slug,
                        'payload_origen' => array_combine($encabezados, array_pad($fila, count($encabezados), null)),
                    ]
                );

                $reserva->wasRecentlyCreated ? $creadas++ : $actualizadas++;
            } catch (\Throwable $e) {
                $errores++;
                $detalleErrores[] = ['fila' => $totalFilas + 1, 'error' => $e->getMessage()];
            }
        }

        fclose($handle);

        return $this->registrarImportacion($plataforma, $usuario, $totalFilas, $creadas, $actualizadas, $errores, $detalleErrores);
    }

    private function resolverIndices(array $encabezados, array $mapeoConfig): array
    {
        $indices = [];

        foreach ($mapeoConfig as $campo => $alias) {
            foreach ($alias as $nombreColumna) {
                $pos = array_search(Str::lower($nombreColumna), array_map(Str::lower(...), $encabezados), true);
                if ($pos !== false) {
                    $indices[$campo] = $pos;
                    break;
                }
            }
        }

        return $indices;
    }

    private function mapearFila(array $fila, array $encabezados, array $indices): array
    {
        $datos = [];
        foreach ($indices as $campo => $pos) {
            $datos[$campo] = trim((string) ($fila[$pos] ?? ''));
        }

        return $datos;
    }

    private function resolverDepartamento(?string $listado, ?Departamento $default): ?Departamento
    {
        if ($listado) {
            $match = Departamento::withoutGlobalScopes()
                ->get()
                ->first(fn (Departamento $d) => Str::contains(Str::lower($d->nombre), Str::lower($listado))
                    || Str::contains(Str::lower($listado), Str::lower($d->nombre)));

            if ($match) {
                return $match;
            }
        }

        return $default;
    }

    private function parseMonto(string $valor): float
    {
        $limpio = preg_replace('/[^0-9.\-]/', '', str_replace(',', '', $valor));

        return (float) $limpio;
    }

    private function parseFecha(?string $valor): ?Carbon
    {
        if (! $valor) {
            return null;
        }

        try {
            return Carbon::parse($valor);
        } catch (\Throwable) {
            return null;
        }
    }

    private function registrarImportacion(
        Plataforma $plataforma,
        User $usuario,
        int $totalFilas,
        int $creadas,
        int $actualizadas,
        int $errores,
        array $detalleErrores
    ): Importacion {
        return Importacion::create([
            'tipo' => 'csv',
            'origen' => $plataforma->slug,
            'usuario_id' => $usuario->id,
            'total_filas' => $totalFilas,
            'total_creadas' => $creadas,
            'total_actualizadas' => $actualizadas,
            'total_error' => $errores,
            'detalle_errores' => $detalleErrores,
        ]);
    }
}
