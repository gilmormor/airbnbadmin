<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

/**
 * Galería polimórfica compartida por villas y departamentos.
 */
#[Fillable(['fotable_type', 'fotable_id', 'ruta', 'alt', 'titulo', 'categoria', 'portada', 'orden', 'ancho', 'alto'])]
class Foto extends Model
{
    public const CATEGORIAS = [
        'dormitorio' => 'Dormitorio',
        'bano' => 'Baño',
        'cocina' => 'Cocina',
        'sala' => 'Sala',
        'comedor' => 'Comedor',
        'exterior' => 'Exterior',
        'piscina' => 'Piscina',
        'jacuzzi' => 'Jacuzzi',
        'vista' => 'Vista',
        'plano' => 'Plano',
    ];

    protected function casts(): array
    {
        return [
            'alt' => 'array',
            'titulo' => 'array',
            'portada' => 'boolean',
        ];
    }

    public function fotable(): MorphTo
    {
        return $this->morphTo();
    }

    public function url(): string
    {
        return Storage::disk('public')->url($this->ruta);
    }
}
