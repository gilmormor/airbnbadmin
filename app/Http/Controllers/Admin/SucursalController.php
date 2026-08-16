<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SucursalRequest;
use App\Models\Empresa;
use App\Models\Sucursal;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SucursalController extends Controller
{
    public function index()
    {
        $sucursales = Sucursal::withCount('edificios')->orderBy('orden')->orderBy('nombre')->get();

        return view('admin.sucursales.index', compact('sucursales'));
    }

    public function create()
    {
        return view('admin.sucursales.create');
    }

    public function store(SucursalRequest $request)
    {
        $sucursal = Sucursal::create(
            $this->atributos($request) + ['empresa_id' => Empresa::actual()->id]
        );

        $this->guardarLogo($request, $sucursal);

        return redirect()->route('sucursales.edit', $sucursal)
            ->with('success', 'Sucursal creada correctamente. Ya puedes cargarle fotos.');
    }

    public function edit(Sucursal $sucursal)
    {
        return view('admin.sucursales.edit', compact('sucursal'));
    }

    public function update(SucursalRequest $request, Sucursal $sucursal)
    {
        $sucursal->update($this->atributos($request));
        $this->guardarLogo($request, $sucursal);

        return redirect()->route('sucursales.index')->with('success', 'Sucursal actualizada correctamente.');
    }

    public function destroy(Sucursal $sucursal)
    {
        if ($sucursal->edificios()->exists()) {
            return redirect()->route('sucursales.index')
                ->with('error', 'No se puede eliminar: la sucursal tiene edificios asociados.');
        }

        $sucursal->logo_ruta and Storage::disk('public')->delete($sucursal->logo_ruta);
        $sucursal->delete();

        return redirect()->route('sucursales.index')->with('success', 'Sucursal eliminada correctamente.');
    }

    /**
     * Normaliza la casilla de publicación, que no llega cuando está desmarcada, y
     * guarda como null los textos traducibles que quedaron vacíos en todos los idiomas.
     */
    private function atributos(SucursalRequest $request): array
    {
        // `logo` y `quitar_logo` no son columnas: los gestiona guardarLogo().
        $atributos = collect($request->validated())->except(['logo', 'quitar_logo'])->all();
        $atributos['publicada'] = $request->boolean('publicada');

        foreach (['titular', 'descripcion_corta', 'descripcion_larga', 'como_llegar',
            'meta_titulo', 'meta_descripcion'] as $traducible) {
            $valores = array_filter($atributos[$traducible] ?? [], fn ($v) => filled($v));
            $atributos[$traducible] = $valores ?: null;
        }

        return $atributos;
    }

    /**
     * Reemplaza o elimina el logo. El archivo anterior se borra siempre que deja
     * de usarse, para que subir logos repetidamente no llene el disco.
     */
    private function guardarLogo(SucursalRequest $request, Sucursal $sucursal): void
    {
        $anterior = $sucursal->logo_ruta;

        if ($request->boolean('quitar_logo') && ! $request->hasFile('logo')) {
            $sucursal->update(['logo_ruta' => null]);
            $anterior and Storage::disk('public')->delete($anterior);

            return;
        }

        if (! $request->hasFile('logo')) {
            return;
        }

        $archivo = $request->file('logo');
        $ruta = $archivo->storeAs(
            "logos/{$sucursal->id}",
            Str::uuid().'.'.$archivo->extension(),
            'public'
        );

        $sucursal->update(['logo_ruta' => $ruta]);

        if ($anterior && $anterior !== $ruta) {
            Storage::disk('public')->delete($anterior);
        }
    }
}
