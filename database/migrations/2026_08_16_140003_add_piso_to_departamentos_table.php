<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Piso en el que se encuentra el departamento dentro de su edificio.
 *
 * Es dato útil para el huésped (escaleras, ascensor, vistas) y para la operación
 * de limpieza. Se admite 0 para la planta baja.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('departamentos', function (Blueprint $table) {
            $table->unsignedSmallInteger('piso')->nullable()->after('tipo');
        });
    }

    public function down(): void
    {
        Schema::table('departamentos', function (Blueprint $table) {
            $table->dropColumn('piso');
        });
    }
};
