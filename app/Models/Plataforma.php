<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nombre', 'slug'])]
class Plataforma extends Model
{
    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class);
    }
}
