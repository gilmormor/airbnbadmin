<?php

namespace Tests\Feature;

use App\Models\Departamento;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Lista numerada dentro de una sección de contenido.
 *
 * No son las amenidades del catálogo: aquellas sirven para filtrar el buscador,
 * estas son frases curadas que resumen lo que incluye la reserva.
 */
class ListaSeccionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function datosBase(Departamento $d): array
    {
        return [
            'edificio_id' => $d->edificio_id,
            'propietario_id' => $d->propietario_id,
            'nombre' => $d->nombre,
            'tipo' => 'penthouse',
            'capacidad_huespedes' => 6,
            'huespedes_incluidos' => 4,
            'dormitorios' => 2,
            'banos_completos' => 2,
            'banos_medios' => 0,
            'precio_base_noche' => 250,
            'moneda' => 'USD',
            'tarifa_limpieza' => 0,
            'tarifa_lavanderia' => 0,
            'deposito_seguridad' => 0,
            'cargo_huesped_adicional' => 0,
            'cargo_extra_noche' => 0,
            'descuento_semanal_pct' => 0,
            'descuento_mensual_pct' => 0,
            'descuento_directo_pct' => 0,
            'noches_minimas' => 2,
            'antelacion_minima_dias' => 0,
            'ventana_reserva_meses' => 12,
            'dias_preparacion' => 0,
            'check_in_desde' => '15:00',
            'check_out_hasta' => '12:00',
            'deposito_mascotas' => 0,
            'publicado' => '1',
            'orden' => 0,
        ];
    }

    public function test_guarda_la_lista_en_los_dos_idiomas_y_en_orden(): void
    {
        $departamento = $this->crearDepartamento(null, ['publicado' => true]);

        $this->actingAs($this->administrador())
            ->put($this->panel('/departamentos/'.$departamento->id), $this->datosBase($departamento) + [
                'bloques' => [[
                    'titulo_es' => 'Tu reserva incluye',
                    'items' => [
                        ['es' => '2 dormitorios (6 huéspedes)', 'en' => '2 bedrooms (6 guests)'],
                        ['es' => 'Terraza con jacuzzi', 'en' => 'Roof terrace with Jacuzzi'],
                    ],
                ]],
            ])
            ->assertRedirect();

        $bloque = $departamento->bloques()->firstOrFail();

        $this->assertCount(2, $bloque->items);
        $this->assertSame('2 dormitorios (6 huéspedes)', $bloque->items[0]['es']);
        $this->assertSame('Roof terrace with Jacuzzi', $bloque->items[1]['en']);
        $this->assertSame(
            ['2 dormitorios (6 huéspedes)', 'Terraza con jacuzzi'],
            $bloque->itemsTraducidos('es')
        );
    }

    public function test_descarta_las_lineas_vacias(): void
    {
        $departamento = $this->crearDepartamento(null, ['publicado' => true]);

        $this->actingAs($this->administrador())
            ->put($this->panel('/departamentos/'.$departamento->id), $this->datosBase($departamento) + [
                'bloques' => [[
                    'titulo_es' => 'Tu reserva incluye',
                    'items' => [
                        ['es' => 'Línea con contenido', 'en' => ''],
                        ['es' => '', 'en' => ''],
                    ],
                ]],
            ]);

        $this->assertCount(1, $departamento->bloques()->firstOrFail()->items);
    }

    public function test_una_seccion_solo_con_lista_tambien_se_guarda(): void
    {
        $departamento = $this->crearDepartamento(null, ['publicado' => true]);

        $this->actingAs($this->administrador())
            ->put($this->panel('/departamentos/'.$departamento->id), $this->datosBase($departamento) + [
                'bloques' => [[
                    'items' => [['es' => 'Seguridad 24 horas']],
                ]],
            ]);

        $this->assertCount(1, $departamento->bloques, 'El titular y el párrafo son opcionales.');
    }

    public function test_la_ficha_publica_muestra_la_lista_numerada(): void
    {
        $departamento = $this->crearDepartamento(null, ['publicado' => true]);

        $departamento->bloques()->create([
            'titulo' => ['es' => 'Tu reserva incluye'],
            'items' => [
                ['es' => 'Terraza en la azotea con jacuzzi'],
                ['es' => 'Servicio de limpieza diario'],
            ],
        ]);

        $this->get($this->web('/villa-riberamar/penthouse-a3'))
            ->assertOk()
            ->assertSee('Terraza en la azotea con jacuzzi')
            ->assertSee('Servicio de limpieza diario')
            ->assertSee('[ 01 ]')
            ->assertSee('[ 02 ]');
    }

    public function test_usa_el_idioma_de_respaldo_cuando_falta_la_traduccion(): void
    {
        $departamento = $this->crearDepartamento(null, ['publicado' => true]);

        $bloque = $departamento->bloques()->create([
            'titulo' => ['es' => 'Tu reserva incluye'],
            'items' => [['en' => 'Roof terrace with Jacuzzi']],
        ]);

        $this->assertSame(
            ['Roof terrace with Jacuzzi'],
            $bloque->itemsTraducidos('es'),
            'Sin traducción al español debe mostrarse la disponible, no una línea vacía.'
        );
    }
}
