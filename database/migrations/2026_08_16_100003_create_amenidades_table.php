<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Catálogo de amenidades, en lugar de texto libre, para poder filtrar el buscador
 * por criterios como "con piscina privada" o "con jacuzzi".
 *
 * `destacada` en la tabla pivote marca las que se muestran arriba en la ficha,
 * antes de desplegar la lista completa.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('amenidades', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('nombre');
            // cocina, bano, exterior, entretenimiento, seguridad, climatizacion, servicios
            $table->string('categoria')->default('servicios');
            $table->string('icono')->nullable();
            $table->unsignedSmallInteger('orden')->default(0);
            $table->boolean('activa')->default(true);
            $table->timestamps();

            $table->index('categoria');
        });

        Schema::create('amenidad_departamento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('amenidad_id')->constrained('amenidades')->cascadeOnDelete();
            $table->foreignId('departamento_id')->constrained('departamentos')->cascadeOnDelete();
            $table->boolean('destacada')->default(false);

            $table->unique(['amenidad_id', 'departamento_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('amenidad_departamento');
        Schema::dropIfExists('amenidades');
    }
};
