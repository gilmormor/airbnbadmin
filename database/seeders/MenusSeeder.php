<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class MenusSeeder extends Seeder
{
    public function run(): void
    {
        $administrador = Role::where('name', 'Administrador')->first();
        $propietario = Role::where('name', 'Propietario')->first();

        $dashboard = $this->crear(null, 'Dashboard', 'dashboard', 'bi bi-speedometer2', 1, [$administrador, $propietario]);
        $this->crear(null, 'Reservas', 'reservas.index', 'bi bi-calendar-check', 2, [$administrador, $propietario]);
        $this->crear(null, 'Reportes', 'reportes.index', 'bi bi-bar-chart-line', 3, [$administrador, $propietario]);

        $administracion = $this->crear(null, 'Administración', null, 'bi bi-gear', 4, [$administrador]);

        $this->crear($administracion->id, 'Edificios', 'edificios.index', 'bi bi-building', 1, [$administrador]);
        $this->crear($administracion->id, 'Propietarios', 'propietarios.index', 'bi bi-person-badge', 2, [$administrador]);
        $this->crear($administracion->id, 'Departamentos', 'departamentos.index', 'bi bi-door-closed', 3, [$administrador]);
        $this->crear($administracion->id, 'Importar reservas', 'importaciones.index', 'bi bi-cloud-upload', 4, [$administrador]);
        $this->crear($administracion->id, 'Propiedades Beds24', 'beds24.propiedades', 'bi bi-diagram-3', 5, [$administrador]);
        $this->crear($administracion->id, 'Usuarios', 'usuarios.index', 'bi bi-people', 6, [$administrador]);
        $this->crear($administracion->id, 'Roles', 'roles.index', 'bi bi-shield-check', 7, [$administrador]);
        $this->crear($administracion->id, 'Permisos', 'permisos.index', 'bi bi-key', 8, [$administrador]);
        $this->crear($administracion->id, 'Menú', 'menus.index', 'bi bi-list-nested', 9, [$administrador]);
        $this->crear($administracion->id, 'Menú - Rol', 'menu-rol.index', 'bi bi-diagram-2', 10, [$administrador]);
        $this->crear($administracion->id, 'Permiso - Rol', 'permiso-rol.index', 'bi bi-shield-lock', 11, [$administrador]);
    }

    private function crear(?int $menuId, string $nombre, ?string $ruta, string $icono, int $orden, array $roles): Menu
    {
        $menu = Menu::firstOrCreate(
            ['menu_id' => $menuId, 'nombre' => $nombre],
            ['ruta' => $ruta, 'icono' => $icono, 'orden' => $orden]
        );

        $menu->roles()->syncWithoutDetaching(collect($roles)->filter()->pluck('id'));

        return $menu;
    }
}
