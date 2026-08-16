<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Empresa que opera las sucursales.
 *
 * Se espera una sola fila por instalación, así que el panel la administra como
 * una pantalla de ajustes. Usa `actual()` para obtenerla en lugar de asumir el id 1.
 */
#[Fillable([
    'razon_social', 'nombre_comercial', 'identificacion_fiscal',
    'telefono', 'email', 'direccion', 'ciudad', 'pais', 'sitio_web', 'mostrar_en_pie',
])]
class Empresa extends Model
{
    protected function casts(): array
    {
        return ['mostrar_en_pie' => 'boolean'];
    }

    public function sucursales(): HasMany
    {
        return $this->hasMany(Sucursal::class);
    }

    /**
     * La empresa de esta instalación. Se crea vacía si aún no existe, para que el
     * panel siempre tenga un registro que editar.
     */
    public static function actual(): self
    {
        return static::firstOrCreate([], ['razon_social' => config('app.name')]);
    }

    public function nombreVisible(): string
    {
        return $this->nombre_comercial ?: $this->razon_social;
    }
}
