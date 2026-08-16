<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Unidad que se alquila. `nombre` es el identificador interno del panel;
 * `titular` es el título comercial que ve el huésped en el sitio.
 *
 * Los campos con cast `array` son textos traducibles con una clave por idioma.
 * Los datos sensibles de acceso viven en la relación `acceso`, no aquí.
 */
#[Fillable([
    'edificio_id', 'propietario_id', 'nombre', 'slug', 'tipo',
    'titular', 'descripcion_corta', 'descripcion_larga',
    'capacidad_huespedes', 'dormitorios', 'banos_completos', 'banos_medios', 'superficie_m2',
    'precio_base_noche', 'moneda', 'tarifa_limpieza', 'tarifa_lavanderia', 'deposito_seguridad',
    'huespedes_incluidos', 'cargo_huesped_adicional', 'cargo_extra_noche', 'cargo_extra_concepto',
    'descuento_semanal_pct', 'descuento_mensual_pct', 'descuento_directo_pct',
    'noches_minimas', 'noches_maximas', 'antelacion_minima_dias', 'ventana_reserva_meses', 'dias_preparacion',
    'check_in_desde', 'check_in_hasta', 'check_out_hasta',
    'mascotas_permitidas', 'deposito_mascotas', 'mascotas_condiciones',
    'fumar_permitido', 'eventos_permitidos', 'hora_silencio', 'edad_minima', 'reglas_adicionales',
    'publicado', 'orden', 'meta_titulo', 'meta_descripcion',
    'comision_coanfitrion_pct', 'beds24_prop_id', 'beds24_room_id',
])]
class Departamento extends Model
{
    protected function casts(): array
    {
        return [
            'titular' => 'array',
            'descripcion_corta' => 'array',
            'descripcion_larga' => 'array',
            'cargo_extra_concepto' => 'array',
            'mascotas_condiciones' => 'array',
            'reglas_adicionales' => 'array',
            'meta_titulo' => 'array',
            'meta_descripcion' => 'array',
            'superficie_m2' => 'decimal:2',
            'precio_base_noche' => 'decimal:2',
            'tarifa_limpieza' => 'decimal:2',
            'tarifa_lavanderia' => 'decimal:2',
            'deposito_seguridad' => 'decimal:2',
            'cargo_huesped_adicional' => 'decimal:2',
            'cargo_extra_noche' => 'decimal:2',
            'deposito_mascotas' => 'decimal:2',
            'descuento_semanal_pct' => 'decimal:2',
            'descuento_mensual_pct' => 'decimal:2',
            'descuento_directo_pct' => 'decimal:2',
            'mascotas_permitidas' => 'boolean',
            'fumar_permitido' => 'boolean',
            'eventos_permitidos' => 'boolean',
            'publicado' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope('propietario', function (Builder $builder) {
            if (auth()->check() && auth()->user()->hasRole('Propietario')) {
                $builder->where('propietario_id', auth()->user()->propietario_id);
            }
        });
    }

    public function edificio(): BelongsTo
    {
        return $this->belongsTo(Edificio::class);
    }

    public function propietario(): BelongsTo
    {
        return $this->belongsTo(Propietario::class);
    }

    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class);
    }

    public function fotos(): MorphMany
    {
        return $this->morphMany(Foto::class, 'fotable')->orderBy('orden');
    }

    public function camas(): HasMany
    {
        return $this->hasMany(Cama::class)->orderBy('orden');
    }

    public function tarifas(): HasMany
    {
        return $this->hasMany(Tarifa::class);
    }

    public function resenas(): HasMany
    {
        return $this->hasMany(Resena::class);
    }

    public function acceso(): HasOne
    {
        return $this->hasOne(Acceso::class);
    }

    public function amenidades(): BelongsToMany
    {
        return $this->belongsToMany(Amenidad::class)->withPivot('destacada');
    }

    public function servicios(): BelongsToMany
    {
        return $this->belongsToMany(Servicio::class)->withPivot(['precio', 'incluido', 'disponible']);
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

    /**
     * Precio de una noche concreta: gana la tarifa de temporada de mayor prioridad
     * que cubra la fecha; si no hay ninguna, se usa el precio base.
     */
    public function precioParaFecha(\DateTimeInterface $fecha): float
    {
        $tarifa = $this->tarifas
            ->where('activa', true)
            ->filter(fn (Tarifa $t) => $fecha >= $t->fecha_inicio && $fecha <= $t->fecha_fin)
            ->sortByDesc('prioridad')
            ->first();

        return (float) ($tarifa->precio_noche ?? $this->precio_base_noche);
    }
}
