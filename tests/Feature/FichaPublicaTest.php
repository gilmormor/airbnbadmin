<?php

namespace Tests\Feature;

use App\Models\Amenidad;
use App\Models\Departamento;
use Database\Seeders\AmenidadesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FichaPublicaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(AmenidadesSeeder::class);
    }

    private function crearUnidad(bool $publicado = true, bool $sucursalPublicada = true): Departamento
    {
        $sucursal = $this->crearSucursal(['publicada' => $sucursalPublicada]);
        $edificio = $this->crearEdificio($sucursal);

        $departamento = $this->crearDepartamento($edificio, [
            'titular' => ['es' => 'Vista al mar con jacuzzi', 'en' => 'Ocean view with jacuzzi'],
            'descripcion_larga' => ['es' => 'Un penthouse elegante frente a la playa.'],
            'capacidad_huespedes' => 6,
            'dormitorios' => 2,
            'banos_completos' => 2,
            'piso' => 3,
            'precio_base_noche' => 250,
            'tarifa_limpieza' => 60,
            'publicado' => $publicado,
        ]);

        $departamento->camas()->create([
            'ambiente' => ['es' => 'Dormitorio 1'],
            'tipo' => 'king',
            'cantidad' => 1,
        ]);

        $departamento->amenidades()->attach(
            Amenidad::where('slug', 'jacuzzi')->value('id'),
            ['destacada' => true]
        );

        return $departamento;
    }

    public function test_la_ficha_muestra_los_datos_de_la_unidad(): void
    {
        $this->crearUnidad();

        $this->get($this->web('/villa-riberamar/penthouse-a3'))
            ->assertOk()
            ->assertSee('Vista al mar con jacuzzi')
            ->assertSee('Un penthouse elegante frente a la playa.')
            ->assertSee('Dormitorio 1')
            ->assertSee('Jacuzzi climatizado')
            ->assertSee('6 huéspedes', false)
            ->assertSee('250');
    }

    public function test_la_ficha_muestra_las_reglas_de_la_casa(): void
    {
        $this->crearUnidad();

        $this->get($this->web('/villa-riberamar/penthouse-a3'))
            ->assertOk()
            ->assertSee('15:00')
            ->assertSee('12:00')
            ->assertSee('No permitidas');
    }

    public function test_una_unidad_en_borrador_no_es_accesible(): void
    {
        $this->crearUnidad(publicado: false);

        $this->get($this->web('/villa-riberamar/penthouse-a3'))->assertNotFound();
    }

    public function test_una_unidad_de_sucursal_no_publicada_no_es_accesible(): void
    {
        $this->crearUnidad(sucursalPublicada: false);

        $this->get($this->web('/villa-riberamar/penthouse-a3'))->assertNotFound();
    }

    public function test_no_responde_con_una_sucursal_que_no_corresponde(): void
    {
        $this->crearUnidad();

        // El slug del departamento existe, pero colgado de otra sucursal.
        $this->get($this->web('/otra-sucursal/penthouse-a3'))->assertNotFound();
    }

    public function test_la_portada_enlaza_a_la_ficha(): void
    {
        $this->crearUnidad();

        $this->get($this->web('/'))
            ->assertOk()
            ->assertSee('/villa-riberamar/penthouse-a3', false);
    }

    public function test_la_ficha_no_responde_bajo_el_prefijo_del_panel(): void
    {
        $this->crearUnidad();

        $this->get($this->panel('/villa-riberamar/penthouse-a3'))->assertNotFound();
    }
}
