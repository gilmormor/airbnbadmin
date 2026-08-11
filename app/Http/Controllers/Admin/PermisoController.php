<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PermisoRequest;
use Spatie\Permission\Models\Permission;

class PermisoController extends Controller
{
    public function index()
    {
        $permisos = Permission::withCount('roles')->orderBy('name')->get();

        return view('admin.permisos.index', compact('permisos'));
    }

    public function create()
    {
        return view('admin.permisos.create');
    }

    public function store(PermisoRequest $request)
    {
        Permission::create(['name' => $request->validated('name'), 'guard_name' => 'web']);

        return redirect()->route('permisos.index')->with('success', 'Permiso creado correctamente.');
    }

    public function edit(Permission $permiso)
    {
        return view('admin.permisos.edit', compact('permiso'));
    }

    public function update(PermisoRequest $request, Permission $permiso)
    {
        $permiso->update(['name' => $request->validated('name')]);

        return redirect()->route('permisos.index')->with('success', 'Permiso actualizado correctamente.');
    }

    public function destroy(Permission $permiso)
    {
        if ($permiso->roles()->count() > 0) {
            return redirect()->route('permisos.index')->with('error', 'No se puede eliminar: hay roles con este permiso asignado.');
        }

        $permiso->delete();

        return redirect()->route('permisos.index')->with('success', 'Permiso eliminado correctamente.');
    }
}
