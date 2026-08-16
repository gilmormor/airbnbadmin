<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Construcción física dentro de una sucursal: el bloque A, el bloque B.
 *
 * La marca, la ubicación y el contacto viven en la sucursal; aquí solo queda lo
 * que distingue a un edificio de otro dentro de la misma propiedad.
 */
#[Fillable(['sucursal_id', 'nombre', 'pisos', 'orden'])]
class Edificio extends Model
{
    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function departamentos(): HasMany
    {
        return $this->hasMany(Departamento::class);
    }
}
