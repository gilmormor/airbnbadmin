<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Galería polimórfica: sirve tanto para la villa como para cada departamento,
 * sin duplicar tabla ni lógica de ordenamiento.
 *
 * `orden` se maneja con arrastre en el panel (ya hay SortableJS en el proyecto,
 * usado para el orden de menús).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fotos', function (Blueprint $table) {
            $table->id();
            $table->morphs('fotable');
            $table->string('ruta');
            $table->json('alt')->nullable();
            $table->json('titulo')->nullable();
            // dormitorio, bano, cocina, sala, exterior, piscina, vista, plano
            $table->string('categoria')->nullable();
            $table->boolean('portada')->default(false);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->unsignedInteger('ancho')->nullable();
            $table->unsignedInteger('alto')->nullable();
            $table->timestamps();

            $table->index(['fotable_type', 'fotable_id', 'orden'], 'fotos_fotable_orden_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fotos');
    }
};
