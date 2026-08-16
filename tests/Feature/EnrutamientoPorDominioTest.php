<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * El panel y el sitio público se separan por dominio y no por prefijo de URL,
 * justamente para que el backoffice no sea alcanzable desde el sitio del cliente.
 * Estas pruebas fijan esa garantía.
 */
class EnrutamientoPorDominioTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_panel_no_es_alcanzable_desde_el_dominio_publico(): void
    {
        $this->get($this->web('/dashboard'))->assertNotFound();
        $this->get($this->web('/login'))->assertNotFound();
        $this->get($this->web('/departamentos'))->assertNotFound();
        $this->get($this->web('/sucursales'))->assertNotFound();
    }

    public function test_el_sitio_publico_no_responde_en_el_dominio_del_panel(): void
    {
        $this->get($this->panel('/'))->assertRedirect(route('dashboard'));
    }

    public function test_la_portada_publica_muestra_la_sucursal_publicada(): void
    {
        $this->crearSucursal([
            'titular' => ['es' => 'Frase de portada', 'en' => 'Headline'],
        ]);

        $this->get($this->web('/'))
            ->assertOk()
            ->assertSee('Frase de portada');
    }

    public function test_la_portada_publica_no_muestra_sucursales_en_borrador(): void
    {
        $this->crearSucursal(['publicada' => false]);

        $this->get($this->web('/'))->assertNotFound();
    }
}
