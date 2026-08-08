<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nombre', 'direccion'])]
class Edificio extends Model
{
    public function departamentos(): HasMany
    {
        return $this->hasMany(Departamento::class);
    }
}
