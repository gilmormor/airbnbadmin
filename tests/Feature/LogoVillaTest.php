<?php

namespace Tests\Feature;

use App\Models\Sucursal;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * El logo pertenece a la sucursal y no a la empresa: la marca que ve el huésped
 * es la de la propiedad («Villa Ribera Mar by Arpel»), y cada sucursal tendrá la suya.
 */
class LogoVillaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function datosBase(Sucursal $sucursal): array
    {
        return [
            'nombre' => $sucursal->nombre,
            'slug' => $sucursal->slug,
            'pais' => 'DO',
            'orden' => 0,
            'publicada' => '1',
        ];
    }

    public function test_sube_el_logo_y_lo_guarda_en_la_sucursal(): void
    {
        $sucursal = $this->crearSucursal();

        $this->actingAs($this->administrador())
            ->put($this->panel('/sucursales/'.$sucursal->id), $this->datosBase($sucursal) + [
                'logo' => UploadedFile::fake()->image('logo.png', 400, 120),
            ])
            ->assertRedirect();

        $sucursal->refresh();

        $this->assertNotNull($sucursal->logo_ruta);
        Storage::disk('public')->assertExists($sucursal->logo_ruta);
    }

    public function test_reemplazar_el_logo_borra_el_anterior(): void
    {
        $sucursal = $this->crearSucursal();
        $administrador = $this->administrador();

        $this->actingAs($administrador)->put($this->panel('/sucursales/'.$sucursal->id), $this->datosBase($sucursal) + [
            'logo' => UploadedFile::fake()->image('viejo.png', 400, 120),
        ]);

        $rutaVieja = $sucursal->fresh()->logo_ruta;

        $this->actingAs($administrador)->put($this->panel('/sucursales/'.$sucursal->id), $this->datosBase($sucursal) + [
            'logo' => UploadedFile::fake()->image('nuevo.png', 400, 120),
        ]);

        $rutaNueva = $sucursal->fresh()->logo_ruta;

        $this->assertNotSame($rutaVieja, $rutaNueva);
        Storage::disk('public')->assertMissing($rutaVieja);
        Storage::disk('public')->assertExists($rutaNueva);
    }

    public function test_puede_quitarse_el_logo(): void
    {
        $sucursal = $this->crearSucursal();
        $administrador = $this->administrador();

        $this->actingAs($administrador)->put($this->panel('/sucursales/'.$sucursal->id), $this->datosBase($sucursal) + [
            'logo' => UploadedFile::fake()->image('logo.png', 400, 120),
        ]);

        $ruta = $sucursal->fresh()->logo_ruta;

        $this->actingAs($administrador)->put($this->panel('/sucursales/'.$sucursal->id), $this->datosBase($sucursal) + [
            'quitar_logo' => '1',
        ]);

        $this->assertNull($sucursal->fresh()->logo_ruta);
        Storage::disk('public')->assertMissing($ruta);
    }

    public function test_rechaza_un_svg_por_seguridad(): void
    {
        $sucursal = $this->crearSucursal();

        $this->actingAs($this->administrador())
            ->put($this->panel('/sucursales/'.$sucursal->id), $this->datosBase($sucursal) + [
                'logo' => UploadedFile::fake()->create('logo.svg', 10, 'image/svg+xml'),
            ])
            ->assertSessionHasErrors('logo');

        $this->assertNull($sucursal->fresh()->logo_ruta);
    }

    public function test_el_sitio_muestra_el_logo_con_el_nombre_como_titulo(): void
    {
        $sucursal = $this->crearSucursal();

        $this->actingAs($this->administrador())->put($this->panel('/sucursales/'.$sucursal->id), $this->datosBase($sucursal) + [
            'logo' => UploadedFile::fake()->image('logo.png', 400, 120),
        ]);

        $this->get($this->web('/'))
            ->assertOk()
            ->assertSee($sucursal->fresh()->logo_ruta, false)
            ->assertSee('title="Villa Riberamar"', false)
            ->assertSee('alt="Villa Riberamar"', false);
    }

    public function test_sin_logo_el_sitio_muestra_el_nombre_en_texto(): void
    {
        $this->crearSucursal();

        $this->get($this->web('/'))
            ->assertOk()
            ->assertSee('Villa Riberamar')
            ->assertDontSee('<img src="/storage/logos', false);
    }
}
