<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Resena;
use App\Models\Sucursal;
use Illuminate\View\View;

/**
 * Portada del sitio público.
 *
 * Muestra la sucursal publicada con todos sus departamentos, sin importar en qué
 * edificio estén: al huésped le interesa la propiedad, no el bloque. Cuando el
 * cliente administre más de una sucursal, esta portada pasará a listarlas.
 */
class InicioController extends Controller
{
    public function index(): View
    {
        $sucursal = Sucursal::where('publicada', true)
            ->with(['empresa', 'fotos', 'edificios.departamentos' => function ($query) {
                $query->where('publicado', true)
                    ->with(['camas', 'fotos'])
                    ->orderBy('orden');
            }])
            ->orderBy('orden')
            ->firstOrFail();

        $departamentos = $sucursal->edificios
            ->flatMap->departamentos
            ->sortBy('orden')
            ->values();

        $resenas = Resena::where('publicada', true)
            ->orderBy('orden')
            ->take(6)
            ->get();

        return view('web.inicio', compact('sucursal', 'departamentos', 'resenas'));
    }
}
