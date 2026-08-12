<?php

use App\Http\Controllers\Admin\Beds24Controller;
use App\Http\Controllers\Admin\DepartamentoController;
use App\Http\Controllers\Admin\EdificioController;
use App\Http\Controllers\Admin\ImportacionController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\MenuRolController;
use App\Http\Controllers\Admin\PermisoController;
use App\Http\Controllers\Admin\PermisoRolController;
use App\Http\Controllers\Admin\PropietarioController;
use App\Http\Controllers\Admin\RolController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::post('/webhooks/beds24', [WebhookController::class, 'beds24'])->name('webhooks.beds24');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('permission:reservas.ver')->group(function () {
        Route::get('reservas', [ReservaController::class, 'index'])->name('reservas.index');
    });

    Route::middleware('permission:reportes.ver')->group(function () {
        Route::get('reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('reportes/excel', [ReporteController::class, 'excel'])->name('reportes.excel');
        Route::get('reportes/pdf', [ReporteController::class, 'pdf'])->name('reportes.pdf');
    });

    Route::middleware('permission:ia.usar')->group(function () {
        Route::get('ia', [IaController::class, 'index'])->name('ia.index');
        Route::get('ia/{conversacion}', [IaController::class, 'show'])->name('ia.show');
        Route::post('ia/mensajes', [IaController::class, 'enviarMensaje'])->name('ia.mensajes');
        Route::delete('ia/{conversacion}', [IaController::class, 'destroy'])->name('ia.destroy');
    });

    Route::middleware('role:Administrador')->group(function () {
        Route::resource('edificios', EdificioController::class)->except('show');
        Route::resource('propietarios', PropietarioController::class)->except('show');
        Route::resource('departamentos', DepartamentoController::class)->except('show');
        Route::resource('usuarios', UsuarioController::class)->except('show');
        Route::get('importaciones', [ImportacionController::class, 'index'])->name('importaciones.index');
        Route::post('importaciones', [ImportacionController::class, 'store'])->name('importaciones.store');
        Route::get('beds24/propiedades', [Beds24Controller::class, 'propiedades'])->name('beds24.propiedades');

        Route::resource('roles', RolController::class)->except('show')->parameters(['roles' => 'rol']);
        Route::resource('permisos', PermisoController::class)->except('show')->parameters(['permisos' => 'permiso']);
        Route::resource('menus', MenuController::class)->except('show')->parameters(['menus' => 'menu']);
        Route::post('menus/guardar-orden', [MenuController::class, 'guardarOrden'])->name('menus.guardar-orden');

        Route::get('permiso-rol', [PermisoRolController::class, 'index'])->name('permiso-rol.index');
        Route::post('permiso-rol/toggle', [PermisoRolController::class, 'toggle'])->name('permiso-rol.toggle');

        Route::get('menu-rol', [MenuRolController::class, 'index'])->name('menu-rol.index');
        Route::post('menu-rol/toggle', [MenuRolController::class, 'toggle'])->name('menu-rol.toggle');
    });
});
