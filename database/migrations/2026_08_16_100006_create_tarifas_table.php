<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tarifas por rango de fechas: temporada alta, Semana Santa, o la promoción puntual
 * de una semana floja que se mencionó en la reunión.
 *
 * Si dos rangos se solapan gana el de mayor `prioridad`. Cuando ninguna tarifa cubre
 * la fecha, se usa `departamentos.precio_base_noche`.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarifas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departamento_id')->constrained('departamentos')->cascadeOnDelete();
            $table->string('nombre');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->decimal('precio_noche', 10, 2);
            // Permite exigir más noches en temporada alta que el mínimo habitual.
            $table->unsignedSmallInteger('noches_minimas')->nullable();
            $table->unsignedSmallInteger('prioridad')->default(0);
            $table->boolean('activa')->default(true);
            $table->timestamps();

            $table->index(['departamento_id', 'fecha_inicio', 'fecha_fin'], 'tarifas_depto_rango_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarifas');
    }
};
