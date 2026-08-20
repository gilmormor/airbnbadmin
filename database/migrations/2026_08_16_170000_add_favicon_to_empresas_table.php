<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Icono de pestaña a nivel de empresa.
 *
 * Actúa como respaldo del de la sucursal: si una propiedad nueva todavía no tiene
 * el suyo cargado, el navegador muestra este en lugar de quedarse sin icono.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->string('favicon_ruta')->nullable()->after('sitio_web');
        });
    }

    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn('favicon_ruta');
        });
    }
};
