<?php

namespace Tests\Feature;

use App\Models\Departamento;
use App\Models\Foto;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GaleriaFotosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_sube_varias_fotos_y_marca_la_primera_como_portada(): void
    {
        $departamento = $this->crearDepartamento();

        $this->actingAs($this->administrador())
            ->post($this->panel("/fotos/departamento/{$departamento->id}"), [
                'fotos' => [
                    UploadedFile::fake()->image('sala.jpg', 1600, 1200),
                    UploadedFile::fake()->image('cocina.jpg', 1600, 1200),
                ],
            ])
            ->assertRedirect();

        $fotos = $departamento->fotos()->orderBy('orden')->get();

        $this->assertCount(2, $fotos);
        $this->assertTrue($fotos[0]->portada, 'La primera foto de una galería vacía debe quedar de portada.');
        $this->assertFalse($fotos[1]->portada);
        $this->assertSame([0, 1], $fotos->pluck('orden')->all());
        $this->assertSame(1600, $fotos[0]->ancho, 'Deben registrarse las dimensiones para poder avisar de imágenes pequeñas.');

        foreach ($fotos as $foto) {
            Storage::disk('public')->assertExists($foto->ruta);
        }
    }

    public function test_la_galeria_tambien_funciona_en_una_sucursal(): void
    {
        $sucursal = $this->crearSucursal();

        $this->actingAs($this->administrador())
            ->post($this->panel("/fotos/sucursal/{$sucursal->id}"), [
                'fotos' => [UploadedFile::fake()->image('portada.jpg', 2000, 1200)],
            ])
            ->assertRedirect();

        $this->assertCount(1, $sucursal->fotos);
        $this->assertSame('sucursal', $sucursal->fotos->first()->fotable_type);
    }

    public function test_rechaza_archivos_que_no_son_imagenes(): void
    {
        $departamento = $this->crearDepartamento();

        $this->actingAs($this->administrador())
            ->post($this->panel("/fotos/departamento/{$departamento->id}"), [
                'fotos' => [UploadedFile::fake()->create('documento.pdf', 100, 'application/pdf')],
            ])
            ->assertSessionHasErrors('fotos.0');

        $this->assertSame(0, $departamento->fotos()->count());
    }

    public function test_cambiar_la_portada_desmarca_la_anterior(): void
    {
        $departamento = $this->crearDepartamento();
        $primera = $this->crearFoto($departamento, 0, true);
        $segunda = $this->crearFoto($departamento, 1, false);

        $this->actingAs($this->administrador())
            ->post($this->panel("/fotos/{$segunda->id}/portada"))
            ->assertRedirect();

        $this->assertFalse($primera->fresh()->portada);
        $this->assertTrue($segunda->fresh()->portada);
    }

    public function test_borrar_la_portada_asciende_a_la_siguiente(): void
    {
        $departamento = $this->crearDepartamento();
        $portada = $this->crearFoto($departamento, 0, true);
        $siguiente = $this->crearFoto($departamento, 1, false);

        $this->actingAs($this->administrador())
            ->delete($this->panel("/fotos/{$portada->id}"))
            ->assertRedirect();

        $this->assertNull(Foto::find($portada->id));
        $this->assertTrue($siguiente->fresh()->portada, 'La galería no debe quedarse sin portada.');
    }

    public function test_guarda_el_orden_arrastrado(): void
    {
        $departamento = $this->crearDepartamento();
        $primera = $this->crearFoto($departamento, 0, true);
        $segunda = $this->crearFoto($departamento, 1, false);

        $this->actingAs($this->administrador())
            ->postJson($this->panel("/fotos/departamento/{$departamento->id}/orden"), [
                'orden' => [$segunda->id, $primera->id],
            ])
            ->assertOk();

        $this->assertSame(0, $segunda->fresh()->orden);
        $this->assertSame(1, $primera->fresh()->orden);
    }

    public function test_no_reordena_fotos_de_otro_departamento(): void
    {
        $propio = $this->crearDepartamento();
        $ajeno = Departamento::create([
            'edificio_id' => $propio->edificio_id,
            'propietario_id' => $propio->propietario_id,
            'nombre' => 'Unidad 2',
            'slug' => 'unidad-2',
        ]);

        $fotoAjena = $this->crearFoto($ajeno, 7, false);

        $this->actingAs($this->administrador())
            ->postJson($this->panel("/fotos/departamento/{$propio->id}/orden"), [
                'orden' => [$fotoAjena->id],
            ])
            ->assertOk();

        $this->assertSame(7, $fotoAjena->fresh()->orden, 'No debe poder reordenarse la galería de otro modelo.');
    }

    public function test_un_tipo_desconocido_no_instancia_modelos_arbitrarios(): void
    {
        $this->actingAs($this->administrador())
            ->post($this->panel('/fotos/usuario/1'), [
                'fotos' => [UploadedFile::fake()->image('x.jpg')],
            ])
            ->assertNotFound();
    }

    private function crearFoto(Departamento $departamento, int $orden, bool $portada): Foto
    {
        return $departamento->fotos()->create([
            'ruta' => "fotos/departamento/{$departamento->id}/".uniqid().'.jpg',
            'orden' => $orden,
            'portada' => $portada,
        ]);
    }
}
