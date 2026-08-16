<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Secciones de texto libre que se muestran bajo la foto de portada, tanto en la
 * ficha de un departamento como en la página de una villa.
 *
 * Son repetibles y no campos fijos porque el sitio actual usa tres por unidad y
 * el equipo de marketing necesita poder agregar o quitar secciones sin tocar código.
 * Cada una repite la misma forma: un antetítulo pequeño, un titular grande y un
 * párrafo. Por ejemplo: «Our Facility» / «Best place to enjoy your life» / texto.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bloques_contenido', function (Blueprint $table) {
            $table->id();
            $table->morphs('bloqueable');
            $table->json('antetitulo')->nullable();
            $table->json('titulo')->nullable();
            $table->json('cuerpo')->nullable();
            $table->unsignedSmallInteger('orden')->default(0);
            $table->boolean('publicado')->default(true);
            $table->timestamps();

            $table->index(['bloqueable_type', 'bloqueable_id', 'orden'], 'bloques_bloqueable_orden_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bloques_contenido');
    }
};
