<?php

use App\Http\Controllers\Web\DepartamentoController;
use App\Http\Controllers\Web\InicioController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

/*
 * Sitio público donde aterriza el huésped. Responde en la raíz del dominio;
 * el panel vive bajo /admin.
 */

Route::get('/', [InicioController::class, 'index'])->name('web.inicio');

// La sucursal va en la URL para que al incorporar más propiedades cada unidad
// quede bajo la suya, sin romper los enlaces ya publicados.
//
// La restricción excluye «admin» de forma explícita: aunque el panel se registra
// antes y gana por orden, una ruta de dos segmentos como /admin/reservas caería
// aquí si alguna vez se altera ese orden.
//
// El `[^/]+` no es opcional: un patrón propio reemplaza al de Laravel, y con `.+`
// el parámetro capturaría también las barras, tragándose URLs de cualquier
// profundidad como /admin/fotos/usuario/1.
Route::get('/{sucursal}/{departamento}', [DepartamentoController::class, 'show'])
    ->where('sucursal', '^(?!admin$)[^/]+$')
    ->name('web.departamento');

// Beds24 llama a este endpoint sin conocer el dominio del panel, así que vive aquí.
Route::post('/webhooks/beds24', [WebhookController::class, 'beds24'])->name('webhooks.beds24');
