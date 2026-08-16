<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

/**
 * Sección de texto que se muestra bajo la foto de portada. Repite la forma
 * antetítulo / titular / cuerpo, y se ordena por arrastre desde el panel.
 */
#[Fillable([
    'bloqueable_type', 'bloqueable_id', 'antetitulo', 'titulo', 'cuerpo',
    'imagen_ruta', 'imagen_alt', 'items', 'orden', 'publicado',
])]
class BloqueContenido extends Model
{
    protected $table = 'bloques_contenido';

    protected function casts(): array
    {
        return [
            'antetitulo' => 'array',
            'titulo' => 'array',
            'cuerpo' => 'array',
            'imagen_alt' => 'array',
            'items' => 'array',
            'publicado' => 'boolean',
        ];
    }

    /**
     * Las líneas de la lista numerada, ya resueltas al idioma activo y sin las
     * que quedaron vacías en ese idioma.
     */
    public function itemsTraducidos(?string $idioma = null): array
    {
        $idioma ??= app()->getLocale();
        $respaldo = config('app.fallback_locale');

        return collect($this->items ?? [])
            ->map(fn ($item) => $item[$idioma] ?? $item[$respaldo] ?? reset($item) ?: null)
            ->filter()
            ->values()
            ->all();
    }

    public function imagenUrl(): ?string
    {
        return $this->imagen_ruta
            ? Storage::disk('public')->url($this->imagen_ruta)
            : null;
    }

    public function bloqueable(): MorphTo
    {
        return $this->morphTo();
    }

    public function texto(string $campo, ?string $idioma = null): ?string
    {
        $valores = $this->{$campo};

        if (! is_array($valores) || $valores === []) {
            return null;
        }

        return $valores[$idioma ?? app()->getLocale()]
            ?? $valores[config('app.fallback_locale')]
            ?? reset($valores);
    }
}
