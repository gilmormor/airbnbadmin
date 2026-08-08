<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use Illuminate\Support\Facades\Date;

class DashboardController extends Controller
{
    public function index()
    {
        $inicioMes = Date::now()->startOfMonth();
        $finMes = Date::now()->endOfMonth();

        $reservasDelMes = Reserva::whereBetween('fecha_checkin', [$inicioMes, $finMes])->get();

        $proximosCheckins = Reserva::with(['departamento.edificio', 'plataforma'])
            ->where('fecha_checkin', '>=', Date::today())
            ->orderBy('fecha_checkin')
            ->limit(5)
            ->get();

        return view('dashboard', [
            'totalReservasMes' => $reservasDelMes->count(),
            'ingresoMes' => $reservasDelMes->sum('ingreso_liquido_propietario'),
            'proximosCheckins' => $proximosCheckins,
        ]);
    }
}
