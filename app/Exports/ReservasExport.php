<?php

namespace App\Exports;

use App\Models\Reserva;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReservasExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private Collection $reservas) {}

    public function collection(): Collection
    {
        return $this->reservas;
    }

    public function headings(): array
    {
        return [
            'Edificio', 'Departamento', 'Propietario', 'Plataforma', 'Código', 'Huésped',
            'Check-in', 'Check-out', 'Noches', 'Estado', 'Monto bruto',
            'Comisión plataforma', 'Comisión coanfitrión', 'Ingreso líquido propietario', 'Moneda',
        ];
    }

    public function map($reserva): array
    {
        /** @var Reserva $reserva */
        return [
            $reserva->departamento->edificio->nombre,
            $reserva->departamento->nombre,
            $reserva->departamento->propietario->nombre,
            $reserva->plataforma->nombre,
            $reserva->codigo_externo,
            $reserva->huesped,
            $reserva->fecha_checkin->format('d/m/Y'),
            $reserva->fecha_checkout->format('d/m/Y'),
            $reserva->noches,
            $reserva->estado,
            $reserva->monto_bruto,
            $reserva->comision_plataforma,
            $reserva->comision_coanfitrion,
            $reserva->ingreso_liquido_propietario,
            $reserva->moneda,
        ];
    }
}
