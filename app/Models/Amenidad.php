<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['slug', 'nombre', 'categoria', 'icono', 'orden', 'activa'])]
class Amenidad extends Model
{
    protected $table = 'amenidades';

    protected function casts(): array
    {
        return [
            'nombre' => 'array',
            'activa' => 'boolean',
        ];
    }

    public function departamentos(): BelongsToMany
    {
        return $this->belongsToMany(Departamento::class)->withPivot('destacada');
    }

    public function texto(?string $idioma = null): ?string
    {
        $valores = $this->nombre;

        if (! is_array($valores) || $valores === []) {
            return null;
        }

        return $valores[$idioma ?? app()->getLocale()]
            ?? $valores[config('app.fallback_locale')]
            ?? reset($valores);
    }
}
