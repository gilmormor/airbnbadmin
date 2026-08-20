<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            // El icono de pestaña debe ser cuadrado o el navegador lo deforma.
            'favicon' => ['nullable', 'image', 'mimes:png,webp', 'max:1024', 'dimensions:ratio=1'],
            'quitar_favicon' => ['boolean'],
        ], [
            'favicon.dimensions' => 'El icono debe ser cuadrado: mismo ancho que alto. Se recomienda 512 × 512 píxeles.',
            'favicon.mimes' => 'El icono debe ser PNG o WebP.',
            'favicon.max' => 'El icono debe pesar menos de 1 MB.',
        ], [
            'razon_social' => 'razón social',
            'identificacion_fiscal' => 'identificación fiscal',
            'pais' => 'país',
            'favicon' => 'icono',
        ]);

        $empresa = Empresa::actual();

        // El archivo y su casilla no son columnas.
        $atributos = collect($datos)->except(['favicon', 'quitar_favicon'])->all();
        $atributos['mostrar_en_pie'] = $request->boolean('mostrar_en_pie');

        $empresa->update($atributos);
        $this->guardarFavicon($request, $empresa);

        return redirect()->route('empresa.edit')->with('success', 'Datos de la empresa actualizados.');
    }

    /**
     * Reemplaza o elimina el icono. El archivo anterior se borra siempre que deja
     * de usarse, para que subirlo repetidamente no llene el disco.
     */
    private function guardarFavicon(Request $request, Empresa $empresa): void
    {
        $anterior = $empresa->favicon_ruta;

        if ($request->boolean('quitar_favicon') && ! $request->hasFile('favicon')) {
            $empresa->update(['favicon_ruta' => null]);
            $anterior and Storage::disk('public')->delete($anterior);

            return;
        }

        if (! $request->hasFile('favicon')) {
            return;
        }

        $archivo = $request->file('favicon');
        $ruta = $archivo->storeAs(
            'favicons/empresa',
            Str::uuid().'.'.$archivo->extension(),
            'public'
        );

        $empresa->update(['favicon_ruta' => $ruta]);

        if ($anterior && $anterior !== $ruta) {
            Storage::disk('public')->delete($anterior);
        }
    }
}
