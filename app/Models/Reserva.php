<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'departamento_id', 'plataforma_id', 'codigo_externo', 'huesped',
    'fecha_checkin', 'fecha_checkout', 'noches', 'fecha_reserva', 'estado',
    'monto_bruto', 'comision_plataforma', 'tarifa_limpieza', 'comision_coanfitrion', 'ingreso_liquido_propietario',
    'moneda', 'origen', 'payload_origen',
])]
class Reserva extends Model
{
    protected function casts(): array
    {
        return [
            'fecha_checkin' => 'date',
            'fecha_checkout' => 'date',
            'fecha_reserva' => 'date',
            'payload_origen' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope('propietario', function (Builder $builder) {
            if (auth()->check() && auth()->user()->hasRole('Propietario')) {
                $builder->whereHas('departamento', function (Builder $query) {
                    $query->where('propietario_id', auth()->user()->propietario_id);
                });
            }
        });
    }

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class);
    }

    public function plataforma(): BelongsTo
    {
        return $this->belongsTo(Plataforma::class);
    }
}
