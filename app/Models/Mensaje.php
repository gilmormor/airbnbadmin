<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['conversacion_id', 'rol', 'contenido', 'metadata'])]
class Mensaje extends Model
{
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function conversacion(): BelongsTo
    {
        return $this->belongsTo(Conversacion::class);
    }
}
