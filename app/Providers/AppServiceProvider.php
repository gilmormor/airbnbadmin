<?php

namespace App\Providers;

use App\Models\Departamento;
use App\Models\Menu;
use App\Models\Sucursal;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // La galería es polimórfica y la comparten villas y departamentos. Con este
        // mapa la columna fotable_type guarda «departamento» en lugar del nombre
        // completo de la clase, así renombrar o mover un modelo no invalida las filas.
        Relation::morphMap([
            'departamento' => Departamento::class,
            'sucursal' => Sucursal::class,
        ]);

        View::composer('partials.sidebar', function ($view) {
            $view->with('menuArbol', auth()->check() ? Menu::arbolVisiblePara(auth()->user()) : collect());
        });
    }
}
