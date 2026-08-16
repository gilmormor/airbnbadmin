<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Precio de un departamento durante un rango de fechas. Si dos rangos se solapan,
 * gana el de mayor `prioridad`.
 */
#[Fillable(['departamento_id', 'nombre', 'fecha_inicio', 'fecha_fin', 'precio_noche', 'noches_minimas', 'prioridad', 'activa'])]
class Tarifa extends Model
{
    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
            'precio_noche' => 'decimal:2',
            'activa' => 'boolean',
        ];
    }

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class);
    }
}
