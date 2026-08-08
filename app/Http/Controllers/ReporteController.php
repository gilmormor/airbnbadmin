<?php

namespace App\Http\Controllers;

use App\Exports\ReservasExport;
use App\Models\Departamento;
use App\Models\Edificio;
use App\Models\Propietario;
use App\Services\Reportes\ReporteReservasService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{
    public function index(Request $request, ReporteReservasService $service)
    {
        $filtros = $this->filtros($request);
        $reservas = collect();
        $porDepartamento = collect();
        $totales = null;

        if ($request->filled('fecha_desde') && $request->filled('fecha_hasta')) {
            $reservas = $service->buscar($filtros);
            [$porDepartamento, $totales] = $service->resumen($reservas);
        }

        return view('reportes.index', array_merge($this->formData(), [
            'filtros' => $filtros,
            'porDepartamento' => $porDepartamento,
            'totales' => $totales,
            'buscado' => $request->filled('fecha_desde') && $request->filled('fecha_hasta'),
        ]));
    }

    public function excel(Request $request, ReporteReservasService $service)
    {
        $filtros = $this->filtros($request);
        $reservas = $service->buscar($filtros);

        return Excel::download(new ReservasExport($reservas), 'reporte-reservas.xlsx');
    }

    public function pdf(Request $request, ReporteReservasService $service)
    {
        $filtros = $this->filtros($request);
        $reservas = $service->buscar($filtros);
        [$porDepartamento, $totales] = $service->resumen($reservas);

        $pdf = Pdf::loadView('reportes.pdf', compact('porDepartamento', 'totales', 'filtros'));

        return $pdf->download('reporte-reservas.pdf');
    }

    private function filtros(Request $request): array
    {
        return $request->only(['fecha_desde', 'fecha_hasta', 'edificio_id', 'departamento_id', 'propietario_id']);
    }

    private function formData(): array
    {
        if (auth()->user()->hasRole('Propietario')) {
            return [
                'edificios' => collect(),
                'departamentos' => Departamento::orderBy('nombre')->get(),
                'propietarios' => collect(),
            ];
        }

        return [
            'edificios' => Edificio::orderBy('nombre')->get(),
            'departamentos' => Departamento::withoutGlobalScopes()->orderBy('nombre')->get(),
            'propietarios' => Propietario::orderBy('nombre')->get(),
        ];
    }
}
