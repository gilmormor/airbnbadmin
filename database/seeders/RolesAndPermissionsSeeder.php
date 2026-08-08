<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'edificios.gestionar',
            'propietarios.gestionar',
            'departamentos.gestionar',
            'reservas.ver',
            'reservas.gestionar',
            'importaciones.gestionar',
            'usuarios.gestionar',
            'reportes.ver',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $administrador = Role::firstOrCreate(['name' => 'Administrador', 'guard_name' => 'web']);
        $administrador->syncPermissions($permissions);

        $propietario = Role::firstOrCreate(['name' => 'Propietario', 'guard_name' => 'web']);
        $propietario->syncPermissions(['reservas.ver', 'reportes.ver']);
    }
}
