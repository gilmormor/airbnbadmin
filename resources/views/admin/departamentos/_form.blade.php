@include('partials.form-errors')

@php
    $d = $departamento ?? null;
    $valor = fn ($campo, $defecto = '') => old($campo, $d->$campo ?? $defecto);
    $hora = fn ($campo, $defecto = '') => substr((string) old($campo, $d->$campo ?? $defecto), 0, 5);
    $traducciones = fn ($campo) => old($campo, $d->$campo ?? []) ?: [];

    $amenidadesActuales = old('amenidades', $d?->amenidades->pluck('id')->all() ?? []);
    $destacadasActuales = old('destacadas', $d?->amenidades->where('pivot.destacada', true)->pluck('id')->all() ?? []);
    $bloquesActuales = old('bloques') ?: ($d?->bloques->map(fn ($b) => [
        'id' => $b->id,
        'antetitulo_es' => $b->antetitulo['es'] ?? '',
        'antetitulo_en' => $b->antetitulo['en'] ?? '',
        'titulo_es' => $b->titulo['es'] ?? '',
        'titulo_en' => $b->titulo['en'] ?? '',
        'cuerpo_es' => $b->cuerpo['es'] ?? '',
        'cuerpo_en' => $b->cuerpo['en'] ?? '',
        'imagen_url' => $b->imagenUrl(),
        'items' => $b->items ?? [],
    ])->all() ?? []);

    $camasActuales = old('camas') ?: ($d?->camas->map(fn ($c) => [
        'ambiente_es' => $c->ambiente['es'] ?? '',
        'ambiente_en' => $c->ambiente['en'] ?? '',
        'tipo' => $c->tipo,
        'cantidad' => $c->cantidad,
    ])->all() ?? []);

    $categorias = [
        'climatizacion' => 'Climatización',
        'cocina' => 'Cocina',
        'exterior' => 'Exterior',
        'entretenimiento' => 'Entretenimiento',
        'seguridad' => 'Seguridad',
        'servicios' => 'Servicios',
    ];
@endphp

<ul class="nav nav-tabs mb-4" role="tablist">
    @foreach ([
        'general' => 'General',
        'contenido' => 'Secciones',
        'espacio' => 'Espacio y camas',
        'precios' => 'Precios',
        'reglas' => 'Reglas',
        'amenidades' => 'Amenidades',
        'gestion' => 'Gestión interna',
    ] as $clave => $titulo)
        <li class="nav-item">
            <button class="nav-link @if ($loop->first) active @endif" data-bs-toggle="tab"
                    data-bs-target="#tab-{{ $clave }}" type="button">{{ $titulo }}</button>
        </li>
    @endforeach
</ul>

<div class="tab-content">

    {{-- ---------------------------------------------------------------- General --}}
    <div class="tab-pane fade show active" id="tab-general">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Edificio</label>
                <select name="edificio_id" class="form-select" required>
                    <option value="">Seleccione...</option>
                    @foreach ($edificios as $edificio)
                        <option value="{{ $edificio->id }}" @selected($valor('edificio_id') == $edificio->id)>
                            {{ $edificio->sucursal?->nombre }} — {{ $edificio->nombre }}
                        </option>
                    @endforeach
                </select>
                <div class="form-text">La sucursal se deduce del edificio.</div>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Propietario</label>
                <select name="propietario_id" class="form-select" required>
                    <option value="">Seleccione...</option>
                    @foreach ($propietarios as $propietario)
                        <option value="{{ $propietario->id }}" @selected($valor('propietario_id') == $propietario->id)>
                            {{ $propietario->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3 mb-3">
                <label class="form-label">Nombre interno</label>
                <input type="text" name="nombre" class="form-control" value="{{ $valor('nombre') }}" required>
                <div class="form-text">Ejemplo: Penthouse A3.</div>
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label">Dirección web</label>
                <input type="text" name="slug" class="form-control" value="{{ $valor('slug') }}">
                <div class="form-text">Se genera del nombre si lo dejas vacío.</div>
            </div>

            <div class="col-md-2 mb-3">
                <label class="form-label">Piso</label>
                <input type="number" name="piso" class="form-control" min="0" max="200" value="{{ $valor('piso') }}">
                <div class="form-text">0 = planta baja.</div>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Tipo</label>
                <select name="tipo" class="form-select" required>
                    @foreach (['departamento' => 'Departamento', 'penthouse' => 'Penthouse', 'duplex' => 'Dúplex', 'condo' => 'Condo', 'villa' => 'Villa', 'estudio' => 'Estudio'] as $clave => $titulo)
                        <option value="{{ $clave }}" @selected($valor('tipo', 'departamento') === $clave)>{{ $titulo }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        @include('partials.campo-traducible', [
            'campo' => 'titular',
            'etiqueta' => 'Título comercial',
            'valores' => $traducciones('titular'),
            'ayuda' => 'El título que ve el huésped. Ejemplo: Penthouse con vista al mar, terraza en la azotea y jacuzzi.',
            'limite' => 200,
        ])

        @include('partials.campo-traducible', [
            'campo' => 'descripcion_corta',
            'etiqueta' => 'Descripción corta',
            'valores' => $traducciones('descripcion_corta'),
            'tipo' => 'textarea',
            'filas' => 2,
            'ayuda' => 'Aparece en la tarjeta del listado.',
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
                    <input type="hidden" name="publicado" value="0">
                    <input type="checkbox" name="publicado" value="1" class="form-check-input"
                           id="publicado" @checked($valor('publicado', false))>
                    <label class="form-check-label" for="publicado">Visible en el sitio web</label>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Orden en el listado</label>
                <input type="number" name="orden" class="form-control" min="0" value="{{ $valor('orden', 0) }}" required>
            </div>
        </div>

        @include('partials.campo-traducible', [
            'campo' => 'meta_titulo',
            'etiqueta' => 'Título para buscadores',
            'valores' => $traducciones('meta_titulo'),
            'ayuda' => 'Máximo 70 caracteres. Si lo dejas vacío se usa el título comercial.',
            'limite' => 70,
        ])

        @include('partials.campo-traducible', [
            'campo' => 'meta_descripcion',
            'etiqueta' => 'Descripción para buscadores',
            'valores' => $traducciones('meta_descripcion'),
            'tipo' => 'textarea',
            'filas' => 2,
            'ayuda' => 'Máximo 160 caracteres. Es el texto que aparece bajo el título en Google.',
            'limite' => 160,
        ])
    </div>

    {{-- ---------------------------------------------------------------- Secciones --}}
    <div class="tab-pane fade" id="tab-contenido">
        <p class="text-secondary small">
            Secciones de texto que se muestran bajo la foto de portada, en el orden en
            que aparecen aquí. Cada una tiene un antetítulo pequeño, un titular grande
            y un párrafo. Puedes agregar las que necesites.
        </p>

        <div id="lista-bloques">
            @foreach ($bloquesActuales as $indice => $bloque)
                <div class="card mb-3 bloque-item">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge text-bg-secondary">
                                Sección {{ $indice + 1 }} · imagen {{ $indice % 2 === 0 ? 'a la izquierda' : 'a la derecha' }}
                            </span>
                            <button type="button" class="btn btn-sm btn-outline-danger quitar-bloque">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>

                        <input type="hidden" name="bloques[{{ $indice }}][id]" value="{{ $bloque['id'] ?? '' }}">

                        <div class="d-flex align-items-start gap-3 mb-3">
                            @if ($bloque['imagen_url'] ?? null)
                                <img src="{{ $bloque['imagen_url'] }}" alt="Imagen de la sección"
                                     class="rounded" style="width:140px; height:105px; object-fit:cover;">
                            @endif

                            <div class="flex-grow-1">
                                <label class="form-label small">Imagen de la sección</label>
                                <input type="file" name="bloques[{{ $indice }}][imagen]"
                                       class="form-control form-control-sm"
                                       accept="image/jpeg,image/png,image/webp">
                                <div class="form-text">
                                    Se muestra al costado del texto. El lado se alterna solo según el orden.
                                </div>

                                @if ($bloque['imagen_url'] ?? null)
                                    <div class="form-check mt-1">
                                        <input type="hidden" name="bloques[{{ $indice }}][quitar_imagen]" value="0">
                                        <input type="checkbox" name="bloques[{{ $indice }}][quitar_imagen]" value="1"
                                               class="form-check-input" id="quitar-imagen-{{ $indice }}">
                                        <label class="form-check-label small" for="quitar-imagen-{{ $indice }}">
                                            Quitar la imagen
                                        </label>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <p class="fw-semibold small text-secondary mb-2">ESPAÑOL</p>
                                <input type="text" name="bloques[{{ $indice }}][antetitulo_es]"
                                       class="form-control form-control-sm mb-2" placeholder="Antetítulo"
                                       value="{{ $bloque['antetitulo_es'] ?? '' }}" maxlength="120">
                                <input type="text" name="bloques[{{ $indice }}][titulo_es]"
                                       class="form-control mb-2" placeholder="Titular"
                                       value="{{ $bloque['titulo_es'] ?? '' }}" maxlength="200">
                                <textarea name="bloques[{{ $indice }}][cuerpo_es]" rows="5"
                                          class="form-control" placeholder="Párrafo"
                                          maxlength="3000">{{ $bloque['cuerpo_es'] ?? '' }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <p class="fw-semibold small text-secondary mb-2">INGLÉS</p>
                                <input type="text" name="bloques[{{ $indice }}][antetitulo_en]"
                                       class="form-control form-control-sm mb-2" placeholder="Eyebrow"
                                       value="{{ $bloque['antetitulo_en'] ?? '' }}" maxlength="120">
                                <input type="text" name="bloques[{{ $indice }}][titulo_en]"
                                       class="form-control mb-2" placeholder="Headline"
                                       value="{{ $bloque['titulo_en'] ?? '' }}" maxlength="200">
                                <textarea name="bloques[{{ $indice }}][cuerpo_en]" rows="5"
                                          class="form-control" placeholder="Body"
                                          maxlength="3000">{{ $bloque['cuerpo_en'] ?? '' }}</textarea>
                            </div>
                        </div>

                        <hr class="my-3">

                        <p class="fw-semibold small text-secondary mb-1">LISTA NUMERADA (opcional)</p>
                        <p class="text-secondary small">
                            Frases cortas que resumen lo que incluye la reserva. Se muestran
                            numeradas bajo el párrafo. No son las amenidades del catálogo:
                            estas se escriben libremente para vender.
                        </p>

                        <div class="lista-items" data-indice="{{ $indice }}">
                            @foreach ($bloque['items'] ?? [] as $posicion => $item)
                                <div class="row g-2 mb-2 item-fila">
                                    <div class="col-md-5">
                                        <input type="text" name="bloques[{{ $indice }}][items][{{ $posicion }}][es]"
                                               class="form-control form-control-sm" maxlength="300"
                                               placeholder="Español" value="{{ $item['es'] ?? '' }}">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" name="bloques[{{ $indice }}][items][{{ $posicion }}][en]"
                                               class="form-control form-control-sm" maxlength="300"
                                               placeholder="Inglés" value="{{ $item['en'] ?? '' }}">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-sm btn-outline-danger w-100 quitar-item">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" class="btn btn-sm btn-outline-secondary agregar-item">
                            <i class="bi bi-plus-lg"></i> Agregar línea
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <button type="button" class="btn btn-sm btn-outline-primary" id="agregar-bloque">
            <i class="bi bi-plus-lg"></i> Agregar sección
        </button>
    </div>

    {{-- ---------------------------------------------------------------- Espacio --}}
    <div class="tab-pane fade" id="tab-espacio">
        <div class="row">
            <div class="col-md-3 mb-3">
                <label class="form-label">Capacidad de huéspedes</label>
                <input type="number" name="capacidad_huespedes" class="form-control" min="1" max="50"
                       value="{{ $valor('capacidad_huespedes', 2) }}" required>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Dormitorios</label>
                <input type="number" name="dormitorios" class="form-control" min="0" max="20"
                       value="{{ $valor('dormitorios', 1) }}" required>
            </div>
            <div class="col-md-2 mb-3">
                <label class="form-label">Baños completos</label>
                <input type="number" name="banos_completos" class="form-control" min="0" max="20"
                       value="{{ $valor('banos_completos', 1) }}" required>
            </div>
            <div class="col-md-2 mb-3">
                <label class="form-label">Medios baños</label>
                <input type="number" name="banos_medios" class="form-control" min="0" max="20"
                       value="{{ $valor('banos_medios', 0) }}" required>
            </div>
            <div class="col-md-2 mb-3">
                <label class="form-label">Superficie (m²)</label>
                <input type="number" step="0.01" name="superficie_m2" class="form-control" min="0"
                       value="{{ $valor('superficie_m2') }}">
            </div>
        </div>

        <hr class="my-4">

        <h5>Distribución de camas</h5>
        <p class="text-secondary small">
            No es lo mismo seis huéspedes en tres camas dobles que en una doble y cuatro individuales.
            El huésped decide con este detalle.
        </p>

        <table class="table table-sm align-middle" id="tabla-camas">
            <thead>
                <tr>
                    <th style="width: 28%">Ambiente (español)</th>
                    <th style="width: 28%">Ambiente (inglés)</th>
                    <th style="width: 24%">Tipo de cama</th>
                    <th style="width: 12%">Cantidad</th>
                    <th style="width: 8%"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($camasActuales as $indice => $cama)
                    <tr>
                        <td><input type="text" name="camas[{{ $indice }}][ambiente_es]" class="form-control form-control-sm"
                                   value="{{ $cama['ambiente_es'] ?? '' }}" placeholder="Dormitorio 1"></td>
                        <td><input type="text" name="camas[{{ $indice }}][ambiente_en]" class="form-control form-control-sm"
                                   value="{{ $cama['ambiente_en'] ?? '' }}" placeholder="Bedroom 1"></td>
                        <td>
                            <select name="camas[{{ $indice }}][tipo]" class="form-select form-select-sm">
                                <option value="">—</option>
                                @foreach (\App\Models\Cama::TIPOS as $clave => $titulo)
                                    <option value="{{ $clave }}" @selected(($cama['tipo'] ?? '') === $clave)>{{ $titulo }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" name="camas[{{ $indice }}][cantidad]" class="form-control form-control-sm"
                                   min="1" max="20" value="{{ $cama['cantidad'] ?? 1 }}"></td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-danger quitar-cama">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <button type="button" class="btn btn-sm btn-outline-primary" id="agregar-cama">
            <i class="bi bi-plus-lg"></i> Agregar cama
        </button>
    </div>

    {{-- ---------------------------------------------------------------- Precios --}}
    <div class="tab-pane fade" id="tab-precios">
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Precio base por noche</label>
                <input type="number" step="0.01" name="precio_base_noche" class="form-control" min="0"
                       value="{{ $valor('precio_base_noche', 0) }}" required>
                <div class="form-text">Se usa cuando ninguna tarifa de temporada cubre la fecha.</div>
            </div>
            <div class="col-md-2 mb-3">
                <label class="form-label">Moneda</label>
                <select name="moneda" class="form-select" required>
                    @foreach (['USD', 'DOP', 'CLP', 'EUR'] as $moneda)
                        <option value="{{ $moneda }}" @selected($valor('moneda', 'USD') === $moneda)>{{ $moneda }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3 mb-3">
                <label class="form-label">Tarifa de limpieza</label>
                <input type="number" step="0.01" name="tarifa_limpieza" class="form-control" min="0"
                       value="{{ $valor('tarifa_limpieza', 0) }}" required>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Tarifa de lavandería</label>
                <input type="number" step="0.01" name="tarifa_lavanderia" class="form-control" min="0"
                       value="{{ $valor('tarifa_lavanderia', 0) }}" required>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Depósito de seguridad</label>
                <input type="number" step="0.01" name="deposito_seguridad" class="form-control" min="0"
                       value="{{ $valor('deposito_seguridad', 0) }}" required>
            </div>
        </div>

        <hr class="my-4">

        <h5>Huésped adicional</h5>
        <div class="row">
            <div class="col-md-3 mb-3">
                <label class="form-label">Huéspedes incluidos</label>
                <input type="number" name="huespedes_incluidos" class="form-control" min="1"
                       value="{{ $valor('huespedes_incluidos', 2) }}" required>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Cargo por huésped extra</label>
                <input type="number" step="0.01" name="cargo_huesped_adicional" class="form-control" min="0"
                       value="{{ $valor('cargo_huesped_adicional', 0) }}" required>
                <div class="form-text">Por noche, a partir del huésped incluido.</div>
            </div>
        </div>

        <hr class="my-4">

        <h5>Cargo especial de la unidad</h5>
        <div class="row">
            <div class="col-md-3 mb-3">
                <label class="form-label">Monto por noche</label>
                <input type="number" step="0.01" name="cargo_extra_noche" class="form-control" min="0"
                       value="{{ $valor('cargo_extra_noche', 0) }}" required>
            </div>
            <div class="col-md-9">
                @include('partials.campo-traducible', [
                    'campo' => 'cargo_extra_concepto',
                    'etiqueta' => 'Concepto del cargo',
                    'valores' => $traducciones('cargo_extra_concepto'),
                    'ayuda' => 'Ejemplo: aire central en sala y comedor, que en los penthouses cuesta 20 dólares por noche.',
                    'limite' => 200,
                ])
            </div>
        </div>

        <hr class="my-4">

        <h5>Descuentos</h5>
        <div class="row">
            <div class="col-md-3 mb-3">
                <label class="form-label">Por semana (%)</label>
                <input type="number" step="0.01" name="descuento_semanal_pct" class="form-control" min="0" max="100"
                       value="{{ $valor('descuento_semanal_pct', 0) }}" required>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Por mes (%)</label>
                <input type="number" step="0.01" name="descuento_mensual_pct" class="form-control" min="0" max="100"
                       value="{{ $valor('descuento_mensual_pct', 0) }}" required>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Por reserva directa (%)</label>
                <input type="number" step="0.01" name="descuento_directo_pct" class="form-control" min="0" max="100"
                       value="{{ $valor('descuento_directo_pct', 0) }}" required>
                <div class="form-text">Incentivo para desviar reservas desde las OTAs.</div>
            </div>
        </div>
    </div>

    {{-- ---------------------------------------------------------------- Reglas --}}
    <div class="tab-pane fade" id="tab-reglas">
        <h5>Entrada y salida</h5>
        <div class="row">
            <div class="col-md-3 mb-3">
                <label class="form-label">Entrada desde</label>
                <input type="time" name="check_in_desde" class="form-control" value="{{ $hora('check_in_desde', '15:00') }}" required>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Entrada hasta</label>
                <input type="time" name="check_in_hasta" class="form-control" value="{{ $hora('check_in_hasta') }}">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Salida hasta</label>
                <input type="time" name="check_out_hasta" class="form-control" value="{{ $hora('check_out_hasta', '12:00') }}" required>
            </div>
        </div>

        <hr class="my-4">

        <h5>Estadía</h5>
        <div class="row">
            <div class="col-md-3 mb-3">
                <label class="form-label">Noches mínimas</label>
                <input type="number" name="noches_minimas" class="form-control" min="1" value="{{ $valor('noches_minimas', 2) }}" required>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Noches máximas</label>
                <input type="number" name="noches_maximas" class="form-control" min="1" value="{{ $valor('noches_maximas') }}">
                <div class="form-text">Vacío = sin límite.</div>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Antelación mínima (días)</label>
                <input type="number" name="antelacion_minima_dias" class="form-control" min="0" value="{{ $valor('antelacion_minima_dias', 0) }}" required>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Se puede reservar hasta (meses)</label>
                <input type="number" name="ventana_reserva_meses" class="form-control" min="1" value="{{ $valor('ventana_reserva_meses', 12) }}" required>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Noches de preparación</label>
                <input type="number" name="dias_preparacion" class="form-control" min="0" value="{{ $valor('dias_preparacion', 0) }}" required>
                <div class="form-text">Se bloquean tras cada salida.</div>
            </div>
        </div>

        <hr class="my-4">

        <h5>Reglas de la casa</h5>
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="form-check form-switch">
                    <input type="hidden" name="mascotas_permitidas" value="0">
                    <input type="checkbox" name="mascotas_permitidas" value="1" class="form-check-input"
                           id="mascotas" @checked($valor('mascotas_permitidas', false))>
                    <label class="form-check-label" for="mascotas">Se permiten mascotas</label>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="form-check form-switch">
                    <input type="hidden" name="fumar_permitido" value="0">
                    <input type="checkbox" name="fumar_permitido" value="1" class="form-check-input"
                           id="fumar" @checked($valor('fumar_permitido', false))>
                    <label class="form-check-label" for="fumar">Se permite fumar</label>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="form-check form-switch">
                    <input type="hidden" name="eventos_permitidos" value="0">
                    <input type="checkbox" name="eventos_permitidos" value="1" class="form-check-input"
                           id="eventos" @checked($valor('eventos_permitidos', false))>
                    <label class="form-check-label" for="eventos">Se permiten eventos o fiestas</label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3 mb-3">
                <label class="form-label">Depósito por mascota</label>
                <input type="number" step="0.01" name="deposito_mascotas" class="form-control" min="0"
                       value="{{ $valor('deposito_mascotas', 0) }}" required>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Hora de silencio</label>
                <input type="time" name="hora_silencio" class="form-control" value="{{ $hora('hora_silencio') }}">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Edad mínima</label>
                <input type="number" name="edad_minima" class="form-control" min="0" max="99" value="{{ $valor('edad_minima') }}">
            </div>
        </div>

        @include('partials.campo-traducible', [
            'campo' => 'mascotas_condiciones',
            'etiqueta' => 'Condiciones para mascotas',
            'valores' => $traducciones('mascotas_condiciones'),
            'tipo' => 'textarea',
            'filas' => 2,
            'limite' => 500,
        ])

        @include('partials.campo-traducible', [
            'campo' => 'reglas_adicionales',
            'etiqueta' => 'Otras reglas',
            'valores' => $traducciones('reglas_adicionales'),
            'tipo' => 'textarea',
            'filas' => 4,
        ])
    </div>

    {{-- ---------------------------------------------------------------- Amenidades --}}
    <div class="tab-pane fade" id="tab-amenidades">
        <p class="text-secondary small">
            Marca las que tiene esta unidad. Las destacadas se muestran arriba en la ficha,
            antes de desplegar la lista completa.
        </p>

        <div class="row">
            @foreach ($categorias as $clave => $titulo)
                @if (($amenidadesPorCategoria[$clave] ?? collect())->isNotEmpty())
                    <div class="col-lg-6 mb-4">
                        <h6 class="border-bottom pb-2">{{ $titulo }}</h6>

                        @foreach ($amenidadesPorCategoria[$clave] as $amenidad)
                            <div class="d-flex align-items-center justify-content-between py-1">
                                <div class="form-check mb-0">
                                    <input type="checkbox" name="amenidades[]" value="{{ $amenidad->id }}"
                                           class="form-check-input" id="amenidad-{{ $amenidad->id }}"
                                           @checked(in_array($amenidad->id, $amenidadesActuales))>
                                    <label class="form-check-label" for="amenidad-{{ $amenidad->id }}">
                                        {{ $amenidad->texto('es') }}
                                    </label>
                                </div>

                                <div class="form-check form-switch mb-0">
                                    <input type="checkbox" name="destacadas[]" value="{{ $amenidad->id }}"
                                           class="form-check-input" id="destacada-{{ $amenidad->id }}"
                                           @checked(in_array($amenidad->id, $destacadasActuales))>
                                    <label class="form-check-label small text-secondary" for="destacada-{{ $amenidad->id }}">
                                        Destacar
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    {{-- ---------------------------------------------------------------- Gestión --}}
    <div class="tab-pane fade" id="tab-gestion">
        <div class="mb-3">
            <label class="form-label">% Comisión de coanfitrión</label>
            <input type="number" step="0.01" min="0" max="100" name="comision_coanfitrion_pct" class="form-control"
                   value="{{ $valor('comision_coanfitrion_pct') }}">
            <div class="form-text">
                Porcentaje que se descuenta al propietario por gestión de coanfitrión.
                Se aplica a cada reserva importada.
            </div>
        </div>

        <hr class="my-4">

        <h5>Sincronización con Beds24</h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Property ID</label>
                <input type="number" name="beds24_prop_id" class="form-control" value="{{ $valor('beds24_prop_id') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Room ID (opcional)</label>
                <input type="number" name="beds24_room_id" class="form-control" value="{{ $valor('beds24_room_id') }}">
            </div>
        </div>
        <div class="form-text mb-3">
            Necesario para que la sincronización automática asigne correctamente cada reserva a este departamento.
            Consulta <a href="{{ route('beds24.propiedades') }}" target="_blank">el listado de propiedades de tu cuenta Beds24</a>
            para obtener estos IDs.
        </div>
    </div>

</div>

<hr class="my-4">

<button type="submit" class="btn btn-primary">Guardar</button>
<a href="{{ route('departamentos.index') }}" class="btn btn-secondary">Cancelar</a>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const cuerpo = document.querySelector('#tabla-camas tbody');
        const tipos = @json(\App\Models\Cama::TIPOS);

        document.getElementById('agregar-cama').addEventListener('click', function () {
            // El índice se toma del total de filas: al enviarse, el controlador
            // reindexa con array_values, así que los huecos no importan.
            const indice = cuerpo.querySelectorAll('tr').length;
            const opciones = Object.entries(tipos)
                .map(([clave, titulo]) => `<option value="${clave}">${titulo}</option>`)
                .join('');

            const fila = document.createElement('tr');
            fila.innerHTML = `
                <td><input type="text" name="camas[${indice}][ambiente_es]" class="form-control form-control-sm" placeholder="Dormitorio 1"></td>
                <td><input type="text" name="camas[${indice}][ambiente_en]" class="form-control form-control-sm" placeholder="Bedroom 1"></td>
                <td><select name="camas[${indice}][tipo]" class="form-select form-select-sm"><option value="">—</option>${opciones}</select></td>
                <td><input type="number" name="camas[${indice}][cantidad]" class="form-control form-control-sm" min="1" max="20" value="1"></td>
                <td class="text-end"><button type="button" class="btn btn-sm btn-outline-danger quitar-cama"><i class="bi bi-trash"></i></button></td>`;

            cuerpo.appendChild(fila);
        });

        cuerpo.addEventListener('click', function (evento) {
            const boton = evento.target.closest('.quitar-cama');
            if (boton) {
                boton.closest('tr').remove();
            }
        });

        // --- Secciones de contenido ---
        const listaBloques = document.getElementById('lista-bloques');

        document.getElementById('agregar-bloque').addEventListener('click', function () {
            const indice = listaBloques.querySelectorAll('.bloque-item').length;
            const tarjeta = document.createElement('div');
            tarjeta.className = 'card mb-3 bloque-item';
            tarjeta.innerHTML = `
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="badge text-bg-secondary">Sección ${indice + 1} · imagen ${indice % 2 === 0 ? 'a la izquierda' : 'a la derecha'}</span>
                        <button type="button" class="btn btn-sm btn-outline-danger quitar-bloque"><i class="bi bi-trash"></i></button>
                    </div>
                    <input type="hidden" name="bloques[${indice}][id]" value="">
                    <div class="mb-3">
                        <label class="form-label small">Imagen de la sección</label>
                        <input type="file" name="bloques[${indice}][imagen]" class="form-control form-control-sm" accept="image/jpeg,image/png,image/webp">
                        <div class="form-text">Se muestra al costado del texto. El lado se alterna solo según el orden.</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="fw-semibold small text-secondary mb-2">ESPAÑOL</p>
                            <input type="text" name="bloques[${indice}][antetitulo_es]" class="form-control form-control-sm mb-2" placeholder="Antetítulo" maxlength="120">
                            <input type="text" name="bloques[${indice}][titulo_es]" class="form-control mb-2" placeholder="Titular" maxlength="200">
                            <textarea name="bloques[${indice}][cuerpo_es]" rows="5" class="form-control" placeholder="Párrafo" maxlength="3000"></textarea>
                        </div>
                        <div class="col-md-6">
                            <p class="fw-semibold small text-secondary mb-2">INGLÉS</p>
                            <input type="text" name="bloques[${indice}][antetitulo_en]" class="form-control form-control-sm mb-2" placeholder="Eyebrow" maxlength="120">
                            <input type="text" name="bloques[${indice}][titulo_en]" class="form-control mb-2" placeholder="Headline" maxlength="200">
                            <textarea name="bloques[${indice}][cuerpo_en]" rows="5" class="form-control" placeholder="Body" maxlength="3000"></textarea>
                        </div>
                    </div>
                    <hr class="my-3">
                    <p class="fw-semibold small text-secondary mb-1">LISTA NUMERADA (opcional)</p>
                    <div class="lista-items" data-indice="${indice}"></div>
                    <button type="button" class="btn btn-sm btn-outline-secondary agregar-item"><i class="bi bi-plus-lg"></i> Agregar línea</button>
                </div>`;
            listaBloques.appendChild(tarjeta);
        });

        listaBloques.addEventListener('click', function (evento) {
            if (evento.target.closest('.quitar-bloque')) {
                evento.target.closest('.bloque-item').remove();
                return;
            }

            if (evento.target.closest('.quitar-item')) {
                evento.target.closest('.item-fila').remove();
                return;
            }

            const agregar = evento.target.closest('.agregar-item');
            if (! agregar) {
                return;
            }

            const lista = agregar.closest('.card-body').querySelector('.lista-items');
            const indice = lista.dataset.indice;
            // La posición sale del total de filas; el controlador reindexa al
            // guardar, así que los huecos que deje una eliminación no importan.
            const posicion = lista.querySelectorAll('.item-fila').length;

            const fila = document.createElement('div');
            fila.className = 'row g-2 mb-2 item-fila';
            fila.innerHTML = `
                <div class="col-md-5"><input type="text" name="bloques[${indice}][items][${posicion}][es]" class="form-control form-control-sm" maxlength="300" placeholder="Español"></div>
                <div class="col-md-5"><input type="text" name="bloques[${indice}][items][${posicion}][en]" class="form-control form-control-sm" maxlength="300" placeholder="Inglés"></div>
                <div class="col-md-2"><button type="button" class="btn btn-sm btn-outline-danger w-100 quitar-item"><i class="bi bi-trash"></i></button></div>`;

            lista.appendChild(fila);
        });

        // Marcar una amenidad como destacada implica que está presente.
        document.querySelectorAll('input[name="destacadas[]"]').forEach(function (destacada) {
            destacada.addEventListener('change', function () {
                if (destacada.checked) {
                    const presente = document.querySelector(`input[name="amenidades[]"][value="${destacada.value}"]`);
                    if (presente) {
                        presente.checked = true;
                    }
                }
            });
        });
    });
</script>
@endpush
