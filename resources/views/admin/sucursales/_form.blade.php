@include('partials.form-errors')

@php
    $s = $sucursal ?? null;
    $valor = fn ($campo, $defecto = '') => old($campo, $s->$campo ?? $defecto);
    $traducciones = fn ($campo) => old($campo, $s->$campo ?? []) ?: [];
@endphp

<ul class="nav nav-tabs mb-4" role="tablist">
    @foreach (['general' => 'General', 'ubicacion' => 'Ubicación', 'contacto' => 'Contacto', 'seo' => 'Buscadores'] as $clave => $titulo)
        <li class="nav-item">
            <button class="nav-link @if ($loop->first) active @endif" data-bs-toggle="tab"
                    data-bs-target="#tab-sucursal-{{ $clave }}" type="button">{{ $titulo }}</button>
        </li>
    @endforeach
</ul>

<div class="tab-content">

    <div class="tab-pane fade show active" id="tab-sucursal-general">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" value="{{ $valor('nombre') }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Dirección web</label>
                <input type="text" name="slug" class="form-control" value="{{ $valor('slug') }}">
                <div class="form-text">Se genera del nombre si lo dejas vacío.</div>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label">Logo</label>

            <div class="d-flex align-items-start gap-3">
                @if ($s?->logoUrl())
                    {{-- Fondo oscuro porque en el sitio el logo va sobre la foto de
                         portada: así se ve aquí tal como lo verá el huésped. --}}
                    <div class="rounded p-3 text-center" style="background:#1c4e4e; min-width:170px;">
                        <img src="{{ $s->logoUrl() }}" alt="Logo actual" style="max-height:48px; max-width:140px;">
                    </div>
                @endif

                <div class="flex-grow-1">
                    <input type="file" name="logo" class="form-control @error('logo') is-invalid @enderror"
                           accept="image/png,image/webp,image/jpeg">
                    @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror

                    <div class="form-text">
                        Reemplaza al nombre en el encabezado del sitio. PNG con fondo
                        transparente, hasta 2 MB. Como se muestra sobre fotos, conviene
                        que sea claro o blanco.
                    </div>

                    @if ($s?->logoUrl())
                        <div class="form-check mt-2">
                            <input type="hidden" name="quitar_logo" value="0">
                            <input type="checkbox" name="quitar_logo" value="1" class="form-check-input" id="quitar-logo">
                            <label class="form-check-label" for="quitar-logo">
                                Quitar el logo y volver a mostrar el nombre en texto
                            </label>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @include('partials.campo-traducible', [
            'campo' => 'titular',
            'etiqueta' => 'Frase principal',
            'valores' => $traducciones('titular'),
            'ayuda' => 'Es lo primero que lee el huésped al entrar al sitio.',
            'limite' => 200,
        ])

        @include('partials.campo-traducible', [
            'campo' => 'descripcion_corta',
            'etiqueta' => 'Descripción corta',
            'valores' => $traducciones('descripcion_corta'),
            'tipo' => 'textarea',
            'filas' => 2,
            'limite' => 500,
        ])

        @include('partials.campo-traducible', [
            'campo' => 'descripcion_larga',
            'etiqueta' => 'Descripción completa',
            'valores' => $traducciones('descripcion_larga'),
            'tipo' => 'textarea',
            'filas' => 6,
        ])

        <hr class="my-4">

        <div class="row align-items-end">
            <div class="col-md-4 mb-3">
                <div class="form-check form-switch">
                    <input type="hidden" name="publicada" value="0">
                    <input type="checkbox" name="publicada" value="1" class="form-check-input"
                           id="publicada" @checked($valor('publicada', false))>
                    <label class="form-check-label" for="publicada">Visible en el sitio web</label>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Orden</label>
                <input type="number" name="orden" class="form-control" min="0" value="{{ $valor('orden', 0) }}" required>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="tab-sucursal-ubicacion">
        <div class="mb-3">
            <label class="form-label">Dirección</label>
            <input type="text" name="direccion" class="form-control" value="{{ $valor('direccion') }}">
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Ciudad</label>
                <input type="text" name="ciudad" class="form-control" value="{{ $valor('ciudad') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Provincia o región</label>
                <input type="text" name="provincia" class="form-control" value="{{ $valor('provincia') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">País</label>
                <select name="pais" class="form-select" required>
                    @foreach (['DO' => 'República Dominicana', 'CL' => 'Chile', 'US' => 'Estados Unidos', 'ES' => 'España'] as $codigo => $nombre)
                        <option value="{{ $codigo }}" @selected($valor('pais', 'DO') === $codigo)>{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Latitud</label>
                <input type="number" step="0.0000001" name="latitud" class="form-control" value="{{ $valor('latitud') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Longitud</label>
                <input type="number" step="0.0000001" name="longitud" class="form-control" value="{{ $valor('longitud') }}">
            </div>
            <div class="col-md-4">
                <div class="form-text mt-4">
                    Se usan para el mapa del sitio. En Google Maps, clic derecho sobre el punto
                    y copia las coordenadas.
                </div>
            </div>
        </div>

        @include('partials.campo-traducible', [
            'campo' => 'como_llegar',
            'etiqueta' => 'Cómo llegar',
            'valores' => $traducciones('como_llegar'),
            'tipo' => 'textarea',
            'filas' => 4,
            'ayuda' => 'Distancias a la playa, comercios y puntos de referencia.',
            'limite' => 1000,
        ])
    </div>

    <div class="tab-pane fade" id="tab-sucursal-contacto">
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Teléfono</label>
                <input type="text" name="telefono" class="form-control" value="{{ $valor('telefono') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">WhatsApp</label>
                <input type="text" name="whatsapp" class="form-control" value="{{ $valor('whatsapp') }}">
                <div class="form-text">
                    Con código de país. Ejemplo: +18493822222. Es el número del botón
                    flotante que aparece en todas las páginas del sitio.
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Correo de reservas</label>
                <input type="email" name="email" class="form-control" value="{{ $valor('email') }}">
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="tab-sucursal-seo">
        @include('partials.campo-traducible', [
            'campo' => 'meta_titulo',
            'etiqueta' => 'Título para buscadores',
            'valores' => $traducciones('meta_titulo'),
            'ayuda' => 'Máximo 70 caracteres.',
            'limite' => 70,
        ])

        @include('partials.campo-traducible', [
            'campo' => 'meta_descripcion',
            'etiqueta' => 'Descripción para buscadores',
            'valores' => $traducciones('meta_descripcion'),
            'tipo' => 'textarea',
            'filas' => 3,
            'ayuda' => 'Máximo 160 caracteres. Es el texto que aparece bajo el título en Google.',
            'limite' => 160,
        ])
    </div>

</div>

<hr class="my-4">

<button type="submit" class="btn btn-primary">Guardar</button>
<a href="{{ route('sucursales.index') }}" class="btn btn-secondary">Cancelar</a>
