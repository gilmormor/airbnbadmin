<?php

namespace App\Http\Controllers;

use App\Models\Reserva;

class ReservaController extends Controller
{
    public function index()
    {
        $reservas = Reserva::with(['departamento.edificio', 'departamento.propietario', 'plataforma'])
            ->orderByDesc('fecha_checkin')
            ->get();

        return view('reservas.index', compact('reservas'));
    }
}
