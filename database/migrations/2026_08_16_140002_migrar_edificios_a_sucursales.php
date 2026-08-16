<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Convierte cada edificio existente en una sucursal y deja al edificio como la
 * construcción física que agrupa departamentos dentro de ella.
 *
 * La conversión es uno a uno para no perder información: cada edificio conserva
 * su nombre y queda colgando de la sucursal creada a partir de sus propios datos.
 * Reorganizarlos después (por ejemplo, separar «Bloque A» de «Bloque B» dentro de
 * una misma sucursal) es trabajo de contenido, no de migración.
 *
 * Las fotos y las secciones que apuntaban al edificio se reasignan a la sucursal,
 * porque son material de marca y no de la construcción.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('edificios', function (Blueprint $table) {
            $table->foreignId('sucursal_id')->nullable()->after('id')
                ->constrained('sucursales')->restrictOnDelete();
            $table->unsignedSmallInteger('pisos')->nullable()->after('nombre');
        });

        $empresaId = DB::table('empresas')->insertGetId([
            'razon_social' => config('app.name'),
            'nombre_comercial' => config('app.name'),
            'pais' => 'DO',
            'mostrar_en_pie' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach (DB::table('edificios')->orderBy('id')->get() as $edificio) {
            $sucursalId = DB::table('sucursales')->insertGetId([
                'empresa_id' => $empresaId,
                'nombre' => $edificio->nombre,
                'slug' => $edificio->slug,
                'titular' => $edificio->titular,
                'descripcion_corta' => $edificio->descripcion_corta,
                'descripcion_larga' => $edificio->descripcion_larga,
                'direccion' => $edificio->direccion,
                'ciudad' => $edificio->ciudad,
                'provincia' => $edificio->provincia,
                'pais' => $edificio->pais ?? 'DO',
                'latitud' => $edificio->latitud,
                'longitud' => $edificio->longitud,
                'como_llegar' => $edificio->como_llegar,
                'logo_ruta' => $edificio->logo_ruta,
                'telefono' => $edificio->telefono,
                'whatsapp' => $edificio->whatsapp,
                'email' => $edificio->email,
                'publicada' => $edificio->publicada,
                'orden' => $edificio->orden,
                'meta_titulo' => $edificio->meta_titulo,
                'meta_descripcion' => $edificio->meta_descripcion,
                'created_at' => $edificio->created_at,
                'updated_at' => now(),
            ]);

            DB::table('edificios')->where('id', $edificio->id)->update(['sucursal_id' => $sucursalId]);

            DB::table('fotos')
                ->where('fotable_type', 'edificio')->where('fotable_id', $edificio->id)
                ->update(['fotable_type' => 'sucursal', 'fotable_id' => $sucursalId]);

            DB::table('bloques_contenido')
                ->where('bloqueable_type', 'edificio')->where('bloqueable_id', $edificio->id)
                ->update(['bloqueable_type' => 'sucursal', 'bloqueable_id' => $sucursalId]);
        }

        Schema::table('edificios', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn([
                'slug', 'titular', 'descripcion_corta', 'descripcion_larga',
                'direccion', 'ciudad', 'provincia', 'pais', 'latitud', 'longitud', 'como_llegar',
                'logo_ruta', 'telefono', 'whatsapp', 'email',
                'publicada', 'meta_titulo', 'meta_descripcion',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('edificios', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('nombre');
            $table->json('titular')->nullable();
            $table->json('descripcion_corta')->nullable();
            $table->json('descripcion_larga')->nullable();
            $table->string('direccion')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('provincia')->nullable();
            $table->char('pais', 2)->default('DO');
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
            $table->json('como_llegar')->nullable();
            $table->string('logo_ruta')->nullable();
            $table->string('telefono')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->boolean('publicada')->default(false);
            $table->json('meta_titulo')->nullable();
            $table->json('meta_descripcion')->nullable();
        });

        foreach (DB::table('edificios')->whereNotNull('sucursal_id')->get() as $edificio) {
            $sucursal = DB::table('sucursales')->find($edificio->sucursal_id);

            if (! $sucursal) {
                continue;
            }

            DB::table('edificios')->where('id', $edificio->id)->update([
                'slug' => $sucursal->slug,
                'titular' => $sucursal->titular,
                'descripcion_corta' => $sucursal->descripcion_corta,
                'descripcion_larga' => $sucursal->descripcion_larga,
                'direccion' => $sucursal->direccion,
                'ciudad' => $sucursal->ciudad,
                'provincia' => $sucursal->provincia,
                'pais' => $sucursal->pais,
                'latitud' => $sucursal->latitud,
                'longitud' => $sucursal->longitud,
                'como_llegar' => $sucursal->como_llegar,
                'logo_ruta' => $sucursal->logo_ruta,
                'telefono' => $sucursal->telefono,
                'whatsapp' => $sucursal->whatsapp,
                'email' => $sucursal->email,
                'publicada' => $sucursal->publicada,
                'meta_titulo' => $sucursal->meta_titulo,
                'meta_descripcion' => $sucursal->meta_descripcion,
            ]);

            DB::table('fotos')
                ->where('fotable_type', 'sucursal')->where('fotable_id', $sucursal->id)
                ->update(['fotable_type' => 'edificio', 'fotable_id' => $edificio->id]);

            DB::table('bloques_contenido')
                ->where('bloqueable_type', 'sucursal')->where('bloqueable_id', $sucursal->id)
                ->update(['bloqueable_type' => 'edificio', 'bloqueable_id' => $edificio->id]);
        }

        Schema::table('edificios', function (Blueprint $table) {
            $table->dropForeign(['sucursal_id']);
            $table->dropColumn(['sucursal_id', 'pisos']);
        });

        DB::table('sucursales')->delete();
        DB::table('empresas')->delete();
    }
};
