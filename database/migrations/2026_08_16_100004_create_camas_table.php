<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Distribución de camas por ambiente, modelada aparte porque el huésped decide con
 * ella: no es lo mismo "6 huéspedes en 2 dormitorios" con tres camas dobles que con
 * una doble y cuatro individuales.
 *
 * Ejemplo: (Dormitorio 1, king, 1), (Dormitorio 2, individual, 2), (Sala, sofá cama, 1).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('camas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departamento_id')->constrained('departamentos')->cascadeOnDelete();
            $table->json('ambiente');
            // king, queen, matrimonial, individual, sofa_cama, litera, cuna
            $table->string('tipo');
            $table->unsignedTinyInteger('cantidad')->default(1);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();

            $table->index('departamento_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('camas');
    }
};
