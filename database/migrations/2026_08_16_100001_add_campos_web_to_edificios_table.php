<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Convierte `edificios` en la ficha comercial de la villa que se publica en el sitio.
 *
 * Los campos de texto visibles para el huésped son JSON con una clave por idioma
 * (`{"es": "...", "en": "..."}`). Se eligió JSON en lugar de columnas `_es`/`_en`
 * para poder añadir un tercer idioma sin migrar la tabla.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('edificios', function (Blueprint $table) {
            // Se crea nullable para poder rellenar las filas existentes antes de
            // aplicar el índice único; se endurece al final del método.
            $table->string('slug')->nullable()->after('nombre');
            $table->json('titular')->nullable()->after('slug');
            $table->json('descripcion_corta')->nullable()->after('titular');
            $table->json('descripcion_larga')->nullable()->after('descripcion_corta');

            $table->string('ciudad')->nullable()->after('direccion');
            $table->string('provincia')->nullable()->after('ciudad');
            $table->char('pais', 2)->default('DO')->after('provincia');
            $table->decimal('latitud', 10, 7)->nullable()->after('pais');
            $table->decimal('longitud', 10, 7)->nullable()->after('latitud');
            $table->json('como_llegar')->nullable()->after('longitud');

            $table->string('logo_ruta')->nullable()->after('como_llegar');
            $table->string('telefono')->nullable()->after('logo_ruta');
            $table->string('whatsapp')->nullable()->after('telefono');
            $table->string('email')->nullable()->after('whatsapp');

            $table->boolean('publicada')->default(false)->after('email');
            $table->unsignedSmallInteger('orden')->default(0)->after('publicada');

            $table->json('meta_titulo')->nullable()->after('orden');
            $table->json('meta_descripcion')->nullable()->after('meta_titulo');
        });

        foreach (DB::table('edificios')->select('id', 'nombre')->get() as $edificio) {
            DB::table('edificios')
                ->where('id', $edificio->id)
                ->update(['slug' => Str::slug($edificio->nombre).'-'.$edificio->id]);
        }

        Schema::table('edificios', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('edificios', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn([
                'slug', 'titular', 'descripcion_corta', 'descripcion_larga',
                'ciudad', 'provincia', 'pais', 'latitud', 'longitud', 'como_llegar',
                'logo_ruta', 'telefono', 'whatsapp', 'email',
                'publicada', 'orden', 'meta_titulo', 'meta_descripcion',
            ]);
        });
    }
};
