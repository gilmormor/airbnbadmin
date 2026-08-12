<?php

namespace App\Services\Ia;

use App\Models\Conversacion;
use App\Models\Mensaje;
use App\Services\Reportes\ReporteReservasService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Punto único de entrada para generar la respuesta del asistente.
 *
 * La IA nunca toca la base de datos directamente: solo puede llamar a la
 * herramienta `consultar_reservas`, que reutiliza ReporteReservasService
 * (el mismo servicio que usa la pantalla de Reportes) y por lo tanto respeta
 * automáticamente el scope de propietario ya existente en el sistema.
 */
class AsistenteIaService
{
    private const MODEL_POR_DEFECTO = 'claude-sonnet-5';

    private const MAX_TOKENS = 1024;

    private const MAX_RONDAS_HERRAMIENTA = 4;

    public function __construct(private ReporteReservasService $reportes) {}

    public function responder(Conversacion $conversacion): string
    {
        $apiKey = config('services.anthropic.api_key');

        if (! $apiKey) {
            return $this->guardarRespuesta(
                $conversacion,
                'El asistente de IA todavía no está conectado — falta configurar la API key de Anthropic en el servidor.'
            );
        }

        $mensajes = $this->construirHistorial($conversacion);

        try {
            for ($ronda = 0; $ronda < self::MAX_RONDAS_HERRAMIENTA; $ronda++) {
                $respuesta = Http::withHeaders([
                    'x-api-key' => $apiKey,
                    'anthropic-version' => '2023-06-01',
                    'content-type' => 'application/json',
                ])->timeout(60)->post('https://api.anthropic.com/v1/messages', [
                    'model' => config('services.anthropic.model', self::MODEL_POR_DEFECTO),
                    'max_tokens' => self::MAX_TOKENS,
                    'system' => $this->systemPrompt(),
                    'messages' => $mensajes,
                    'tools' => $this->herramientas(),
                ])->throw()->json();

                $bloques = $respuesta['content'] ?? [];
                $usoHerramienta = collect($bloques)->firstWhere('type', 'tool_use');

                if (! $usoHerramienta) {
                    $texto = collect($bloques)->where('type', 'text')->pluck('text')->implode("\n");

                    return $this->guardarRespuesta($conversacion, $texto ?: 'No obtuve una respuesta de texto, intenta reformular tu pregunta.');
                }

                $mensajes[] = ['role' => 'assistant', 'content' => $bloques];

                $resultado = $this->ejecutarHerramienta($usoHerramienta['name'], $usoHerramienta['input'] ?? []);

                $mensajes[] = [
                    'role' => 'user',
                    'content' => [[
                        'type' => 'tool_result',
                        'tool_use_id' => $usoHerramienta['id'],
                        'content' => json_encode($resultado, JSON_UNESCAPED_UNICODE),
                    ]],
                ];
            }

            return $this->guardarRespuesta($conversacion, 'No logré terminar de procesar tu pregunta después de varios intentos. Intenta ser más específico (por ejemplo, con un rango de fechas concreto).');
        } catch (\Throwable $e) {
            Log::warning('Asistente IA: fallo al llamar a la API de Anthropic.', ['error' => $e->getMessage()]);

            return $this->guardarRespuesta($conversacion, 'Ocurrió un error al conectar con el asistente de IA. Intenta de nuevo en unos minutos.');
        }
    }

    private function guardarRespuesta(Conversacion $conversacion, string $contenido): string
    {
        Mensaje::create([
            'conversacion_id' => $conversacion->id,
            'rol' => 'assistant',
            'contenido' => $contenido,
        ]);

        return $contenido;
    }

    private function construirHistorial(Conversacion $conversacion): array
    {
        return $conversacion->mensajes()
            ->orderBy('created_at')
            ->get()
            ->map(fn (Mensaje $m) => [
                'role' => $m->rol === 'assistant' ? 'assistant' : 'user',
                'content' => $m->contenido,
            ])
            ->values()
            ->all();
    }

    private function systemPrompt(): string
    {
        $rol = auth()->user()->getRoleNames()->first() ?? 'usuario';

        return 'Eres el asistente de un sistema de administración de alquileres de corto plazo (Airbnb, Booking, VRBO). '
            .'Respondes siempre en español, de forma breve y concreta. NUNCA inventes cifras: cualquier respuesta con '
            .'montos, fechas o cantidades de reservas debe basarse en el resultado de la herramienta "consultar_reservas". '
            .'Si necesitas un rango de fechas y el usuario no lo dio, pregúntaselo antes de usar la herramienta. '
            .'Si la herramienta no puede responder lo que se pregunta, dilo con honestidad en vez de adivinar. '
            .'El usuario actual tiene el rol "'.$rol.'" — los datos que devuelve la herramienta ya están filtrados '
            .'automáticamente a lo que ese usuario tiene permitido ver (si es Propietario, solo ve sus propios '
            .'departamentos), así que no necesitas preguntar por su identidad ni filtrar tú mismo por propietario.';
    }

    private function herramientas(): array
    {
        return [[
            'name' => 'consultar_reservas',
            'description' => 'Busca reservas y calcula totales (bruto, comisión de plataforma, tarifas de limpieza, '
                .'comisión de coanfitrión, ingreso líquido del propietario) para un rango de fechas, agrupado por '
                .'departamento. Los resultados ya vienen filtrados a lo que el usuario actual tiene permitido ver.',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'fecha_desde' => ['type' => 'string', 'description' => 'Fecha de inicio del rango, formato YYYY-MM-DD.'],
                    'fecha_hasta' => ['type' => 'string', 'description' => 'Fecha de fin del rango, formato YYYY-MM-DD.'],
                ],
                'required' => ['fecha_desde', 'fecha_hasta'],
            ],
        ]];
    }

    private function ejecutarHerramienta(string $nombre, array $input): array
    {
        if ($nombre !== 'consultar_reservas') {
            return ['error' => 'Herramienta desconocida.'];
        }

        $reservas = $this->reportes->buscar([
            'fecha_desde' => $input['fecha_desde'] ?? null,
            'fecha_hasta' => $input['fecha_hasta'] ?? null,
        ]);

        [$porDepartamento, $totales] = $this->reportes->resumen($reservas);

        return [
            'total_reservas' => $reservas->count(),
            'totales' => $totales,
            'por_departamento' => $porDepartamento->map(fn (array $g) => [
                'departamento' => $g['departamento']->nombre,
                'edificio' => $g['departamento']->edificio->nombre,
                'monto_bruto' => $g['monto_bruto'],
                'comision_plataforma' => $g['comision_plataforma'],
                'tarifa_limpieza' => $g['tarifa_limpieza'],
                'comision_coanfitrion' => $g['comision_coanfitrion'],
                'ingreso_liquido_propietario' => $g['ingreso_liquido_propietario'],
            ])->values(),
        ];
    }
}
