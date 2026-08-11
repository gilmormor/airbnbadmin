<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermisoRolController extends Controller
{
    public function index(Request $request)
    {
        $todosLosRoles = Role::orderBy('name')->get();
        $idsSeleccionados = array_map('intval', $request->query('roles', []));
        $rolesSeleccionados = $todosLosRoles->whereIn('id', $idsSeleccionados)->values();

        $permisos = collect();
        if ($rolesSeleccionados->isNotEmpty()) {
            $permisos = Permission::with(['roles' => function ($query) use ($idsSeleccionados) {
                $query->whereIn('roles.id', $idsSeleccionados);
            }])->orderBy('name')->get();
        }

        return view('admin.permisos-rol.index', compact('todosLosRoles', 'rolesSeleccionados', 'permisos'));
    }

    public function toggle(Request $request)
    {
        $data = $request->validate([
            'permiso_id' => ['required', 'exists:permissions,id'],
            'rol_id' => ['required', 'exists:roles,id'],
            'checked' => ['required', 'boolean'],
        ]);

        $rol = Role::findOrFail($data['rol_id']);
        $permiso = Permission::findOrFail($data['permiso_id']);

        if ($data['checked']) {
            $rol->givePermissionTo($permiso);
        } else {
            $rol->revokePermissionTo($permiso);
        }

        return response()->json(['ok' => true]);
    }
}
