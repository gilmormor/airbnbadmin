<?php

namespace App\Http\Controllers;

use App\Models\Conversacion;
use App\Models\Mensaje;
use App\Services\Ia\AsistenteIaService;
use Illuminate\Http\Request;

class IaController extends Controller
{
    public function index(Request $request)
    {
        $conversaciones = Conversacion::orderByDesc('updated_at')->get();

        return view('ia.index', [
            'conversaciones' => $conversaciones,
            'conversacionActual' => null,
            'mensajes' => collect(),
        ]);
    }

    public function show(Conversacion $conversacion)
    {
        $conversaciones = Conversacion::orderByDesc('updated_at')->get();

        return view('ia.index', [
            'conversaciones' => $conversaciones,
            'conversacionActual' => $conversacion,
            'mensajes' => $conversacion->mensajes,
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

        $respuesta = $asistente->responder($conversacion);

        $conversacion->touch();

        return response()->json([
            'conversacion_id' => $conversacion->id,
            'conversacion_titulo' => $conversacion->titulo,
            'respuesta' => $respuesta,
        ]);
    }

    public function destroy(Conversacion $conversacion)
    {
        $conversacion->delete();

        return redirect()->route('ia.index')->with('success', 'Conversación eliminada.');
    }
}
