<?php

namespace App\Http\Controllers;

use App\Exports\ReservasExport;
use App\Models\Conversacion;
use App\Models\Mensaje;
use App\Services\Ia\AsistenteIaService;
use App\Services\Reportes\ReporteReservasService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Facades\Excel;

class IaController extends Controller
{
    public function index(Request $request)
    {
        $conversaciones = Conversacion::orderByDesc('updated_at')->get();

        return view('ia.index', [
            'conversaciones' => $conversaciones,
            'conversacionActual' => null,
            'mensajes' => collect(),
            'mensajesParaJs' => [],
        ]);
    }

    public function show(Conversacion $conversacion)
    {
        $conversaciones = Conversacion::orderByDesc('updated_at')->get();
        $mensajes = $conversacion->mensajes;

        return view('ia.index', [
            'conversaciones' => $conversaciones,
            'conversacionActual' => $conversacion,
            'mensajes' => $mensajes,
            'mensajesParaJs' => $mensajes->map(fn (Mensaje $m) => [
                'rol' => $m->rol,
                'contenido' => $m->contenido,
                'id' => $m->id,
                'exportable' => $this->esExportable($m),
            ])->values(),
        ]);
    }

    public function enviarMensaje(Request $request, AsistenteIaService $asistente)
    {
        $data = $request->validate([
            'conversacion_id' => ['nullable', 'exists:conversaciones,id'],
            'mensaje' => ['required', 'string', 'max:4000'],
        ]);

        $conversacion = $data['conversacion_id']
            ? Conversacion::findOrFail($data['conversacion_id'])
            : Conversacion::create(['user_id' => $request->user()->id, 'titulo' => (string) str($data['mensaje'])->limit(50)]);

        Mensaje::create([
            'conversacion_id' => $conversacion->id,
            'rol' => 'user',
            'contenido' => $data['mensaje'],
        ]);

        $mensaje = $asistente->responder($conversacion);

        $conversacion->touch();

        return response()->json([
            'conversacion_id' => $conversacion->id,
            'conversacion_titulo' => $conversacion->titulo,
            'respuesta' => $mensaje->contenido,
            'mensaje_id' => $mensaje->id,
            'exportable' => $this->esExportable($mensaje),
        ]);
    }

    public function destroy(Conversacion $conversacion)
    {
        $conversacion->delete();

        return redirect()->route('ia.index')->with('success', 'Conversación eliminada.');
    }

    public function excel(Mensaje $mensaje, ReporteReservasService $service)
    {
        $filtros = $this->filtrosDeMensaje($mensaje);
        $reservas = $service->buscar($filtros);

        return Excel::download(new ReservasExport($reservas), 'asistente-ia-reservas.xlsx');
    }

    public function csv(Mensaje $mensaje, ReporteReservasService $service)
    {
        $filtros = $this->filtrosDeMensaje($mensaje);
        $reservas = $service->buscar($filtros);

        return Excel::download(new ReservasExport($reservas), 'asistente-ia-reservas.csv', ExcelFormat::CSV);
    }

    public function pdf(Mensaje $mensaje, ReporteReservasService $service)
    {
        $filtros = $this->filtrosDeMensaje($mensaje);
        $reservas = $service->buscar($filtros);
        [$porDepartamento, $totales] = $service->resumen($reservas);

        $pdf = Pdf::loadView('reportes.pdf', compact('porDepartamento', 'totales', 'filtros'));

        return $pdf->download('asistente-ia-reservas.pdf');
    }

    private function esExportable(Mensaje $mensaje): bool
    {
        return ! empty($mensaje->metadata['fecha_desde']) && ! empty($mensaje->metadata['fecha_hasta']);
    }

    private function filtrosDeMensaje(Mensaje $mensaje): array
    {
        abort_unless($mensaje->conversacion, 404);
        abort_unless($this->esExportable($mensaje), 404);

        return [
            'fecha_desde' => $mensaje->metadata['fecha_desde'],
            'fecha_hasta' => $mensaje->metadata['fecha_hasta'],
        ];
    }
}
