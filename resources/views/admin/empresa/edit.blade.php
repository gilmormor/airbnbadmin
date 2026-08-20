@extends('layouts.app')

@section('page-title', 'Datos de la empresa')

@section('content')
    <div class="card">
        <div class="card-body">
            @include('partials.form-errors')

            <p class="text-secondary small">
                Datos de la empresa que opera las sucursales. Se usan en el pie del sitio
                y estarán disponibles para las facturas cuando exista el módulo de cobro.
            </p>

            <form method="POST" action="{{ route('empresa.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Razón social</label>
                        <input type="text" name="razon_social" class="form-control"
                               value="{{ old('razon_social', $empresa->razon_social) }}" required>
                        <div class="form-text">El nombre legal, tal como figura en los documentos.</div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre comercial</label>
                        <input type="text" name="nombre_comercial" class="form-control"
                               value="{{ old('nombre_comercial', $empresa->nombre_comercial) }}">
                        <div class="form-text">Si lo dejas vacío se usa la razón social.</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Identificación fiscal</label>
                        <input type="text" name="identificacion_fiscal" class="form-control"
                               value="{{ old('identificacion_fiscal', $empresa->identificacion_fiscal) }}">
                        <div class="form-text">RNC en República Dominicana, RUT en Chile.</div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control"
                               value="{{ old('telefono', $empresa->telefono) }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Correo</label>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email', $empresa->email) }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Dirección</label>
                        <input type="text" name="direccion" class="form-control"
                               value="{{ old('direccion', $empresa->direccion) }}">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Ciudad</label>
                        <input type="text" name="ciudad" class="form-control"
                               value="{{ old('ciudad', $empresa->ciudad) }}">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">País</label>
                        <select name="pais" class="form-select" required>
                            @foreach (['DO' => 'República Dominicana', 'CL' => 'Chile', 'US' => 'Estados Unidos', 'ES' => 'España'] as $codigo => $nombre)
                                <option value="{{ $codigo }}" @selected(old('pais', $empresa->pais) === $codigo)>{{ $nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Sitio web</label>
                    <input type="url" name="sitio_web" class="form-control"
                           value="{{ old('sitio_web', $empresa->sitio_web) }}" placeholder="https://">
                </div>

                <hr class="my-4">

                <div class="mb-4">
                    <label class="form-label">Icono de pestaña</label>

                    <div class="d-flex align-items-start gap-3">
                        @if ($empresa->faviconUrl())
                            {{-- Se muestra al tamaño real de la pestaña, que es donde
                                 importa que siga siendo reconocible. --}}
                            <div class="border rounded p-2 text-center" style="min-width:90px;">
                                <img src="{{ $empresa->faviconUrl() }}" alt="Icono actual" width="16" height="16">
                                <div class="text-body-secondary" style="font-size:11px;">16 px</div>
                                <img src="{{ $empresa->faviconUrl() }}" alt="" width="48" height="48" class="mt-1">
                            </div>
                        @endif

                        <div class="flex-grow-1">
                            <input type="file" name="favicon"
                                   class="form-control @error('favicon') is-invalid @enderror"
                                   accept="image/png,image/webp">
                            @error('favicon')<div class="invalid-feedback">{{ $message }}</div>@enderror

                            <div class="form-text">
                                Se usa como respaldo: el navegador muestra este icono en las
                                sucursales que no tengan uno propio cargado. PNG o WebP
                                cuadrado, idealmente 512 × 512. Como se ve a 16 píxeles,
                                conviene un símbolo simple y no el logo completo con texto.
                            </div>

                            @if ($empresa->faviconUrl())
                                <div class="form-check mt-2">
                                    <input type="hidden" name="quitar_favicon" value="0">
                                    <input type="checkbox" name="quitar_favicon" value="1"
                                           class="form-check-input" id="quitar-favicon">
                                    <label class="form-check-label" for="quitar-favicon">Quitar el icono</label>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="form-check form-switch mb-4">
                    <input type="hidden" name="mostrar_en_pie" value="0">
                    <input type="checkbox" name="mostrar_en_pie" value="1" class="form-check-input"
                           id="mostrar-en-pie" @checked(old('mostrar_en_pie', $empresa->mostrar_en_pie))>
                    <label class="form-check-label" for="mostrar-en-pie">
                        Mostrar la razón social y la identificación fiscal en el pie del sitio
                    </label>
                </div>

                <button type="submit" class="btn btn-primary">Guardar</button>
            </form>
        </div>
    </div>
@endsection
