<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Departamento;
use App\Models\Foto;
use App\Models\Sucursal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Galería de fotos, compartida por villas y departamentos.
 *
 * El tipo llega en la URL, así que nunca se resuelve como nombre de clase: se
 * traduce contra esta lista blanca. De otro modo un tipo manipulado podría
 * instanciar cualquier modelo de la aplicación.
 */
class FotoController extends Controller
{
    private const TIPOS = [
        'departamento' => Departamento::class,
        'sucursal' => Sucursal::class,
    ];

    public function store(Request $request, string $tipo, int $id): RedirectResponse
    {
        $modelo = $this->resolverModelo($tipo, $id);

        $request->validate([
            'fotos' => ['required', 'array', 'max:30'],
            'fotos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
        ], [
            'fotos.required' => 'Selecciona al menos una imagen.',
            'fotos.*.image' => 'Solo se aceptan imágenes.',
            'fotos.*.max' => 'Cada imagen debe pesar menos de 8 MB.',
        ]);

        // En una galería vacía max() devuelve null, y la primera foto debe quedar
        // en la posición 0, no en la 1.
        $maximo = $modelo->fotos()->max('orden');
        $siguienteOrden = $maximo === null ? 0 : (int) $maximo + 1;
        $esPrimera = $maximo === null;
        $subidas = 0;

        foreach ($request->file('fotos') as $archivo) {
            $this->guardarArchivo($modelo, $archivo, $siguienteOrden + $subidas, $esPrimera && $subidas === 0);
            $subidas++;
        }

        return back()->with('success', $subidas === 1
            ? 'Foto cargada correctamente.'
            : "Se cargaron {$subidas} fotos correctamente.");
    }

    public function update(Request $request, Foto $foto): RedirectResponse
    {
        $datos = $request->validate([
            'alt' => ['array'],
            'alt.*' => ['nullable', 'string', 'max:200'],
            'titulo' => ['array'],
            'titulo.*' => ['nullable', 'string', 'max:200'],
            'categoria' => ['nullable', 'string', 'max:50'],
        ]);

        foreach (['alt', 'titulo'] as $traducible) {
            $valores = array_filter($datos[$traducible] ?? [], fn ($v) => filled($v));
            $datos[$traducible] = $valores ?: null;
        }

        $foto->update($datos);

        return back()->with('success', 'Datos de la foto actualizados.');
    }

    public function destroy(Foto $foto): RedirectResponse
    {
        $eraPortada = $foto->portada;
        $padre = $foto->fotable;

        Storage::disk('public')->delete($foto->ruta);
        $foto->delete();

        // La galería nunca debe quedarse sin portada mientras tenga fotos.
        if ($eraPortada && $padre) {
            $padre->fotos()->orderBy('orden')->first()?->update(['portada' => true]);
        }

        return back()->with('success', 'Foto eliminada.');
    }

    public function portada(Foto $foto): RedirectResponse
    {
        DB::transaction(function () use ($foto) {
            Foto::where('fotable_type', $foto->fotable_type)
                ->where('fotable_id', $foto->fotable_id)
                ->update(['portada' => false]);

            $foto->update(['portada' => true]);
        });

        return back()->with('success', 'Foto de portada actualizada.');
    }

    /**
     * Recibe el orden completo de la galería tras arrastrar. Se acotan los ids a
     * los de la propia galería para que no se pueda reordenar la de otro modelo.
     */
    public function guardarOrden(Request $request, string $tipo, int $id): JsonResponse
    {
        $modelo = $this->resolverModelo($tipo, $id);

        $datos = $request->validate([
            'orden' => ['required', 'array'],
            'orden.*' => ['integer'],
        ]);

        $propias = $modelo->fotos()->pluck('id')->all();

        DB::transaction(function () use ($datos, $propias) {
            foreach ($datos['orden'] as $posicion => $fotoId) {
                if (in_array((int) $fotoId, $propias, true)) {
                    Foto::where('id', $fotoId)->update(['orden' => $posicion]);
                }
            }
        });

        return response()->json(['ok' => true]);
    }

    private function guardarArchivo(Model $modelo, UploadedFile $archivo, int $orden, bool $portada): void
    {
        $nombre = Str::uuid().'.'.$archivo->extension();
        $carpeta = "fotos/{$modelo->getMorphClass()}/{$modelo->id}";

        $archivo->storeAs($carpeta, $nombre, 'public');

        // getimagesize lee la cabecera del archivo ya guardado; si falla, se dejan
        // nulas las dimensiones en vez de abortar la carga completa.
        $dimensiones = @getimagesize(Storage::disk('public')->path("{$carpeta}/{$nombre}"));

        $modelo->fotos()->create([
            'ruta' => "{$carpeta}/{$nombre}",
            'orden' => $orden,
            'portada' => $portada,
            'ancho' => $dimensiones[0] ?? null,
            'alto' => $dimensiones[1] ?? null,
        ]);
    }

    private function resolverModelo(string $tipo, int $id): Model
    {
        abort_unless(isset(self::TIPOS[$tipo]), 404);

        return self::TIPOS[$tipo]::findOrFail($id);
    }
}
