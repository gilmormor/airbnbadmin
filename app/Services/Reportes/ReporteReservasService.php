<?php

namespace App\Services\Reportes;

use App\Models\Reserva;
use Illuminate\Support\Collection;

class ReporteReservasService
{
    /**
     * @param  array{fecha_desde?: string, fecha_hasta?: string, edificio_id?: int, departamento_id?: int, propietario_id?: int}  $filtros
     */
    public function buscar(array $filtros): Collection
    {
        return Reserva::with(['departamento.edificio', 'departamento.propietario', 'plataforma'])
            ->whereHas('departamento', function ($query) use ($filtros) {
                if (! empty($filtros['edificio_id'])) {
                    $query->where('edificio_id', $filtros['edificio_id']);
                }
                if (! empty($filtros['departamento_id'])) {
                    $query->where('id', $filtros['departamento_id']);
                }
                if (! empty($filtros['propietario_id'])) {
                    $query->where('propietario_id', $filtros['propietario_id']);
                }
            })
            ->when(! empty($filtros['fecha_desde']), fn ($q) => $q->whereDate('fecha_checkin', '>=', $filtros['fecha_desde']))
            ->when(! empty($filtros['fecha_hasta']), fn ($q) => $q->whereDate('fecha_checkin', '<=', $filtros['fecha_hasta']))
            ->orderBy('fecha_checkin')
            ->get();
    }

    /**
     * Agrupa las reservas por departamento con subtotales, más un total general.
     */
    public function resumen(Collection $reservas): array
    {
        $porDepartamento = $reservas->groupBy(fn (Reserva $r) => $r->departamento_id)->map(function (Collection $grupo) {
            return [
                'departamento' => $grupo->first()->departamento,
                'reservas' => $grupo,
                'monto_bruto' => $grupo->sum('monto_bruto'),
                'comision_plataforma' => $grupo->sum('comision_plataforma'),
                'tarifa_limpieza' => $grupo->sum('tarifa_limpieza'),
                'comision_coanfitrion' => $grupo->sum('comision_coanfitrion'),
                'ingreso_liquido_propietario' => $grupo->sum('ingreso_liquido_propietario'),
            ];
        })->values();

        $totales = [
            'monto_bruto' => $reservas->sum('monto_bruto'),
            'comision_plataforma' => $reservas->sum('comision_plataforma'),
            'tarifa_limpieza' => $reservas->sum('tarifa_limpieza'),
            'comision_coanfitrion' => $reservas->sum('comision_coanfitrion'),
            'ingreso_liquido_propietario' => $reservas->sum('ingreso_liquido_propietario'),
        ];

        return [$porDepartamento, $totales];
    }
}
