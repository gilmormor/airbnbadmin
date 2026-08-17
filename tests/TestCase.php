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
     * base configurada. Un .env mal apuntado, o una configuración cacheada que
     * ignora los valores de phpunit.xml, bastarían para perder datos reales.
     *
     * La comprobación va aquí y no en setUp() porque el orden es lo único que la
     * hace útil: Laravel llama a refreshApplication() antes de setUpTraits(), que
     * es donde RefreshDatabase arrasa con la base. Hecha en setUp() después de
     * parent::setUp(), avisaría cuando el daño ya está hecho.
     */
    protected function refreshApplication()
    {
        parent::refreshApplication();

        $base = config('database.connections.'.config('database.default').'.database');

        if (! str_contains((string) $base, 'testing')) {
            throw new RuntimeException(
                "Las pruebas apuntan a la base «{$base}», que no parece de pruebas. ".
                'Revisa phpunit.xml y borra la configuración cacheada con '.
                '«php artisan config:clear»: si existe bootstrap/cache/config.php, '.
                'los valores de phpunit.xml se ignoran.'
            );
        }
    }

    /** Ruta dentro del panel, que cuelga del prefijo /admin. */
    protected function panel(string $ruta = ''): string
    {
        return '/admin'.$ruta;
    }

    /** Ruta del sitio público, que responde en la raíz. */
    protected function web(string $ruta = ''): string
    {
        return $ruta ?: '/';
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
