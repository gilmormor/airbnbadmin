<?php

use App\Http\Controllers\Web\InicioController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

/*
 * Sitio público donde aterriza el huésped. Responde solo en APP_DOMINIO_WEB
 * (estadia.test en local, el dominio del cliente en producción).
 */

Route::get('/', [InicioController::class, 'index'])->name('web.inicio');

// Beds24 llama a este endpoint sin conocer el dominio del panel, así que vive aquí.
Route::post('/webhooks/beds24', [WebhookController::class, 'beds24'])->name('webhooks.beds24');
