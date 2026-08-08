<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Departamento;
use App\Models\Importacion;
use App\Models\Plataforma;
use App\Services\Import\CsvReservaImporter;
use Illuminate\Http\Request;

class ImportacionController extends Controller
{
    public function index()
    {
        $importaciones = Importacion::with('usuario')->latest()->limit(50)->get();
        $plataformas = Plataforma::orderBy('nombre')->get();
        $departamentos = Departamento::withoutGlobalScopes()->with('edificio')->orderBy('nombre')->get();

        return view('admin.importaciones.index', compact('importaciones', 'plataformas', 'departamentos'));
    }

    public function store(Request $request, CsvReservaImporter $importer)
    {
        $data = $request->validate([
            'plataforma_id' => ['required', 'exists:plataformas,id'],
            'departamento_id' => ['nullable', 'exists:departamentos,id'],
            'archivo' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
        ]);

        $plataforma = Plataforma::findOrFail($data['plataforma_id']);
        $departamentoDefault = $data['departamento_id'] ?? null
            ? Departamento::withoutGlobalScopes()->find($data['departamento_id'])
            : null;

        $importacion = $importer->import($request->file('archivo'), $plataforma, $departamentoDefault, $request->user());

        if ($importacion->total_error > 0 && $importacion->total_creadas === 0 && $importacion->total_actualizadas === 0) {
            return redirect()->route('importaciones.index')
                ->with('error', 'La importación no pudo procesar ninguna fila. Revise el detalle en el historial.');
        }

        return redirect()->route('importaciones.index')
            ->with('success', "Importación procesada: {$importacion->total_creadas} creadas, {$importacion->total_actualizadas} actualizadas, {$importacion->total_error} con error.");
    }
}
