<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EdificioRequest;
use App\Models\Edificio;
use App\Models\Sucursal;

/**
 * Edificios: las construcciones físicas dentro de una sucursal. La marca, la
 * ubicación y el contacto se administran en la sucursal, no aquí.
 */
class EdificioController extends Controller
{
    public function index()
    {
        $edificios = Edificio::with('sucursal')
            ->withCount('departamentos')
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();

        return view('admin.edificios.index', compact('edificios'));
    }

    public function create()
    {
        return view('admin.edificios.create', $this->formData());
    }

    public function store(EdificioRequest $request)
    {
        Edificio::create($request->validated());

        return redirect()->route('edificios.index')->with('success', 'Edificio creado correctamente.');
    }

    public function edit(Edificio $edificio)
    {
        return view('admin.edificios.edit', array_merge(['edificio' => $edificio], $this->formData()));
    }

    public function update(EdificioRequest $request, Edificio $edificio)
    {
        $edificio->update($request->validated());

        return redirect()->route('edificios.index')->with('success', 'Edificio actualizado correctamente.');
    }

    public function destroy(Edificio $edificio)
    {
        if ($edificio->departamentos()->exists()) {
            return redirect()->route('edificios.index')
                ->with('error', 'No se puede eliminar: el edificio tiene departamentos asociados.');
        }

        $edificio->delete();

        return redirect()->route('edificios.index')->with('success', 'Edificio eliminado correctamente.');
    }

    private function formData(): array
    {
        return ['sucursales' => Sucursal::orderBy('nombre')->get()];
    }
}
