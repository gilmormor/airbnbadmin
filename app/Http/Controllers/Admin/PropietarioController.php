<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PropietarioRequest;
use App\Models\Propietario;

class PropietarioController extends Controller
{
    public function index()
    {
        $propietarios = Propietario::withCount('departamentos')->orderBy('nombre')->get();

        return view('admin.propietarios.index', compact('propietarios'));
    }

    public function create()
    {
        return view('admin.propietarios.create');
    }

    public function store(PropietarioRequest $request)
    {
        Propietario::create($request->validated());

        return redirect()->route('propietarios.index')->with('success', 'Propietario creado correctamente.');
    }

    public function edit(Propietario $propietario)
    {
        return view('admin.propietarios.edit', compact('propietario'));
    }

    public function update(PropietarioRequest $request, Propietario $propietario)
    {
        $propietario->update($request->validated());

        return redirect()->route('propietarios.index')->with('success', 'Propietario actualizado correctamente.');
    }

    public function destroy(Propietario $propietario)
    {
        if ($propietario->departamentos()->exists() || $propietario->users()->exists()) {
            return redirect()->route('propietarios.index')->with('error', 'No se puede eliminar: el propietario tiene departamentos o usuarios asociados.');
        }

        $propietario->delete();

        return redirect()->route('propietarios.index')->with('success', 'Propietario eliminado correctamente.');
    }
}
