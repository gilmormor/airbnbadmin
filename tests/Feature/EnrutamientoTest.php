<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * El panel y el sitio público comparten dominio y se separan por prefijo: el
 * panel cuelga de /admin.
 *
 * La ruta pública /{sucursal}/{departamento} tiene dos segmentos y se tragaría
 * las del panel si se registrara antes, así que estas pruebas fijan que eso no
 * ocurra ni por orden ni por descuido.
 */
class EnrutamientoTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_panel_vive_bajo_el_prefijo_admin(): void
    {
        // Sin sesión redirige al login, que es lo que confirma que la ruta existe.
        $this->get('/admin/dashboard')->assertRedirect(route('login'));
        $this->get('/admin/login')->assertOk();
    }

    public function test_la_ruta_publica_no_se_traga_las_del_panel(): void
    {
        $this->crearSucursal();

        // «admin» nunca debe interpretarse como el slug de una sucursal.
        $this->get('/admin/departamentos')->assertRedirect(route('login'));
        $this->get('/admin/reservas')->assertRedirect(route('login'));
    }

    /**
     * La ficha pública toma dos segmentos y ni uno más. Un patrón mal escrito en
     * su restricción hace que el parámetro capture también las barras, y entonces
     * cualquier URL profunda cae en la ficha en vez de dar 404.
     */
    public function test_la_ruta_publica_solo_acepta_dos_segmentos(): void
    {
        $this->crearSucursal();

        $this->get('/una/ruta/de/cuatro/segmentos')->assertNotFound();
        $this->get('/admin/fotos/usuario/1')->assertNotFound();
    }

    public function test_la_portada_publica_responde_en_la_raiz(): void
    {
        $this->crearSucursal([
            'titular' => ['es' => 'Frase de portada', 'en' => 'Headline'],
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Frase de portada');
    }

    public function test_la_portada_publica_no_muestra_sucursales_en_borrador(): void
    {
        $this->crearSucursal(['publicada' => false]);

        $this->get('/')->assertNotFound();
    }
}
