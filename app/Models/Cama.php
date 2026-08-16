<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['departamento_id', 'ambiente', 'tipo', 'cantidad', 'orden'])]
class Cama extends Model
{
    public const TIPOS = [
        'king' => 'King',
        'queen' => 'Queen',
        'matrimonial' => 'Matrimonial',
        'individual' => 'Individual',
        'sofa_cama' => 'Sofá cama',
        'litera' => 'Litera',
        'cuna' => 'Cuna',
    ];

    protected function casts(): array
    {
        return ['ambiente' => 'array'];
    }

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class);
    }
}
