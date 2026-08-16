<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Servicios que el huésped agrega a su reserva como ítems del carrito: chef,
 * carrito de golf, cuatrimoto, traslado.
 *
 * `tipo_cobro` define cómo se multiplica el precio al calcular el total.
 * La tabla pivote permite que un servicio esté disponible solo en ciertas unidades,
 * con precio propio, o incluido sin costo: el desayuno de chef es cortesía en las
 * unidades de Riberamar pero no en las demás propiedades.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servicios', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('nombre');
            $table->json('descripcion')->nullable();
            $table->decimal('precio', 10, 2)->default(0);
            $table->char('moneda', 3)->default('USD');
            // por_reserva, por_noche, por_huesped, por_comida
            $table->string('tipo_cobro')->default('por_reserva');
            $table->string('icono')->nullable();
            $table->boolean('activo')->default(true);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
        });

        Schema::create('departamento_servicio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departamento_id')->constrained('departamentos')->cascadeOnDelete();
            $table->foreignId('servicio_id')->constrained('servicios')->cascadeOnDelete();
            // Sobrescribe `servicios.precio` solo para esta unidad.
            $table->decimal('precio', 10, 2)->nullable();
            $table->boolean('incluido')->default(false);
            $table->boolean('disponible')->default(true);

            $table->unique(['departamento_id', 'servicio_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departamento_servicio');
        Schema::dropIfExists('servicios');
    }
};
