<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Ficha completa del departamento, al estilo de un anuncio de Airbnb.
 *
 * `nombre` se conserva como identificador interno para el panel; `titular` es el
 * título comercial que ve el huésped. Los campos de texto públicos son JSON con
 * una clave por idioma.
 *
 * Los datos sensibles de acceso (clave de puerta, wifi) NO viven aquí: están en la
 * tabla `accesos`, separados para poder restringirlos por permiso y auditarlos.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('departamentos', function (Blueprint $table) {
            // --- Identidad pública ---
            // Se crea nullable para poder rellenar las filas existentes antes de
            // aplicar el índice único; se endurece al final del método.
            $table->string('slug')->nullable()->after('nombre');
            $table->string('tipo')->default('departamento')->after('slug');
            $table->json('titular')->nullable()->after('tipo');
            $table->json('descripcion_corta')->nullable()->after('titular');
            $table->json('descripcion_larga')->nullable()->after('descripcion_corta');

            // --- Capacidad y espacio ---
            $table->unsignedSmallInteger('capacidad_huespedes')->default(2)->after('descripcion_larga');
            $table->unsignedSmallInteger('dormitorios')->default(1)->after('capacidad_huespedes');
            $table->unsignedSmallInteger('banos_completos')->default(1)->after('dormitorios');
            $table->unsignedSmallInteger('banos_medios')->default(0)->after('banos_completos');
            $table->decimal('superficie_m2', 8, 2)->nullable()->after('banos_medios');

            // --- Precios y cargos ---
            $table->decimal('precio_base_noche', 10, 2)->default(0)->after('superficie_m2');
            $table->char('moneda', 3)->default('USD')->after('precio_base_noche');
            $table->decimal('tarifa_limpieza', 10, 2)->default(0)->after('moneda');
            $table->decimal('tarifa_lavanderia', 10, 2)->default(0)->after('tarifa_limpieza');
            $table->decimal('deposito_seguridad', 10, 2)->default(0)->after('tarifa_lavanderia');
            // Se cobra por cada huésped que exceda `huespedes_incluidos`.
            $table->unsignedSmallInteger('huespedes_incluidos')->default(2)->after('deposito_seguridad');
            $table->decimal('cargo_huesped_adicional', 10, 2)->default(0)->after('huespedes_incluidos');
            // Cargo propio de los penthouses: aire central de sala y comedor (US$20/noche).
            $table->decimal('cargo_extra_noche', 10, 2)->default(0)->after('cargo_huesped_adicional');
            $table->json('cargo_extra_concepto')->nullable()->after('cargo_extra_noche');

            // --- Descuentos ---
            $table->decimal('descuento_semanal_pct', 5, 2)->default(0)->after('cargo_extra_concepto');
            $table->decimal('descuento_mensual_pct', 5, 2)->default(0)->after('descuento_semanal_pct');
            // Incentivo para desviar tráfico desde las OTAs hacia el canal directo.
            $table->decimal('descuento_directo_pct', 5, 2)->default(0)->after('descuento_mensual_pct');

            // --- Reglas de estadía ---
            $table->unsignedSmallInteger('noches_minimas')->default(2)->after('descuento_directo_pct');
            $table->unsignedSmallInteger('noches_maximas')->nullable()->after('noches_minimas');
            $table->unsignedSmallInteger('antelacion_minima_dias')->default(0)->after('noches_maximas');
            $table->unsignedSmallInteger('ventana_reserva_meses')->default(12)->after('antelacion_minima_dias');
            // Noches que se bloquean tras un check-out para preparar la unidad.
            $table->unsignedSmallInteger('dias_preparacion')->default(0)->after('ventana_reserva_meses');

            // --- Reglas de la casa ---
            $table->time('check_in_desde')->default('15:00:00')->after('dias_preparacion');
            $table->time('check_in_hasta')->nullable()->after('check_in_desde');
            $table->time('check_out_hasta')->default('12:00:00')->after('check_in_hasta');
            $table->boolean('mascotas_permitidas')->default(false)->after('check_out_hasta');
            $table->decimal('deposito_mascotas', 10, 2)->default(0)->after('mascotas_permitidas');
            $table->json('mascotas_condiciones')->nullable()->after('deposito_mascotas');
            $table->boolean('fumar_permitido')->default(false)->after('mascotas_condiciones');
            $table->boolean('eventos_permitidos')->default(false)->after('fumar_permitido');
            $table->time('hora_silencio')->nullable()->after('eventos_permitidos');
            $table->unsignedTinyInteger('edad_minima')->nullable()->after('hora_silencio');
            $table->json('reglas_adicionales')->nullable()->after('edad_minima');

            // --- Publicación ---
            $table->boolean('publicado')->default(false)->after('reglas_adicionales');
            $table->unsignedSmallInteger('orden')->default(0)->after('publicado');
            $table->json('meta_titulo')->nullable()->after('orden');
            $table->json('meta_descripcion')->nullable()->after('meta_titulo');
        });

        foreach (DB::table('departamentos')->select('id', 'nombre')->get() as $departamento) {
            DB::table('departamentos')
                ->where('id', $departamento->id)
                ->update(['slug' => Str::slug($departamento->nombre).'-'.$departamento->id]);
        }

        Schema::table('departamentos', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('departamentos', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn([
                'slug', 'tipo', 'titular', 'descripcion_corta', 'descripcion_larga',
                'capacidad_huespedes', 'dormitorios', 'banos_completos', 'banos_medios', 'superficie_m2',
                'precio_base_noche', 'moneda', 'tarifa_limpieza', 'tarifa_lavanderia', 'deposito_seguridad',
                'huespedes_incluidos', 'cargo_huesped_adicional', 'cargo_extra_noche', 'cargo_extra_concepto',
                'descuento_semanal_pct', 'descuento_mensual_pct', 'descuento_directo_pct',
                'noches_minimas', 'noches_maximas', 'antelacion_minima_dias', 'ventana_reserva_meses', 'dias_preparacion',
                'check_in_desde', 'check_in_hasta', 'check_out_hasta',
                'mascotas_permitidas', 'deposito_mascotas', 'mascotas_condiciones',
                'fumar_permitido', 'eventos_permitidos', 'hora_silencio', 'edad_minima', 'reglas_adicionales',
                'publicado', 'orden', 'meta_titulo', 'meta_descripcion',
            ]);
        });
    }
};
