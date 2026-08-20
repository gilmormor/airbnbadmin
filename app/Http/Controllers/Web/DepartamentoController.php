<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Departamento;
use Illuminate\View\View;

/**
 * Ficha pública de una unidad: galería completa, secciones de contenido,
 * distribución de camas, amenidades y reglas de la casa.
 */
class DepartamentoController extends Controller
{
    public function show(string $sucursal, string $departamento): View
    {
        $departamento = Departamento::withoutGlobalScopes()
            ->where('slug', $departamento)
            ->where('publicado', true)
            ->whereHas('edificio.sucursal', fn ($query) => $query
                ->where('slug', $sucursal)
                ->where('publicada', true))
            ->with([
                'edificio.sucursal.empresa', 'fotos', 'camas', 'amenidades',
                'bloques' => fn ($query) => $query->where('publicado', true)->orderBy('orden'),
                'resenas' => fn ($query) => $query->where('publicada', true)->orderBy('orden'),
            ])
            ->firstOrFail();

        $sucursal = $departamento->edificio->sucursal;

        // Las destacadas se muestran arriba; el resto queda agrupado por categoría
        // para no volcarle al huésped una lista de treinta ítems sin orden.
        $destacadas = $departamento->amenidades->where('pivot.destacada', true);
        $amenidadesPorCategoria = $departamento->amenidades->groupBy('categoria');

        return view('web.departamento', compact(
            'departamento', 'sucursal', 'destacadas', 'amenidadesPorCategoria'
        ));
    }
}
