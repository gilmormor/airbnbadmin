<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepartamentoRequest;
use App\Models\Amenidad;
use App\Models\BloqueContenido;
use App\Models\Departamento;
use App\Models\Edificio;
use App\Models\Propietario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DepartamentoController extends Controller
{
    public function index()
    {
        $departamentos = Departamento::with(['edificio.sucursal', 'propietario'])
            ->withCount('fotos')
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();

        return view('admin.departamentos.index', compact('departamentos'));
    }

    public function create()
    {
        return view('admin.departamentos.create', $this->formData());
    }

    public function store(DepartamentoRequest $request)
    {
        $departamento = DB::transaction(function () use ($request) {
            $departamento = Departamento::create($this->atributos($request));
            $this->sincronizarRelaciones($departamento, $request);

            return $departamento;
        });

        return redirect()->route('departamentos.edit', $departamento)
            ->with('success', 'Departamento creado correctamente. Ya puedes cargarle fotos.');
    }

    public function edit(Departamento $departamento)
    {
        $departamento->load(['camas', 'amenidades', 'bloques']);

        return view('admin.departamentos.edit', array_merge(
            ['departamento' => $departamento],
            $this->formData()
        ));
    }

    public function update(DepartamentoRequest $request, Departamento $departamento)
    {
        DB::transaction(function () use ($request, $departamento) {
            $departamento->update($this->atributos($request));
            $this->sincronizarRelaciones($departamento, $request);
        });

        return redirect()->route('departamentos.index')
            ->with('success', 'Departamento actualizado correctamente.');
    }

    public function destroy(Departamento $departamento)
    {
        if ($departamento->reservas()->exists()) {
            return redirect()->route('departamentos.index')
                ->with('error', 'No se puede eliminar: el departamento tiene reservas asociadas.');
        }

        $departamento->delete();

        return redirect()->route('departamentos.index')
            ->with('success', 'Departamento eliminado correctamente.');
    }

    /**
     * Descarta del payload lo que no son columnas de la tabla y normaliza las
     * casillas de verificación, que no llegan cuando están desmarcadas.
     */
    private function atributos(DepartamentoRequest $request): array
    {
        $atributos = collect($request->validated())
            ->except(['amenidades', 'destacadas', 'camas', 'bloques'])
            ->all();

        foreach (['mascotas_permitidas', 'fumar_permitido', 'eventos_permitidos', 'publicado'] as $casilla) {
            $atributos[$casilla] = $request->boolean($casilla);
        }

        // Un texto traducible vacío se guarda como null y no como ['es' => null].
        foreach (['titular', 'descripcion_corta', 'descripcion_larga', 'cargo_extra_concepto',
            'mascotas_condiciones', 'reglas_adicionales', 'meta_titulo', 'meta_descripcion'] as $traducible) {
            $valores = array_filter($atributos[$traducible] ?? [], fn ($v) => filled($v));
            $atributos[$traducible] = $valores ?: null;
        }

        return $atributos;
    }

    private function sincronizarRelaciones(Departamento $departamento, DepartamentoRequest $request): void
    {
        $destacadas = collect($request->input('destacadas', []))->map(fn ($id) => (int) $id);

        $departamento->amenidades()->sync(
            collect($request->input('amenidades', []))
                ->mapWithKeys(fn ($id) => [(int) $id => ['destacada' => $destacadas->contains((int) $id)]])
                ->all()
        );

        // Las camas se reemplazan completas: son pocas por departamento y así el
        // formulario no necesita rastrear altas, bajas y reordenamientos.
        $departamento->camas()->delete();

        foreach (array_values($request->input('camas', [])) as $orden => $cama) {
            if (blank($cama['tipo'] ?? null) || blank($cama['ambiente_es'] ?? null)) {
                continue;
            }

            $departamento->camas()->create([
                'ambiente' => array_filter([
                    'es' => $cama['ambiente_es'],
                    'en' => $cama['ambiente_en'] ?? null,
                ], fn ($v) => filled($v)),
                'tipo' => $cama['tipo'],
                'cantidad' => $cama['cantidad'] ?? 1,
                'orden' => $orden,
            ]);
        }

        $this->sincronizarBloques($departamento, $request);
    }

    /**
     * A diferencia de las camas, las secciones no se borran y recrean: llevan una
     * imagen asociada que se perdería en cada guardado. Se actualizan por id y solo
     * se eliminan las que el usuario quitó del formulario.
     */
    private function sincronizarBloques(Departamento $departamento, DepartamentoRequest $request): void
    {
        $existentes = $departamento->bloques()->get()->keyBy('id');
        $conservados = [];

        foreach (array_values($request->input('bloques', [])) as $indice => $datos) {
            $traducir = fn (string $campo) => array_filter([
                'es' => $datos[$campo.'_es'] ?? null,
                'en' => $datos[$campo.'_en'] ?? null,
            ], fn ($v) => filled($v)) ?: null;

            $bloque = $existentes->get($datos['id'] ?? null);

            // Se descartan las líneas que quedaron en blanco en todos los idiomas:
            // son filas que el usuario agregó y no llegó a llenar.
            $items = collect($datos['items'] ?? [])
                ->map(fn ($item) => array_filter($item, fn ($v) => filled($v)))
                ->filter()
                ->values()
                ->all();

            // Una sección agregada y dejada vacía no se guarda. Una que solo tiene
            // lista sí: el titular y el párrafo son opcionales.
            if (! $bloque && ! $traducir('titulo') && ! $traducir('cuerpo') && ! $items) {
                continue;
            }

            $atributos = [
                'antetitulo' => $traducir('antetitulo'),
                'titulo' => $traducir('titulo'),
                'cuerpo' => $traducir('cuerpo'),
                'items' => $items ?: null,
                'orden' => $indice,
            ];

            $bloque = $bloque
                ? tap($bloque)->update($atributos)
                : $departamento->bloques()->create($atributos);

            $this->guardarImagenBloque($bloque, $request, $indice, (bool) ($datos['quitar_imagen'] ?? false));

            $conservados[] = $bloque->id;
        }

        foreach ($existentes->whereNotIn('id', $conservados) as $eliminado) {
            $eliminado->imagen_ruta and Storage::disk('public')->delete($eliminado->imagen_ruta);
            $eliminado->delete();
        }
    }

    private function guardarImagenBloque(
        BloqueContenido $bloque,
        DepartamentoRequest $request,
        int $indice,
        bool $quitar
    ): void {
        $anterior = $bloque->imagen_ruta;
        $archivo = $request->file("bloques.{$indice}.imagen");

        if (! $archivo) {
            if ($quitar && $anterior) {
                $bloque->update(['imagen_ruta' => null]);
                Storage::disk('public')->delete($anterior);
            }

            return;
        }

        $ruta = $archivo->storeAs(
            "secciones/{$bloque->bloqueable_id}",
            Str::uuid().'.'.$archivo->extension(),
            'public'
        );

        $bloque->update(['imagen_ruta' => $ruta]);

        if ($anterior && $anterior !== $ruta) {
            Storage::disk('public')->delete($anterior);
        }
    }

    private function formData(): array
    {
        return [
            'edificios' => Edificio::with('sucursal')->orderBy('nombre')->get(),
            'propietarios' => Propietario::orderBy('nombre')->get(),
            'amenidadesPorCategoria' => Amenidad::where('activa', true)
                ->orderBy('orden')
                ->get()
                ->groupBy('categoria'),
        ];
    }
}
