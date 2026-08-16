<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Datos de la empresa que opera las sucursales.
 *
 * Se espera una sola fila por instalación: cada despliegue atiende a una empresa
 * y tiene su propia base de datos. Por eso en el panel se administra como una
 * pantalla de ajustes y no como un listado.
 *
 * `identificacion_fiscal` es genérica a propósito: en República Dominicana es el
 * RNC y en Chile el RUT, y el sistema corre en ambos países.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('razon_social');
            $table->string('nombre_comercial')->nullable();
            $table->string('identificacion_fiscal')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('direccion')->nullable();
            $table->string('ciudad')->nullable();
            $table->char('pais', 2)->default('DO');
            $table->string('sitio_web')->nullable();
            // Los datos fiscales se muestran en el pie del sitio y se usarán en
            // las facturas cuando exista el módulo de cobro.
            $table->boolean('mostrar_en_pie')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
