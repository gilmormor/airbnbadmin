<?php

namespace Tests\Feature;

use App\Models\Amenidad;
use Database\Seeders\AmenidadesSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepartamentoAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        $this->seed(AmenidadesSeeder::class);
    }

    public function test_el_formulario_de_alta_carga(): void
    {
        $this->actingAs($this->administrador())
            ->get($this->panel('/departamentos/create'))
            ->assertOk()
            ->assertSee('Distribución de camas')
            ->assertSee('Tarifa de lavandería');
    }

    public function test_el_formulario_de_edicion_carga_con_los_datos(): void
    {
        $departamento = $this->crearDepartamento();

        $this->actingAs($this->administrador())
            ->get($this->panel('/departamentos/'.$departamento->id.'/edit'))
            ->assertOk()
            ->assertSee('Penthouse A3')
            ->assertSee('Amenidades');
    }

    public function test_guarda_la_ficha_completa_con_camas_y_amenidades(): void
    {
        $departamento = $this->crearDepartamento();
        $jacuzzi = Amenidad::where('slug', 'jacuzzi')->firstOrFail();
        $wifi = Amenidad::where('slug', 'wifi')->firstOrFail();

        $this->actingAs($this->administrador())
            ->put($this->panel('/departamentos/'.$departamento->id), [
                'edificio_id' => $departamento->edificio_id,
                'propietario_id' => $departamento->propietario_id,
                'nombre' => 'Penthouse A3',
                'slug' => '',
                'tipo' => 'penthouse',
                'piso' => 3,
                'titular' => ['es' => 'Vista al mar', 'en' => 'Ocean view'],
                'capacidad_huespedes' => 6,
                'dormitorios' => 2,
                'banos_completos' => 2,
                'banos_medios' => 0,
                'superficie_m2' => 169.92,
                'precio_base_noche' => 250,
                'moneda' => 'USD',
                'tarifa_limpieza' => 60,
                'tarifa_lavanderia' => 15,
                'deposito_seguridad' => 200,
                'huespedes_incluidos' => 4,
                'cargo_huesped_adicional' => 25,
                'cargo_extra_noche' => 20,
                'descuento_semanal_pct' => 10,
                'descuento_mensual_pct' => 20,
                'descuento_directo_pct' => 5,
                'noches_minimas' => 2,
                'antelacion_minima_dias' => 0,
                'ventana_reserva_meses' => 12,
                'dias_preparacion' => 0,
                'check_in_desde' => '15:00',
                'check_out_hasta' => '12:00',
                'deposito_mascotas' => 100,
                'mascotas_permitidas' => '1',
                'publicado' => '1',
                'orden' => 1,
                'amenidades' => [$jacuzzi->id, $wifi->id],
                'destacadas' => [$jacuzzi->id],
                'camas' => [
                    ['ambiente_es' => 'Dormitorio 1', 'ambiente_en' => 'Bedroom 1', 'tipo' => 'king', 'cantidad' => 1],
                    ['ambiente_es' => 'Sala', 'ambiente_en' => 'Living room', 'tipo' => 'sofa_cama', 'cantidad' => 1],
                ],
            ])
            ->assertRedirect($this->panel('/departamentos'));

        $departamento->refresh();

        $this->assertSame('penthouse-a3', $departamento->slug, 'El slug debe derivarse del nombre.');
        $this->assertSame('Vista al mar', $departamento->texto('titular', 'es'));
        $this->assertSame(3, $departamento->piso);
        $this->assertTrue($departamento->publicado);
        $this->assertTrue($departamento->mascotas_permitidas);
        $this->assertSame('250.00', $departamento->precio_base_noche);

        $this->assertCount(2, $departamento->camas);
        $this->assertSame('king', $departamento->camas->first()->tipo);

        $this->assertCount(2, $departamento->amenidades);
        $this->assertTrue(
            (bool) $departamento->amenidades->firstWhere('id', $jacuzzi->id)->pivot->destacada,
            'El jacuzzi debía quedar marcado como destacado.'
        );
    }

    public function test_rechaza_mas_huespedes_incluidos_que_la_capacidad(): void
    {
        $departamento = $this->crearDepartamento();

        $this->actingAs($this->administrador())
            ->put($this->panel('/departamentos/'.$departamento->id), [
                'edificio_id' => $departamento->edificio_id,
                'propietario_id' => $departamento->propietario_id,
                'nombre' => 'Unidad 1',
                'tipo' => 'departamento',
                'capacidad_huespedes' => 4,
                'huespedes_incluidos' => 6,
                'dormitorios' => 1,
                'banos_completos' => 1,
                'banos_medios' => 0,
                'precio_base_noche' => 100,
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
                'orden' => 0,
            ])
            ->assertSessionHasErrors('huespedes_incluidos');
    }
}
