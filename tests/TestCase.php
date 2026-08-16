<?php

namespace Tests;

use App\Models\Departamento;
use App\Models\Edificio;
use App\Models\Empresa;
use App\Models\Propietario;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    /**
     * Las pruebas corren sobre MySQL, y RefreshDatabase borra y vuelve a migrar la
     * base que tenga configurada. Un .env mal apuntado bastaría para perder las
     * reservas reales, así que se comprueba el nombre antes de tocar nada.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $base = config('database.connections.'.config('database.default').'.database');

        if (! str_contains((string) $base, 'testing')) {
            throw new RuntimeException(
                "Las pruebas apuntan a la base «{$base}», que no parece de pruebas. ".
                'Revisa phpunit.xml: el nombre debe contener «testing».'
            );
        }
    }

    /** Dominio del panel; las rutas están atadas a él y sin ese host dan 404. */
    protected function panel(string $ruta = ''): string
    {
        return 'http://'.config('app.dominio_admin').$ruta;
    }

    /** Dominio del sitio público. */
    protected function web(string $ruta = ''): string
    {
        return 'http://'.config('app.dominio_web').$ruta;
    }

    protected function administrador(): User
    {
        $usuario = User::create([
            'name' => 'Administrador de prueba',
            'email' => 'admin@ejemplo.test',
            'password' => 'secreto-de-prueba',
        ]);

        $usuario->assignRole('Administrador');

        return $usuario;
    }

    protected function crearSucursal(array $atributos = []): Sucursal
    {
        return Sucursal::create($atributos + [
            'empresa_id' => Empresa::actual()->id,
            'nombre' => 'Villa Riberamar',
            'slug' => 'villa-riberamar',
            'pais' => 'DO',
            'publicada' => true,
        ]);
    }

    protected function crearEdificio(?Sucursal $sucursal = null, array $atributos = []): Edificio
    {
        return Edificio::create($atributos + [
            'sucursal_id' => ($sucursal ?? $this->crearSucursal())->id,
            'nombre' => 'Bloque A',
            'pisos' => 3,
        ]);
    }

    protected function crearDepartamento(?Edificio $edificio = null, array $atributos = []): Departamento
    {
        return Departamento::create($atributos + [
            'edificio_id' => ($edificio ?? $this->crearEdificio())->id,
            'propietario_id' => Propietario::firstOrCreate(['nombre' => 'Propietario'])->id,
            'nombre' => 'Penthouse A3',
            'slug' => 'penthouse-a3',
        ]);
    }
}
