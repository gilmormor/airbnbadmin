<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Reseña de un huésped, usada como prueba social en el sitio.
 *
 * El comentario no se traduce: es una cita textual del huésped. Se guarda el
 * idioma original para poder priorizar las que coinciden con el del visitante.
 */
#[Fillable([
    'departamento_id', 'plataforma_id', 'autor', 'pais', 'calificacion',
    'comentario', 'idioma', 'fecha', 'publicada', 'orden',
])]
class Resena extends Model
{
    protected $table = 'resenas';

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'publicada' => 'boolean',
        ];
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
