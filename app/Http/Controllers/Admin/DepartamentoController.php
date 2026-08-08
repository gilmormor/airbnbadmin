<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepartamentoRequest;
use App\Models\Departamento;
use App\Models\Edificio;
use App\Models\Propietario;

class DepartamentoController extends Controller
{
    public function index()
    {
        $departamentos = Departamento::with(['edificio', 'propietario'])->orderBy('nombre')->get();

        return view('admin.departamentos.index', compact('departamentos'));
    }

    public function create()
    {
        return view('admin.departamentos.create', $this->formData());
    }

    public function store(DepartamentoRequest $request)
    {
        Departamento::create($request->validated());

        return redirect()->route('departamentos.index')->with('success', 'Departamento creado correctamente.');
    }

    public function edit(Departamento $departamento)
    {
        return view('admin.departamentos.edit', array_merge(['departamento' => $departamento], $this->formData()));
    }

    public function update(DepartamentoRequest $request, Departamento $departamento)
    {
        $departamento->update($request->validated());

        return redirect()->route('departamentos.index')->with('success', 'Departamento actualizado correctamente.');
    }

    public function destroy(Departamento $departamento)
    {
        if ($departamento->reservas()->exists()) {
            return redirect()->route('departamentos.index')->with('error', 'No se puede eliminar: el departamento tiene reservas asociadas.');
        }

        $departamento->delete();

        return redirect()->route('departamentos.index')->with('success', 'Departamento eliminado correctamente.');
    }

    private function formData(): array
    {
        return [
            'edificios' => Edificio::orderBy('nombre')->get(),
            'propietarios' => Propietario::orderBy('nombre')->get(),
        ];
    }
}
