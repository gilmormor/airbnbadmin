<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * Plantilla de un correo automático al huésped.
 *
 * `dias_offset` se cuenta desde `evento_referencia`: negativo es antes
 * (-3 = tres días antes del check-in), 0 el mismo día, positivo después.
 */
#[Fillable(['clave', 'nombre', 'evento_referencia', 'dias_offset', 'hora_envio', 'canal', 'asunto', 'cuerpo', 'activa'])]
class PlantillaMensaje extends Model
{
    protected $table = 'plantillas_mensaje';

    /** Variables que se sustituyen al componer el mensaje. */
    public const VARIABLES = [
        '{huesped}', '{villa}', '{departamento}', '{codigo_reserva}',
        '{fecha_checkin}', '{fecha_checkout}', '{noches}', '{huespedes}',
        '{total}', '{moneda}', '{check_in_desde}', '{check_out_hasta}',
        '{clave_puerta}', '{wifi_red}', '{wifi_clave}',
    ];

    protected function casts(): array
    {
        return [
            'asunto' => 'array',
            'cuerpo' => 'array',
            'activa' => 'boolean',
        ];
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
