<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Datos operativos sensibles de un departamento.
 *
 * `clave_puerta` y `wifi_clave` se cifran en base de datos: quien tenga acceso a
 * estos campos tiene acceso físico a la propiedad. No se exponen nunca en el sitio
 * público, y la clave de puerta se envía solo en el mensaje del día de llegada,
 * no en la confirmación de la reserva.
 */
#[Fillable([
    'departamento_id', 'clave_puerta', 'wifi_red', 'wifi_clave',
    'instrucciones_llegada', 'ubicacion_llaves', 'notas_limpieza',
    'conserje_nombre', 'conserje_telefono',
])]
class Acceso extends Model
{
    protected $table = 'accesos';

    protected function casts(): array
    {
        return [
            'clave_puerta' => 'encrypted',
            'wifi_clave' => 'encrypted',
        ];
    }

    protected $hidden = ['clave_puerta', 'wifi_clave'];

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class);
    }
}
