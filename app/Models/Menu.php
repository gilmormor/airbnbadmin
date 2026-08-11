<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Models\Role;

#[Fillable(['menu_id', 'nombre', 'ruta', 'icono', 'orden'])]
class Menu extends Model
{
    public function padre(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    public function hijos(): HasMany
    {
        return $this->hasMany(Menu::class, 'menu_id')->orderBy('orden');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'menu_rol', 'menu_id', 'rol_id');
    }

    /**
     * Árbol de ítems de menú visibles para los roles del usuario, ya anidado
     * (cada item trae su colección 'hijosArbol').
     */
    public static function arbolVisiblePara(User $user): Collection
    {
        $idsRoles = $user->roles->pluck('id');

        $visibles = static::with('roles')
            ->orderBy('orden')->orderBy('nombre')
            ->get()
            ->filter(fn (Menu $item) => $item->roles->pluck('id')->intersect($idsRoles)->isNotEmpty())
            ->values();

        return static::construirArbol($visibles, null);
    }

    /**
     * Árbol completo (sin filtrar por rol), para la pantalla de administración
     * de menús donde se pueden reordenar/reanidar los ítems.
     */
    public static function arbolCompleto(): Collection
    {
        $todos = static::orderBy('orden')->orderBy('nombre')->get();

        return static::construirArbol($todos, null);
    }

    /**
     * True si este ítem, o alguno de sus descendientes (ya cargados en
     * 'hijosArbol'), corresponde a la ruta actual — para mantener expandida
     * la rama del árbol donde está parado el usuario en cada carga de página.
     */
    public function contieneRutaActiva(): bool
    {
        if ($this->ruta && request()->routeIs($this->ruta.'*')) {
            return true;
        }

        foreach ($this->hijosArbol ?? [] as $hijo) {
            if ($hijo->contieneRutaActiva()) {
                return true;
            }
        }

        return false;
    }

    private static function construirArbol(Collection $coleccion, ?int $padreId): Collection
    {
        return $coleccion->where('menu_id', $padreId)->values()->map(function (Menu $item) use ($coleccion) {
            $item->setRelation('hijosArbol', static::construirArbol($coleccion, $item->id));

            return $item;
        });
    }
}
