<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Icono que el navegador muestra en la pestaña.
 *
 * Va en la sucursal y no en la empresa por el mismo motivo que el logo: la marca
 * que ve el huésped es la de la propiedad. Donoma tendrá el suyo.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sucursales', function (Blueprint $table) {
            $table->string('favicon_ruta')->nullable()->after('logo_ruta');
        });
    }

    public function down(): void
    {
        Schema::table('sucursales', function (Blueprint $table) {
            $table->dropColumn('favicon_ruta');
        });
    }
};
