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

        // Grupo "Admin": gestión de usuarios, menú, roles y permisos.
        $admin = $this->crear(null, 'Admin', null, 'bi bi-speedometer2', 0, [$administrador]);
        $this->crear($admin->id, 'Usuarios', 'usuarios.index', 'bi bi-people', 0, [$administrador]);
        $this->crear($admin->id, 'Menú', 'menus.index', 'bi bi-list-nested', 1, [$administrador]);

        $rolesPadre = $this->crear($admin->id, 'Roles Padre', null, null, 2, [$administrador]);
        $this->crear($rolesPadre->id, 'Roles', 'roles.index', 'bi bi-shield-check', 0, [$administrador]);
        $this->crear($rolesPadre->id, 'Menú - Rol', 'menu-rol.index', 'bi bi-diagram-2', 1, [$administrador]);

        $permisosPadre = $this->crear($admin->id, 'Permisos padre', null, null, 3, [$administrador]);
        $this->crear($permisosPadre->id, 'Permisos', 'permisos.index', 'bi bi-key', 0, [$administrador]);
        $this->crear($permisosPadre->id, 'Permiso - Rol', 'permiso-rol.index', 'bi bi-shield-lock', 1, [$administrador]);

        // Navegación operativa, visible a ambos roles.
        $this->crear(null, 'Dashboard', 'dashboard', 'bi bi-speedometer2', 1, [$administrador, $propietario]);
        $this->crear(null, 'Reservas', 'reservas.index', 'bi bi-calendar-check', 2, [$administrador, $propietario]);
        $this->crear(null, 'Reportes', 'reportes.index', 'bi bi-bar-chart-line', 3, [$administrador, $propietario]);
        $this->crear(null, 'Asistente IA', 'ia.index', 'bi bi-stars', 4, [$administrador]);

        // Grupo "Administración": operación del negocio, de lo general a lo concreto
        // siguiendo la jerarquía empresa → sucursal → edificio → departamento.
        $administracion = $this->crear(null, 'Administración', null, 'bi bi-gear', 5, [$administrador]);
        $this->crear($administracion->id, 'Empresa', 'empresa.edit', 'bi bi-briefcase', 0, [$administrador]);
        $this->crear($administracion->id, 'Sucursales', 'sucursales.index', 'bi bi-geo-alt', 1, [$administrador]);
        $this->crear($administracion->id, 'Edificios', 'edificios.index', 'bi bi-building', 2, [$administrador]);
        $this->crear($administracion->id, 'Departamentos', 'departamentos.index', 'bi bi-door-closed', 3, [$administrador]);
        $this->crear($administracion->id, 'Propietarios', 'propietarios.index', 'bi bi-person-badge', 4, [$administrador]);
        $this->crear($administracion->id, 'Importar reservas', 'importaciones.index', 'bi bi-cloud-upload', 5, [$administrador]);
        $this->crear($administracion->id, 'Propiedades Beds24', 'beds24.propiedades', 'bi bi-diagram-3', 6, [$administrador]);
    }

    private function crear(?int $menuId, string $nombre, ?string $ruta, ?string $icono, int $orden, array $roles): Menu
    {
        $menu = Menu::firstOrCreate(
            ['menu_id' => $menuId, 'nombre' => $nombre],
            ['ruta' => $ruta, 'icono' => $icono, 'orden' => $orden]
        );

        $menu->roles()->syncWithoutDetaching(collect($roles)->filter()->pluck('id'));

        return $menu;
    }
}
