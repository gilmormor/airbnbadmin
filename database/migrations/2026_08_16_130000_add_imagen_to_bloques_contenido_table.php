<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cada sección se acompaña de una foto que se muestra al costado del texto,
 * alternando de lado en lado, como en el sitio actual.
 *
 * La imagen vive aquí y no en la tabla `fotos` porque no forma parte de la galería:
 * no se muestra en el carrusel ni en el visor, y borrarla no debe alterar el orden
 * de las demás.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bloques_contenido', function (Blueprint $table) {
            $table->string('imagen_ruta')->nullable()->after('cuerpo');
            $table->json('imagen_alt')->nullable()->after('imagen_ruta');
        });
    }

    public function down(): void
    {
        Schema::table('bloques_contenido', function (Blueprint $table) {
            $table->dropColumn(['imagen_ruta', 'imagen_alt']);
        });
    }
};
