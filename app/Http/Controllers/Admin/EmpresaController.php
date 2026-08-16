<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Illuminate\Http\Request;

/**
 * Datos de la empresa. Como se espera una sola por instalación, se administra
 * como una pantalla de ajustes y no como un listado con altas y bajas.
 */
class EmpresaController extends Controller
{
    public function edit()
    {
        return view('admin.empresa.edit', ['empresa' => Empresa::actual()]);
    }

    public function update(Request $request)
    {
        $datos = $request->validate([
            'razon_social' => ['required', 'string', 'max:200'],
            'nombre_comercial' => ['nullable', 'string', 'max:200'],
            'identificacion_fiscal' => ['nullable', 'string', 'max:50'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'direccion' => ['nullable', 'string', 'max:250'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'pais' => ['required', 'string', 'size:2'],
            'sitio_web' => ['nullable', 'url', 'max:200'],
            'mostrar_en_pie' => ['boolean'],
        ], [], [
            'razon_social' => 'razón social',
            'identificacion_fiscal' => 'identificación fiscal',
            'pais' => 'país',
        ]);

        $datos['mostrar_en_pie'] = $request->boolean('mostrar_en_pie');

        Empresa::actual()->update($datos);

        return redirect()->route('empresa.edit')->with('success', 'Datos de la empresa actualizados.');
    }
}
