<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MenuRequest;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $arbol = Menu::arbolCompleto();

        return view('admin.menus.index', compact('arbol'));
    }

    public function create()
    {
        return view('admin.menus.create', $this->formData());
    }

    public function store(MenuRequest $request)
    {
        Menu::create($request->validated());

        return redirect()->route('menus.index')->with('success', 'Ítem de menú creado correctamente.');
    }

    public function edit(Menu $menu)
    {
        return view('admin.menus.edit', array_merge(['menu' => $menu], $this->formData($menu)));
    }

    public function update(MenuRequest $request, Menu $menu)
    {
        $menu->update($request->validated());

        return redirect()->route('menus.index')->with('success', 'Ítem de menú actualizado correctamente.');
    }

    public function destroy(Menu $menu)
    {
        if ($menu->hijos()->exists()) {
            return redirect()->route('menus.index')->with('error', 'No se puede eliminar: este ítem tiene submenús asociados.');
        }

        $menu->delete();

        return redirect()->route('menus.index')->with('success', 'Ítem de menú eliminado correctamente.');
    }

    /**
     * Persiste el nuevo orden/anidamiento tras arrastrar y soltar en el árbol.
     * Recibe la estructura completa del árbol (ids anidados) y reescribe
     * menu_id/orden de todos los ítems para que coincidan.
     */
    public function guardarOrden(Request $request)
    {
        $data = $request->validate([
            'estructura' => ['required', 'array'],
        ]);

        $this->aplicarOrden($data['estructura'], null);

        return response()->json(['ok' => true]);
    }

    private function aplicarOrden(array $nodos, ?int $padreId): void
    {
        foreach ($nodos as $indice => $nodo) {
            Menu::where('id', $nodo['id'])->update(['menu_id' => $padreId, 'orden' => $indice]);

            if (! empty($nodo['hijos'])) {
                $this->aplicarOrden($nodo['hijos'], (int) $nodo['id']);
            }
        }
    }

    private function formData(?Menu $actual = null): array
    {
        $posibles = Menu::orderBy('nombre')->get();

        if ($actual) {
            $posibles = $posibles->reject(fn (Menu $m) => $m->id === $actual->id);
        }

        return ['menusPadre' => $posibles];
    }
}
