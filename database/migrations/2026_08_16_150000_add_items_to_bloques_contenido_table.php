<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lista numerada opcional dentro de una sección.
 *
 * El sitio actual la usa para resumir lo que incluye la reserva con frases
 * curadas («2 dormitorios (6 huéspedes), aire acondicionado»), que no son las
 * amenidades del catálogo: aquellas sirven para filtrar el buscador, estas para
 * vender. Por eso son texto libre y no una relación.
 *
 * Se guarda como JSON y no como tabla aparte porque son pocas por sección, su
 * orden es el del array, y siempre se leen junto con la sección que las contiene.
 * Formato: [{"es": "...", "en": "..."}, ...]
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bloques_contenido', function (Blueprint $table) {
            $table->json('items')->nullable()->after('imagen_alt');
        });
    }

    public function down(): void
    {
        Schema::table('bloques_contenido', function (Blueprint $table) {
            $table->dropColumn('items');
        });
    }
};
