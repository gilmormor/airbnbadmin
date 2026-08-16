<?php

use App\Http\Controllers\Web\DepartamentoController;
use App\Http\Controllers\Web\InicioController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

/*
 * Sitio público donde aterriza el huésped. Responde solo en APP_DOMINIO_WEB
 * (estadia.test en local, el dominio del cliente en producción).
 */

Route::get('/', [InicioController::class, 'index'])->name('web.inicio');

// La sucursal va en la URL para que al incorporar más propiedades cada unidad
// quede bajo la suya, sin romper los enlaces ya publicados.
Route::get('/{sucursal}/{departamento}', [DepartamentoController::class, 'show'])
    ->name('web.departamento');

// Beds24 llama a este endpoint sin conocer el dominio del panel, así que vive aquí.
Route::post('/webhooks/beds24', [WebhookController::class, 'beds24'])->name('webhooks.beds24');
