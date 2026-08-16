<?php

namespace Tests\Feature;

use App\Models\Departamento;
use App\Models\Edificio;
use App\Models\Propietario;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BloquesContenidoTest extends TestCase
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

    public function test_guarda_las_secciones_en_los_dos_idiomas(): void
    {
        $departamento = $this->crearDepartamento(null, ['publicado' => true]);

        $this->actingAs($this->administrador())
            ->put($this->panel('/departamentos/').$departamento->id, $this->datosBase($departamento) + [
                'bloques' => [
                    [
                        'antetitulo_es' => 'Nuestra propiedad',
                        'antetitulo_en' => 'Our Facility',
                        'titulo_es' => 'El mejor lugar para disfrutar la vida',
                        'titulo_en' => 'Best place to enjoy your life',
                        'cuerpo_es' => 'Este elegante penthouse tiene 170 m².',
                        'cuerpo_en' => 'This elegant Penthouse has 1,829 sq ft.',
                    ],
                    [
                        'antetitulo_es' => 'Exterior',
                        'titulo_es' => 'Diseño moderno minimalista',
                        'cuerpo_es' => 'Relájate en un día soleado.',
                    ],
                ],
            ])
            ->assertRedirect();

        $bloques = $departamento->bloques()->orderBy('orden')->get();

        $this->assertCount(2, $bloques);
        $this->assertSame('Our Facility', $bloques[0]->texto('antetitulo', 'en'));
        $this->assertSame('El mejor lugar para disfrutar la vida', $bloques[0]->texto('titulo', 'es'));
        $this->assertSame([0, 1], $bloques->pluck('orden')->all());
    }

    public function test_descarta_las_secciones_vacias(): void
    {
        $departamento = $this->crearDepartamento(null, ['publicado' => true]);

        $this->actingAs($this->administrador())
            ->put($this->panel('/departamentos/').$departamento->id, $this->datosBase($departamento) + [
                'bloques' => [
                    ['titulo_es' => 'Sección con contenido', 'cuerpo_es' => 'Texto.'],
                    ['antetitulo_es' => '', 'titulo_es' => '', 'cuerpo_es' => ''],
                ],
            ]);

        $this->assertCount(1, $departamento->bloques, 'Una fila agregada y dejada vacía no debe guardarse.');
    }

    public function test_la_ficha_publica_muestra_las_secciones(): void
    {
        $departamento = $this->crearDepartamento(null, ['publicado' => true]);

        $departamento->bloques()->create([
            'antetitulo' => ['es' => 'Nuestra propiedad', 'en' => 'Our Facility'],
            'titulo' => ['es' => 'El mejor lugar para disfrutar la vida'],
            'cuerpo' => ['es' => 'Madera de roble y mármol italiano.'],
            'orden' => 0,
        ]);

        $this->get($this->web('/villa-riberamar/penthouse-a3'))
            ->assertOk()
            ->assertSee('Nuestra propiedad')
            ->assertSee('El mejor lugar para disfrutar la vida')
            ->assertSee('Madera de roble y mármol italiano.');
    }

    public function test_la_ficha_no_muestra_secciones_despublicadas(): void
    {
        $departamento = $this->crearDepartamento(null, ['publicado' => true]);

        $departamento->bloques()->create([
            'titulo' => ['es' => 'Sección oculta'],
            'cuerpo' => ['es' => 'No debería verse.'],
            'publicado' => false,
        ]);

        $this->get($this->web('/villa-riberamar/penthouse-a3'))
            ->assertOk()
            ->assertDontSee('Sección oculta');
    }

    public function test_sube_la_imagen_de_una_seccion(): void
    {
        Storage::fake('public');
        $departamento = $this->crearDepartamento(null, ['publicado' => true]);

        $this->actingAs($this->administrador())
            ->put($this->panel('/departamentos/').$departamento->id, $this->datosBase($departamento) + [
                'bloques' => [[
                    'titulo_es' => 'Diseño moderno minimalista',
                    'cuerpo_es' => 'Relájate en un día soleado.',
                    'imagen' => UploadedFile::fake()->image('terraza.jpg', 1600, 1200),
                ]],
            ])
            ->assertRedirect();

        $bloque = $departamento->bloques()->firstOrFail();

        $this->assertNotNull($bloque->imagen_ruta);
        Storage::disk('public')->assertExists($bloque->imagen_ruta);
    }

    public function test_editar_el_texto_conserva_la_imagen_ya_cargada(): void
    {
        Storage::fake('public');
        $departamento = $this->crearDepartamento(null, ['publicado' => true]);
        $administrador = $this->administrador();

        $this->actingAs($administrador)->put($this->panel('/departamentos/').$departamento->id, $this->datosBase($departamento) + [
            'bloques' => [[
                'titulo_es' => 'Título original',
                'cuerpo_es' => 'Texto.',
                'imagen' => UploadedFile::fake()->image('foto.jpg', 1600, 1200),
            ]],
        ]);

        $bloque = $departamento->bloques()->firstOrFail();
        $rutaOriginal = $bloque->imagen_ruta;

        // Se reenvía el id, como hace el formulario, y solo cambia el texto.
        $this->actingAs($administrador)->put($this->panel('/departamentos/').$departamento->id, $this->datosBase($departamento) + [
            'bloques' => [[
                'id' => $bloque->id,
                'titulo_es' => 'Título corregido',
                'cuerpo_es' => 'Texto.',
            ]],
        ]);

        $bloque->refresh();

        $this->assertSame('Título corregido', $bloque->texto('titulo', 'es'));
        $this->assertSame($rutaOriginal, $bloque->imagen_ruta, 'Editar el texto no debe borrar la imagen.');
        Storage::disk('public')->assertExists($rutaOriginal);
    }

    public function test_quitar_una_seccion_borra_su_imagen(): void
    {
        Storage::fake('public');
        $departamento = $this->crearDepartamento(null, ['publicado' => true]);
        $administrador = $this->administrador();

        $this->actingAs($administrador)->put($this->panel('/departamentos/').$departamento->id, $this->datosBase($departamento) + [
            'bloques' => [[
                'titulo_es' => 'Sección a eliminar',
                'imagen' => UploadedFile::fake()->image('foto.jpg', 1600, 1200),
            ]],
        ]);

        $ruta = $departamento->bloques()->firstOrFail()->imagen_ruta;

        $this->actingAs($administrador)->put(
            $this->panel('/departamentos/').$departamento->id,
            $this->datosBase($departamento) + ['bloques' => []]
        );

        $this->assertCount(0, $departamento->fresh()->bloques);
        Storage::disk('public')->assertMissing($ruta);
    }

    public function test_las_secciones_alternan_el_lado_de_la_imagen(): void
    {
        $departamento = $this->crearDepartamento(null, ['publicado' => true]);

        foreach (['Primera', 'Segunda', 'Tercera'] as $orden => $titulo) {
            $departamento->bloques()->create([
                'titulo' => ['es' => $titulo],
                'imagen_ruta' => "secciones/{$departamento->id}/{$orden}.jpg",
                'orden' => $orden,
            ]);
        }

        $html = $this->get($this->web('/villa-riberamar/penthouse-a3'))->assertOk()->getContent();

        preg_match_all('/data-desde="(izquierda|derecha)"/', $html, $coincidencias);

        $this->assertSame(
            ['izquierda', 'derecha', 'izquierda'],
            $coincidencias[1],
            'Las secciones deben alternar el lado de la imagen según su orden.'
        );
    }

    public function test_el_nombre_de_la_unidad_es_el_encabezado_principal(): void
    {
        $this->crearDepartamento(null, ['publicado' => true]);

        $this->get($this->web('/villa-riberamar/penthouse-a3'))
            ->assertOk()
            ->assertSee('<h1', false)
            ->assertSee('Penthouse A3');
    }
}
