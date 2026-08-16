<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Edificio;
use App\Models\Resena;
use Illuminate\View\View;

/**
 * Portada del sitio público.
 *
 * Muestra la villa publicada junto con sus unidades. Cuando el cliente administre
 * más de una villa, esta portada pasará a listarlas y cada una tendrá su página.
 */
class InicioController extends Controller
{
    public function index(): View
    {
        $villa = Edificio::where('publicada', true)
            ->with(['departamentos' => function ($query) {
                $query->where('publicado', true)
                    ->with('camas')
                    ->orderBy('orden');
            }])
            ->orderBy('orden')
            ->firstOrFail();

        $resenas = Resena::where('publicada', true)
            ->orderBy('orden')
            ->take(6)
            ->get();

        return view('web.inicio', compact('villa', 'resenas'));
    }
}
