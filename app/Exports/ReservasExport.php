<?php

namespace App\Exports;

use App\Models\Reserva;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReservasExport implements FromCollection, WithColumnFormatting, WithHeadings, WithMapping
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
            'Fecha de reserva', 'Check-in', 'Check-out', 'Noches', 'Estado', 'Monto bruto',
            'Comisión plataforma', 'Tarifas', 'Comisión coanfitrión', 'Ingreso líquido propietario', 'Moneda',
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
            $reserva->fecha_reserva?->format('d/m/Y'),
            $reserva->fecha_checkin->format('d/m/Y'),
            $reserva->fecha_checkout->format('d/m/Y'),
            $reserva->noches,
            $reserva->estado,
            $reserva->monto_bruto,
            $reserva->comision_plataforma,
            $reserva->tarifa_limpieza,
            $reserva->comision_coanfitrion,
            $reserva->ingreso_liquido_propietario,
            $reserva->moneda,
        ];
    }

    /**
     * Formato numérico sin decimales; el separador de miles lo define el idioma/región
     * configurado en Excel de quien abre el archivo (en español de Chile se ve con punto).
     */
    public function columnFormats(): array
    {
        return [
            'L' => '#,##0',
            'M' => '#,##0',
            'N' => '#,##0',
            'O' => '#,##0',
            'P' => '#,##0',
        ];
    }
}
