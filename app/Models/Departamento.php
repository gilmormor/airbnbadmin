<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['edificio_id', 'propietario_id', 'nombre', 'comision_coanfitrion_pct', 'beds24_prop_id', 'beds24_room_id'])]
class Departamento extends Model
{
    protected static function booted(): void
    {
        static::addGlobalScope('propietario', function (Builder $builder) {
            if (auth()->check() && auth()->user()->hasRole('Propietario')) {
                $builder->where('propietario_id', auth()->user()->propietario_id);
            }
        });
    }

    public function edificio(): BelongsTo
    {
        return $this->belongsTo(Edificio::class);
    }

    public function propietario(): BelongsTo
    {
        return $this->belongsTo(Propietario::class);
    }

    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class);
    }
}
