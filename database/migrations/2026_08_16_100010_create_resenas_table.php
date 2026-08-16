<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Reseñas de huéspedes que se muestran como prueba social en el sitio.
 *
 * El comentario es texto plano y no JSON por idioma: una reseña es una cita textual
 * del huésped y traducirla la falsearía. Se guarda el idioma en que fue escrita para
 * poder priorizar las que coinciden con el idioma que está viendo el visitante.
 *
 * Las seis reseñas del sitio actual de WordPress están en
 * database/contenido-inicial/paginas/inicio.md
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resenas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departamento_id')->nullable()->constrained('departamentos')->nullOnDelete();
            $table->foreignId('plataforma_id')->nullable()->constrained('plataformas')->nullOnDelete();
            $table->string('autor');
            $table->string('pais')->nullable();
            $table->unsignedTinyInteger('calificacion')->nullable();
            $table->text('comentario');
            $table->char('idioma', 2)->default('en');
            $table->date('fecha')->nullable();
            $table->boolean('publicada')->default(true);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();

            $table->index(['publicada', 'orden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resenas');
    }
};
