<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Datos operativos sensibles del departamento, separados de la ficha pública.
 *
 * Van en su propia tabla por tres motivos: quien tenga acceso a estos campos tiene
 * acceso físico a la propiedad, así que el permiso se restringe aparte; las columnas
 * cifradas no deben mezclarse con las que se consultan y ordenan; y así se puede
 * auditar quién las consulta sin registrar toda la ficha.
 *
 * `clave_puerta` y `wifi_clave` se cifran con el cast `encrypted` del modelo, por lo
 * que necesitan longitud de texto aunque el valor original sea corto.
 *
 * IMPORTANTE: la clave de puerta no se envía en la confirmación de reserva, solo en
 * el mensaje del día de llegada. Hoy las llaves las entrega el conserje en persona,
 * así que estos campos quedan preparados para cuando se instalen cerraduras con código.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accesos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departamento_id')->unique()->constrained('departamentos')->cascadeOnDelete();
            $table->text('clave_puerta')->nullable();
            $table->text('wifi_clave')->nullable();
            $table->string('wifi_red')->nullable();
            $table->text('instrucciones_llegada')->nullable();
            $table->text('ubicacion_llaves')->nullable();
            $table->text('notas_limpieza')->nullable();
            $table->string('conserje_nombre')->nullable();
            $table->string('conserje_telefono')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accesos');
    }
};
