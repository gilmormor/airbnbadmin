<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Import\Beds24SyncService;

class Beds24Controller extends Controller
{
    public function propiedades(Beds24SyncService $service)
    {
        $error = null;
        $propiedades = [];

        try {
            $propiedades = $service->fetchProperties();
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        }

        return view('admin.beds24.propiedades', compact('propiedades', 'error'));
    }
}
