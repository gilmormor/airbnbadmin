<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Servicio que el huésped agrega a su reserva como ítem del carrito.
 */
#[Fillable(['slug', 'nombre', 'descripcion', 'precio', 'moneda', 'tipo_cobro', 'icono', 'activo', 'orden'])]
class Servicio extends Model
{
    public const TIPOS_COBRO = [
        'por_reserva' => 'Una vez por reserva',
        'por_noche' => 'Por noche',
        'por_huesped' => 'Por huésped',
        'por_comida' => 'Por comida',
    ];

    protected function casts(): array
    {
        return [
            'nombre' => 'array',
            'descripcion' => 'array',
            'precio' => 'decimal:2',
            'activo' => 'boolean',
        ];
    }

    public function departamentos(): BelongsToMany
    {
        return $this->belongsToMany(Departamento::class)->withPivot(['precio', 'incluido', 'disponible']);
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
