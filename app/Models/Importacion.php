<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('importaciones')]
#[Fillable([
    'tipo', 'origen', 'usuario_id', 'total_filas',
    'total_creadas', 'total_actualizadas', 'total_error', 'detalle_errores',
])]
class Importacion extends Model
{
    protected function casts(): array
    {
        return [
            'detalle_errores' => 'array',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
