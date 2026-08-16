<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_guests_are_redirected_to_login(): void
    {
        // Las rutas del panel están atadas a su dominio: sin ese host la petición
        // no coincide con ninguna ruta y devuelve 404 en lugar de redirigir.
        $this->get($this->panel('/dashboard'))->assertRedirect(route('login'));
    }
}
