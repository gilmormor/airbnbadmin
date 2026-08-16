<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Villa o conjunto que agrupa departamentos. Es la unidad de marca del sitio
 * público: cada una tiene su propia página, galería y datos de contacto.
 *
 * Los campos con cast `array` son textos traducibles con una clave por idioma
 * (`['es' => '...', 'en' => '...']`). Usa el helper `texto()` para leerlos.
 */
#[Fillable([
    'nombre', 'slug', 'titular', 'descripcion_corta', 'descripcion_larga',
    'direccion', 'ciudad', 'provincia', 'pais', 'latitud', 'longitud', 'como_llegar',
    'logo_ruta', 'telefono', 'whatsapp', 'email',
    'publicada', 'orden', 'meta_titulo', 'meta_descripcion',
])]
class Edificio extends Model
{
    protected function casts(): array
    {
        return [
            'titular' => 'array',
            'descripcion_corta' => 'array',
            'descripcion_larga' => 'array',
            'como_llegar' => 'array',
            'meta_titulo' => 'array',
            'meta_descripcion' => 'array',
            'latitud' => 'decimal:7',
            'longitud' => 'decimal:7',
            'publicada' => 'boolean',
        ];
    }

    public function departamentos(): HasMany
    {
        return $this->hasMany(Departamento::class);
    }

    public function fotos(): MorphMany
    {
        return $this->morphMany(Foto::class, 'fotable')->orderBy('orden');
    }

    /**
     * Devuelve un campo traducible en el idioma pedido, con reserva al idioma
     * por defecto de la aplicación y luego a la primera traducción disponible.
     */
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
