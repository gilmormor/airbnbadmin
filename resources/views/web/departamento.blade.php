@extends('web.layout')

{{-- El `?? ''` no es cosmético: con la forma de dos argumentos, un contenido nulo
     hace que Blade abra un búfer de salida esperando un @endsection que no existe. --}}
@section('titulo', $departamento->texto('meta_titulo') ?: $departamento->texto('titular').' — '.$sucursal->nombre)
@section('descripcion', ($departamento->texto('meta_descripcion') ?: $departamento->texto('descripcion_corta')) ?? '')

@section('contenido')

    @php
        $portada = $departamento->fotos->firstWhere('portada', true) ?? $departamento->fotos->first();
        $secundarias = $departamento->fotos->where('id', '!=', $portada?->id)->values();
        // La portada encabeza la galería del visor para que su índice coincida
        // con el de la foto del encabezado.
        $galeria = collect([$portada])->filter()->merge($secundarias)->values();
        $idioma = app()->getLocale();
        $categorias = [
            'climatizacion' => 'Climatización',
            'cocina' => 'Cocina',
            'exterior' => 'Exterior',
            'entretenimiento' => 'Entretenimiento',
            'seguridad' => 'Seguridad',
            'servicios' => 'Servicios',
        ];
    @endphp

    {{-- ------------------------------------------------------- Foto de encabezado --}}
    <section class="relative h-[70vh] min-h-[24rem] w-full bg-mar-900">
        @if ($portada)
            <button type="button" data-foto="0" class="group block h-full w-full" aria-label="Ampliar foto">
                <img src="{{ $portada->url() }}"
                     alt="{{ $portada->alt[$idioma] ?? $departamento->nombre }}"
                     class="h-full w-full object-cover transition group-hover:brightness-95">
            </button>
            {{-- El menú superior es blanco y flota sobre la foto: sin este velo
                 pierde legibilidad en imágenes claras. --}}
            <div class="pointer-events-none absolute inset-x-0 top-0 h-32 bg-gradient-to-b from-mar-900/70 to-transparent"></div>
        @else
            <div class="h-full w-full bg-gradient-to-br from-mar-700 to-mar-900"></div>
        @endif

        {{-- El nombre va sobre la foto, como en el sitio actual. `pointer-events-none`
             deja que el clic llegue al botón que abre el visor. --}}
        <div class="pointer-events-none absolute inset-0 flex items-center justify-center px-6">
            <h1 class="text-center text-4xl font-semibold text-white drop-shadow-lg sm:text-6xl">
                {{ $departamento->nombre }}
            </h1>
        </div>
    </section>

    {{-- ------------------------------------------------------------- Contenido --}}
    <section class="mx-auto max-w-7xl px-6 py-12">
        <div class="grid gap-12 lg:grid-cols-3">

            <div class="lg:col-span-2">
                <a href="{{ route('web.inicio') }}" class="text-sm text-mar-600 hover:underline">
                    &larr; {{ $sucursal->nombre }}
                </a>

                {{-- El nombre ya es el h1 sobre la foto; aquí va el título comercial. --}}
                <p class="mt-3 text-2xl font-semibold sm:text-3xl">
                    {{ $departamento->texto('titular') ?: $departamento->nombre }}
                </p>

                <p class="mt-4 flex flex-wrap items-center gap-x-2 gap-y-1 text-mar-700/80">
                    <span>{{ $departamento->capacidad_huespedes }} huéspedes</span>
                    <span aria-hidden="true">·</span>
                    <span>{{ $departamento->dormitorios }} dormitorios</span>
                    <span aria-hidden="true">·</span>
                    <span>{{ $departamento->banos_completos + ($departamento->banos_medios ? 0.5 : 0) }} baños</span>
                    @if ($departamento->superficie_m2)
                        <span aria-hidden="true">·</span>
                        <span>{{ (int) $departamento->superficie_m2 }} m²</span>
                    @endif
                </p>

                @if ($departamento->texto('descripcion_larga'))
                    <div class="mt-8 whitespace-pre-line leading-relaxed text-mar-900/85">
                        {{ $departamento->texto('descripcion_larga') }}
                    </div>
                @endif

                @foreach ($departamento->bloques->where('publicado', true) as $bloque)
                    @php $imagenIzquierda = $loop->index % 2 === 0; @endphp

                    <div class="revelar mt-14 grid items-center gap-8 md:grid-cols-2"
                         data-desde="{{ $imagenIzquierda ? 'izquierda' : 'derecha' }}">
                        @if ($bloque->imagenUrl())
                            <div class="@if (! $imagenIzquierda) md:order-2 @endif">
                                <img src="{{ $bloque->imagenUrl() }}"
                                     alt="{{ $bloque->texto('imagen_alt') ?? $bloque->texto('titulo') ?? '' }}"
                                     class="aspect-[4/3] w-full rounded-2xl object-cover" loading="lazy">
                            </div>
                        @endif

                        <div @class(['md:col-span-2' => ! $bloque->imagenUrl()])>
                            @if ($bloque->texto('antetitulo'))
                                <p class="flex items-center gap-3 text-sm text-arena-600">
                                    <span aria-hidden="true" class="h-px w-10 bg-arena-600"></span>
                                    {{ $bloque->texto('antetitulo') }}
                                </p>
                            @endif

                            @if ($bloque->texto('titulo'))
                                <h2 class="mt-2 text-3xl font-semibold leading-tight sm:text-4xl">
                                    {{ $bloque->texto('titulo') }}
                                </h2>
                            @endif

                            @if ($bloque->texto('cuerpo'))
                                <p class="mt-5 whitespace-pre-line leading-relaxed text-mar-900/80">
                                    {{ $bloque->texto('cuerpo') }}
                                </p>
                            @endif

                            @php $items = $bloque->itemsTraducidos($idioma); @endphp

                            @if ($items)
                                {{-- Lista ordenada de verdad: el número es contenido, no
                                     decoración, así los lectores de pantalla lo anuncian. --}}
                                <ol class="mt-6 space-y-3">
                                    @foreach ($items as $item)
                                        <li class="flex gap-4">
                                            <span class="mt-0.5 shrink-0 font-mono text-xs text-arena-600">
                                                [ {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }} ]
                                            </span>
                                            <span class="font-medium text-mar-900/85">{{ $item }}</span>
                                        </li>
                                    @endforeach
                                </ol>
                            @endif
                        </div>
                    </div>
                @endforeach

                @if ($secundarias->isNotEmpty())
                    <div class="mt-10 border-t border-arena-100 pt-8">
                        <div class="flex items-end justify-between gap-4">
                            <h2 class="text-xl font-semibold">Fotos</h2>
                            <div class="flex gap-2">
                                <button type="button" id="carrusel-anterior" aria-label="Fotos anteriores"
                                        class="rounded-full bg-white p-2 ring-1 ring-arena-100 transition hover:ring-mar-500 disabled:opacity-30">
                                    <span aria-hidden="true" class="block h-5 w-5 leading-5">&larr;</span>
                                </button>
                                <button type="button" id="carrusel-siguiente" aria-label="Fotos siguientes"
                                        class="rounded-full bg-white p-2 ring-1 ring-arena-100 transition hover:ring-mar-500 disabled:opacity-30">
                                    <span aria-hidden="true" class="block h-5 w-5 leading-5">&rarr;</span>
                                </button>
                            </div>
                        </div>

                        {{-- motion-safe: quien pidió movimiento reducido en su sistema
                             recibe un salto directo en vez de desplazamiento animado. --}}
                        <div id="carrusel" class="carrusel-fotos mt-5 flex snap-x snap-mandatory gap-3 overflow-x-auto motion-safe:scroll-smooth">
                            @foreach ($secundarias as $foto)
                                <button type="button" data-foto="{{ $loop->iteration }}"
                                        class="group w-[80%] shrink-0 snap-start sm:w-[46%] lg:w-[31%]"
                                        aria-label="Ampliar foto {{ $loop->iteration }}">
                                    <img src="{{ $foto->url() }}"
                                         alt="{{ $foto->alt[$idioma] ?? '' }}"
                                         class="aspect-[4/3] w-full rounded-xl object-cover transition group-hover:brightness-95"
                                         loading="lazy">
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($departamento->camas->isNotEmpty())
                    <div class="mt-10 border-t border-arena-100 pt-8">
                        <h2 class="text-xl font-semibold">Dónde dormirás</h2>
                        <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($departamento->camas as $cama)
                                <div class="rounded-xl bg-white p-4 ring-1 ring-arena-100">
                                    <p class="font-medium">{{ $cama->ambiente[$idioma] ?? $cama->ambiente['es'] ?? '' }}</p>
                                    <p class="mt-1 text-sm text-mar-700/70">
                                        {{ $cama->cantidad }} {{ \App\Models\Cama::TIPOS[$cama->tipo] ?? $cama->tipo }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($departamento->amenidades->isNotEmpty())
                    <div class="mt-10 border-t border-arena-100 pt-8">
                        <h2 class="text-xl font-semibold">Qué ofrece este alojamiento</h2>

                        @foreach ($categorias as $clave => $titulo)
                            @if (($amenidadesPorCategoria[$clave] ?? collect())->isNotEmpty())
                                <div class="mt-6">
                                    <h3 class="text-xs font-medium uppercase tracking-wider text-arena-600">{{ $titulo }}</h3>
                                    <ul class="mt-2 grid gap-x-8 gap-y-1 sm:grid-cols-2">
                                        @foreach ($amenidadesPorCategoria[$clave] as $amenidad)
                                            <li class="text-mar-900/85">{{ $amenidad->texto($idioma) }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif

                <div class="mt-10 border-t border-arena-100 pt-8">
                    <h2 class="text-xl font-semibold">Lo que debes saber</h2>
                    <dl class="mt-5 grid gap-x-8 gap-y-3 sm:grid-cols-2">
                        <div class="flex justify-between border-b border-arena-100 pb-2">
                            <dt class="text-mar-700/70">Entrada</dt>
                            <dd>desde las {{ substr($departamento->check_in_desde, 0, 5) }}</dd>
                        </div>
                        <div class="flex justify-between border-b border-arena-100 pb-2">
                            <dt class="text-mar-700/70">Salida</dt>
                            <dd>hasta las {{ substr($departamento->check_out_hasta, 0, 5) }}</dd>
                        </div>
                        <div class="flex justify-between border-b border-arena-100 pb-2">
                            <dt class="text-mar-700/70">Estadía mínima</dt>
                            <dd>{{ $departamento->noches_minimas }} noches</dd>
                        </div>
                        <div class="flex justify-between border-b border-arena-100 pb-2">
                            <dt class="text-mar-700/70">Mascotas</dt>
                            <dd>{{ $departamento->mascotas_permitidas ? 'Permitidas' : 'No permitidas' }}</dd>
                        </div>
                    </dl>

                    @if ($departamento->mascotas_permitidas && $departamento->texto('mascotas_condiciones'))
                        <p class="mt-4 text-sm text-mar-700/70">{{ $departamento->texto('mascotas_condiciones') }}</p>
                    @endif

                    @if ($departamento->texto('reglas_adicionales'))
                        <p class="mt-3 text-sm text-mar-700/70">{{ $departamento->texto('reglas_adicionales') }}</p>
                    @endif
                </div>
            </div>

            {{-- ------------------------------------------------------- Reserva --}}
            <aside class="lg:col-span-1">
                <div class="sticky top-6 rounded-2xl bg-white p-6 ring-1 ring-arena-100">
                    @if ($departamento->precio_base_noche > 0)
                        <p class="text-2xl font-semibold">
                            {{ $departamento->moneda }} {{ number_format($departamento->precio_base_noche, 0) }}
                            <span class="text-base font-normal text-mar-700/70">por noche</span>
                        </p>
                    @else
                        <p class="text-lg font-medium">Consulta la tarifa</p>
                    @endif

                    <dl class="mt-5 space-y-2 border-t border-arena-100 pt-5 text-sm">
                        @if ($departamento->tarifa_limpieza > 0)
                            <div class="flex justify-between">
                                <dt class="text-mar-700/70">Limpieza</dt>
                                <dd>{{ $departamento->moneda }} {{ number_format($departamento->tarifa_limpieza, 0) }}</dd>
                            </div>
                        @endif
                        @if ($departamento->cargo_extra_noche > 0 && $departamento->texto('cargo_extra_concepto'))
                            <div class="flex justify-between gap-4">
                                <dt class="text-mar-700/70">{{ $departamento->texto('cargo_extra_concepto') }}</dt>
                                <dd class="whitespace-nowrap">
                                    {{ $departamento->moneda }} {{ number_format($departamento->cargo_extra_noche, 0) }} / noche
                                </dd>
                            </div>
                        @endif
                        @if ($departamento->deposito_seguridad > 0)
                            <div class="flex justify-between">
                                <dt class="text-mar-700/70">Depósito reembolsable</dt>
                                <dd>{{ $departamento->moneda }} {{ number_format($departamento->deposito_seguridad, 0) }}</dd>
                            </div>
                        @endif
                    </dl>

                    @if ($departamento->descuento_directo_pct > 0)
                        <p class="mt-5 rounded-lg bg-mar-50 px-4 py-3 text-sm text-mar-700">
                            {{ (int) $departamento->descuento_directo_pct }}% de descuento por reservar directamente con nosotros.
                        </p>
                    @endif

                    {{-- El motor de reservas aún no existe: por ahora se deriva a WhatsApp. --}}
                    @if ($sucursal->whatsapp)
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $sucursal->whatsapp) }}?text={{ urlencode('Hola, quiero consultar disponibilidad para '.$departamento->nombre) }}"
                           class="mt-6 block rounded-full bg-mar-600 px-6 py-3 text-center font-medium text-white transition hover:bg-mar-700">
                            Consultar disponibilidad
                        </a>
                    @endif
                </div>
            </aside>

        </div>
    </section>

    {{-- ------------------------------------------------------------- Visor --}}
    @if ($departamento->fotos->isNotEmpty())
        <div id="visor" hidden
             class="fixed inset-0 z-50 flex items-center justify-center bg-mar-900/95 p-4">
            <button type="button" id="visor-cerrar" aria-label="Cerrar"
                    class="absolute right-4 top-4 rounded-full bg-white/10 px-4 py-2 text-white hover:bg-white/20">
                Cerrar
            </button>
            <button type="button" id="visor-anterior" aria-label="Anterior"
                    class="absolute left-4 rounded-full bg-white/10 px-4 py-3 text-white hover:bg-white/20">
                &larr;
            </button>
            {{-- Sin atributo src: un src vacío hace que el navegador pida de nuevo
                 la propia página. La imagen se asigna al abrir el visor. --}}
            <img id="visor-imagen" alt="" class="max-h-[85vh] max-w-full rounded-lg object-contain">
            <button type="button" id="visor-siguiente" aria-label="Siguiente"
                    class="absolute right-4 bottom-1/2 rounded-full bg-white/10 px-4 py-3 text-white hover:bg-white/20">
                &rarr;
            </button>
        </div>

        <script>
            (function () {
                const fotos = @json($galeria->map(fn ($f) => [
                    'url' => $f->url(),
                    'alt' => $f->alt[$idioma] ?? '',
                ])->values());

                const visor = document.getElementById('visor');
                const imagen = document.getElementById('visor-imagen');
                let actual = 0;

                function mostrar(indice) {
                    actual = (indice + fotos.length) % fotos.length;
                    imagen.src = fotos[actual].url;
                    imagen.alt = fotos[actual].alt;
                }

                function abrir(indice) {
                    mostrar(indice);
                    visor.hidden = false;
                    document.body.style.overflow = 'hidden';
                }

                function cerrar() {
                    visor.hidden = true;
                    document.body.style.overflow = '';
                }

                document.querySelectorAll('[data-foto]').forEach(function (boton) {
                    boton.addEventListener('click', () => abrir(Number(boton.dataset.foto)));
                });

                document.getElementById('visor-cerrar').addEventListener('click', cerrar);
                document.getElementById('visor-anterior').addEventListener('click', () => mostrar(actual - 1));
                document.getElementById('visor-siguiente').addEventListener('click', () => mostrar(actual + 1));
                visor.addEventListener('click', (e) => { if (e.target === visor) cerrar(); });

                document.addEventListener('keydown', function (e) {
                    if (visor.hidden) return;
                    if (e.key === 'Escape') cerrar();
                    if (e.key === 'ArrowLeft') mostrar(actual - 1);
                    if (e.key === 'ArrowRight') mostrar(actual + 1);
                });

                // --- Carrusel ---
                const carrusel = document.getElementById('carrusel');

                if (carrusel) {
                    const anterior = document.getElementById('carrusel-anterior');
                    const siguiente = document.getElementById('carrusel-siguiente');

                    function desplazar(direccion) {
                        // Avanza una foto completa, tomando el ancho real de la
                        // primera para no depender del punto de quiebre activo.
                        // El modo de desplazamiento lo decide el CSS (motion-safe),
                        // así que aquí no se fuerza `behavior`.
                        const paso = carrusel.firstElementChild.offsetWidth + 12;
                        carrusel.scrollBy({ left: paso * direccion });
                    }

                    function actualizarBotones() {
                        const margen = 4;
                        anterior.disabled = carrusel.scrollLeft <= margen;
                        siguiente.disabled =
                            carrusel.scrollLeft + carrusel.clientWidth >= carrusel.scrollWidth - margen;
                    }

                    anterior.addEventListener('click', () => desplazar(-1));
                    siguiente.addEventListener('click', () => desplazar(1));
                    carrusel.addEventListener('scroll', actualizarBotones, { passive: true });
                    window.addEventListener('resize', actualizarBotones);
                    actualizarBotones();
                }
            })();
        </script>
    @endif

@endsection
