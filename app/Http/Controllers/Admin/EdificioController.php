<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EdificioRequest;
use App\Models\Edificio;

class EdificioController extends Controller
{
    public function index()
    {
        $edificios = Edificio::withCount('departamentos')->orderBy('nombre')->get();

        return view('admin.edificios.index', compact('edificios'));
    }

    public function create()
    {
        return view('admin.edificios.create');
    }

    public function store(EdificioRequest $request)
    {
        Edificio::create($request->validated());

        return redirect()->route('edificios.index')->with('success', 'Edificio creado correctamente.');
    }

    public function edit(Edificio $edificio)
    {
        return view('admin.edificios.edit', compact('edificio'));
    }

    public function update(EdificioRequest $request, Edificio $edificio)
    {
        $edificio->update($request->validated());

        return redirect()->route('edificios.index')->with('success', 'Edificio actualizado correctamente.');
    }

    public function destroy(Edificio $edificio)
    {
        if ($edificio->departamentos()->exists()) {
            return redirect()->route('edificios.index')->with('error', 'No se puede eliminar: el edificio tiene departamentos asociados.');
        }

        $edificio->delete();

        return redirect()->route('edificios.index')->with('success', 'Edificio eliminado correctamente.');
    }
}
