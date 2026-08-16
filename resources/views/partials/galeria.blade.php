{{--
    Galería de fotos, compartida por villas y departamentos.

    Parámetros:
      $modelo  instancia con relación fotos()
      $tipo    'departamento' o 'sucursal'
--}}
@php
    $fotos = $modelo->fotos()->orderBy('orden')->get();
@endphp

<div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Fotos</h5>
        <span class="text-body-secondary small">{{ $fotos->count() }} cargadas</span>
    </div>

    <div class="card-body">

        <form method="POST" action="{{ route('fotos.store', [$tipo, $modelo->id]) }}"
              enctype="multipart/form-data" class="mb-4">
            @csrf
            <div class="row g-2 align-items-end">
                <div class="col-md-8">
                    <label class="form-label">Agregar imágenes</label>
                    <input type="file" name="fotos[]" class="form-control @error('fotos.*') is-invalid @enderror"
                           accept="image/jpeg,image/png,image/webp" multiple required>
                    <div class="form-text">
                        JPG, PNG o WebP, hasta 8 MB cada una. Puedes seleccionar varias a la vez.
                    </div>
                    @error('fotos')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    @error('fotos.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload"></i> Subir
                    </button>
                </div>
            </div>
        </form>

        @if ($fotos->isEmpty())
            <p class="text-body-secondary text-center py-4 mb-0">
                Todavía no hay fotos. Mientras no cargues ninguna, el sitio público muestra un espacio vacío.
            </p>
        @else
            <p class="text-body-secondary small">
                Arrastra las fotos para cambiar su orden; se guarda automáticamente.
                La marcada como portada es la que se ve en el listado.
            </p>

            <div class="row g-3" id="galeria-fotos"
                 data-url-orden="{{ route('fotos.orden', [$tipo, $modelo->id]) }}">
                @foreach ($fotos as $foto)
                    <div class="col-md-6 col-xl-4 foto-item" data-id="{{ $foto->id }}">
                        <div class="border rounded h-100 d-flex flex-column">

                            <div class="position-relative">
                                <img src="{{ $foto->url() }}" alt="{{ $foto->alt['es'] ?? '' }}"
                                     class="w-100 rounded-top"
                                     style="aspect-ratio: 4/3; object-fit: cover; cursor: grab;">

                                @if ($foto->portada)
                                    <span class="badge text-bg-primary position-absolute top-0 start-0 m-2">
                                        Portada
                                    </span>
                                @endif

                                @if ($foto->ancho && $foto->ancho < 1200)
                                    <span class="badge text-bg-warning position-absolute top-0 end-0 m-2"
                                          title="Se verá borrosa en pantallas grandes">
                                        {{ $foto->ancho }}px
                                    </span>
                                @endif
                            </div>

                            <div class="p-2 flex-grow-1">
                                <form method="POST" action="{{ route('fotos.update', $foto) }}">
                                    @csrf
                                    @method('PATCH')

                                    <select name="categoria" class="form-select form-select-sm mb-2">
                                        <option value="">Sin categoría</option>
                                        @foreach (\App\Models\Foto::CATEGORIAS as $clave => $titulo)
                                            <option value="{{ $clave }}" @selected($foto->categoria === $clave)>
                                                {{ $titulo }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <input type="text" name="alt[es]" class="form-control form-control-sm mb-2"
                                           value="{{ $foto->alt['es'] ?? '' }}" maxlength="200"
                                           placeholder="Descripción en español">

                                    <input type="text" name="alt[en]" class="form-control form-control-sm mb-2"
                                           value="{{ $foto->alt['en'] ?? '' }}" maxlength="200"
                                           placeholder="Descripción en inglés">

                                    <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                                        Guardar datos
                                    </button>
                                </form>
                            </div>

                            <div class="p-2 pt-0 d-flex gap-2">
                                @unless ($foto->portada)
                                    <form method="POST" action="{{ route('fotos.portada', $foto) }}" class="flex-grow-1">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-secondary w-100">
                                            <i class="bi bi-star"></i> Portada
                                        </button>
                                    </form>
                                @endunless

                                <form method="POST" action="{{ route('fotos.destroy', $foto) }}"
                                      onsubmit="return confirm('¿Eliminar esta foto? No se puede deshacer.');"
                                      class="@if ($foto->portada) flex-grow-1 @endif">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const galeria = document.getElementById('galeria-fotos');

        if (!galeria || !window.Sortable) {
            return;
        }

        new Sortable(galeria, {
            animation: 150,
            draggable: '.foto-item',
            onEnd: async function () {
                const orden = Array.from(galeria.querySelectorAll('.foto-item'))
                    .map((item) => item.dataset.id);

                try {
                    const respuesta = await fetch(galeria.dataset.urlOrden, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ orden }),
                    });

                    if (!respuesta.ok) {
                        throw new Error('respuesta no ok');
                    }
                } catch (e) {
                    alert('No se pudo guardar el nuevo orden. Recarga la página e intenta de nuevo.');
                }
            },
        });
    });
</script>
@endpush
