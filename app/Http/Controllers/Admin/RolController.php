<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RolRequest;
use Spatie\Permission\Models\Role;

class RolController extends Controller
{
    public function index()
    {
        $roles = Role::withCount(['users', 'permissions'])->orderBy('name')->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('admin.roles.create');
    }

    public function store(RolRequest $request)
    {
        Role::create(['name' => $request->validated('name'), 'guard_name' => 'web']);

        return redirect()->route('roles.index')->with('success', 'Rol creado correctamente.');
    }

    public function edit(Role $rol)
    {
        return view('admin.roles.edit', ['rol' => $rol]);
    }

    public function update(RolRequest $request, Role $rol)
    {
        $rol->update(['name' => $request->validated('name')]);

        return redirect()->route('roles.index')->with('success', 'Rol actualizado correctamente.');
    }

    public function destroy(Role $rol)
    {
        if ($rol->users()->count() > 0) {
            return redirect()->route('roles.index')->with('error', 'No se puede eliminar: hay usuarios con este rol asignado.');
        }

        $rol->delete();

        return redirect()->route('roles.index')->with('success', 'Rol eliminado correctamente.');
    }
}
