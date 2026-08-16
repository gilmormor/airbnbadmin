<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Plantillas de los correos automáticos al huésped.
 *
 * `dias_offset` se cuenta respecto a la fecha de referencia del evento: negativo es
 * antes (-3 = tres días antes del check-in), 0 el mismo día, positivo después.
 *
 * El cuerpo admite variables entre llaves: {huesped}, {departamento}, {villa},
 * {fecha_checkin}, {fecha_checkout}, {noches}, {total}, {clave_puerta}, {wifi_red},
 * {wifi_clave}, {codigo_reserva}.
 *
 * Se llama `plantillas_mensaje` y no `mensajes` porque esa tabla ya existe y guarda
 * la conversación del asistente de IA.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plantillas_mensaje', function (Blueprint $table) {
            $table->id();
            // reserva_confirmada, pago_recibido, pre_llegada, dia_llegada,
            // pre_checkout, post_estancia, reserva_cancelada
            $table->string('clave')->unique();
            $table->string('nombre');
            // checkin, checkout, confirmacion: define desde qué fecha se cuenta el offset
            $table->string('evento_referencia')->default('checkin');
            $table->smallInteger('dias_offset')->default(0);
            $table->time('hora_envio')->default('09:00:00');
            $table->string('canal')->default('email');
            $table->json('asunto');
            $table->json('cuerpo');
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plantillas_mensaje');
    }
};
