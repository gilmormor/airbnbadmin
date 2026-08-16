<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sucursal: la propiedad tal como la conoce el huésped, con su marca, su ubicación
 * y su contacto. Villa Riberamar es una; Donoma sería otra.
 *
 * Recibe los campos que antes vivían en `edificios`, que pasa a representar las
 * construcciones físicas dentro de la sucursal.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sucursales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->restrictOnDelete();

            $table->string('nombre');
            $table->string('slug')->unique();
            $table->json('titular')->nullable();
            $table->json('descripcion_corta')->nullable();
            $table->json('descripcion_larga')->nullable();

            $table->string('direccion')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('provincia')->nullable();
            $table->char('pais', 2)->default('DO');
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
            $table->json('como_llegar')->nullable();

            $table->string('logo_ruta')->nullable();
            $table->string('telefono')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();

            $table->boolean('publicada')->default(false);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->json('meta_titulo')->nullable();
            $table->json('meta_descripcion')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sucursales');
    }
};
