<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class MenuRolController extends Controller
{
    public function index(Request $request)
    {
        $todosLosRoles = Role::orderBy('name')->get();
        $idsSeleccionados = array_map('intval', $request->query('roles', []));
        $rolesSeleccionados = $todosLosRoles->whereIn('id', $idsSeleccionados)->values();

        $arbol = collect();
        if ($rolesSeleccionados->isNotEmpty()) {
            $filtroRoles = fn ($query) => $query->whereIn('roles.id', $idsSeleccionados);
            $ordenHijos = fn ($query) => $query->orderBy('orden')->orderBy('nombre');

            $arbol = Menu::whereNull('menu_id')
                ->with([
                    'roles' => $filtroRoles,
                    'hijos' => $ordenHijos,
                    'hijos.roles' => $filtroRoles,
                    'hijos.hijos' => $ordenHijos,
                    'hijos.hijos.roles' => $filtroRoles,
                    'hijos.hijos.hijos' => $ordenHijos,
                    'hijos.hijos.hijos.roles' => $filtroRoles,
                ])
                ->orderBy('orden')->orderBy('nombre')
                ->get();
        }

        return view('admin.menu-rol.index', compact('todosLosRoles', 'rolesSeleccionados', 'arbol'));
    }

    public function toggle(Request $request)
    {
        $data = $request->validate([
            'menu_id' => ['required', 'exists:menus,id'],
            'rol_id' => ['required', 'exists:roles,id'],
            'checked' => ['required', 'boolean'],
        ]);

        $menu = Menu::findOrFail($data['menu_id']);

        if ($data['checked']) {
            $menu->roles()->syncWithoutDetaching([$data['rol_id']]);
        } else {
            $menu->roles()->detach($data['rol_id']);
        }

        return response()->json(['ok' => true]);
    }
}
