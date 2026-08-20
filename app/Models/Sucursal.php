<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;

/**
 * La propiedad tal como la conoce el huésped: su marca, su ubicación y su contacto.
 * Villa Riberamar es una sucursal; Donoma sería otra.
 *
 * Los campos con cast `array` son textos traducibles con una clave por idioma
 * (`['es' => '...', 'en' => '...']`). Se leen con `texto()`.
 */
#[Fillable([
    'empresa_id', 'nombre', 'slug', 'titular', 'descripcion_corta', 'descripcion_larga',
    'direccion', 'ciudad', 'provincia', 'pais', 'latitud', 'longitud', 'como_llegar',
    'logo_ruta', 'favicon_ruta', 'telefono', 'whatsapp', 'email',
    'publicada', 'orden', 'meta_titulo', 'meta_descripcion',
])]
class Sucursal extends Model
{
    protected $table = 'sucursales';

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

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function edificios(): HasMany
    {
        return $this->hasMany(Edificio::class)->orderBy('orden');
    }

    /** Los departamentos cuelgan de la sucursal a través de su edificio. */
    public function departamentos(): HasManyThrough
    {
        return $this->hasManyThrough(Departamento::class, Edificio::class);
    }

    public function fotos(): MorphMany
    {
        return $this->morphMany(Foto::class, 'fotable')->orderBy('orden');
    }

    public function bloques(): MorphMany
    {
        return $this->morphMany(BloqueContenido::class, 'bloqueable')->orderBy('orden');
    }

    public function logoUrl(): ?string
    {
        return $this->logo_ruta
            ? Storage::disk('public')->url($this->logo_ruta)
            : null;
    }

    /**
     * Icono de pestaña de esta sucursal, con respaldo en el de la empresa: una
     * propiedad recién creada no debería quedarse sin icono mientras nadie le
     * carga el suyo.
     */
    public function faviconUrl(): ?string
    {
        if ($this->favicon_ruta) {
            return Storage::disk('public')->url($this->favicon_ruta);
        }

        return $this->empresa?->faviconUrl();
    }

    /** Número de WhatsApp en el formato que espera wa.me: solo dígitos. */
    public function whatsappDigitos(): ?string
    {
        return $this->whatsapp ? preg_replace('/\D/', '', $this->whatsapp) : null;
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
