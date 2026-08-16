@extends('web.layout')

@section('titulo', $villa->nombre.' — '.($villa->texto('titular') ?? ''))
@section('descripcion', $villa->texto('descripcion_corta'))

@section('contenido')

    <section class="relative flex min-h-[32rem] items-end bg-mar-700">
        {{-- Sin fotos cargadas todavía: se usa un degradado en lugar de una imagen rota. --}}
        <div class="absolute inset-0 bg-gradient-to-br from-mar-700 via-mar-600 to-mar-900"></div>

        <div class="relative mx-auto w-full max-w-6xl px-6 pb-20 pt-32">
            <p class="text-sm font-medium uppercase tracking-[0.2em] text-arena-300">
                {{ $villa->ciudad }}@if ($villa->provincia), {{ $villa->provincia }}@endif
            </p>
            <h1 class="mt-4 max-w-3xl text-4xl font-semibold leading-tight text-white sm:text-5xl">
                {{ $villa->texto('titular') }}
            </h1>
            <p class="mt-6 max-w-2xl text-lg leading-relaxed text-mar-50/90">
                {{ $villa->texto('descripcion_corta') }}
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-6 py-20">
        <h2 class="text-3xl font-semibold">Alojamientos</h2>
        <p class="mt-3 max-w-2xl text-mar-700/80">
            {{ $villa->departamentos->count() }} unidades independientes, cada una con entrada privada.
        </p>

        <div class="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($villa->departamentos as $departamento)
                <article class="flex flex-col overflow-hidden rounded-2xl bg-white ring-1 ring-arena-100">
                    <div class="aspect-[4/3] bg-gradient-to-br from-arena-100 to-arena-300"></div>

                    <div class="flex flex-1 flex-col p-6">
                        <p class="text-xs font-medium uppercase tracking-wider text-arena-600">
                            {{ ucfirst($departamento->tipo) }}
                        </p>
                        <h3 class="mt-2 text-xl font-semibold">{{ $departamento->nombre }}</h3>
                        <p class="mt-2 flex-1 text-sm leading-relaxed text-mar-700/80">
                            {{ $departamento->texto('titular') }}
                        </p>

                        <dl class="mt-5 grid grid-cols-3 gap-3 border-t border-arena-100 pt-5 text-center">
                            <div>
                                <dt class="text-xs text-mar-700/60">Huéspedes</dt>
                                <dd class="mt-1 font-semibold">{{ $departamento->capacidad_huespedes }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-mar-700/60">Dormitorios</dt>
                                <dd class="mt-1 font-semibold">{{ $departamento->dormitorios }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-mar-700/60">Baños</dt>
                                <dd class="mt-1 font-semibold">
                                    {{ $departamento->banos_completos + ($departamento->banos_medios ? 0.5 : 0) }}
                                </dd>
                            </div>
                        </dl>

                        @if ($departamento->camas->isNotEmpty())
                            <p class="mt-4 text-xs text-mar-700/60">
                                {{ $departamento->camas->map(fn ($cama) => $cama->cantidad.' '.\App\Models\Cama::TIPOS[$cama->tipo])->join(' · ') }}
                            </p>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    @if ($resenas->isNotEmpty())
        <section class="bg-white py-20">
            <div class="mx-auto max-w-6xl px-6">
                <h2 class="text-3xl font-semibold">Lo que dicen nuestros huéspedes</h2>

                <div class="mt-12 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($resenas as $resena)
                        <figure class="flex flex-col rounded-2xl bg-arena-50 p-6">
                            <blockquote class="flex-1 text-sm leading-relaxed text-mar-900/85">
                                “{{ $resena->comentario }}”
                            </blockquote>
                            <figcaption class="mt-5 text-sm font-medium text-arena-600">
                                {{ $resena->autor }}
                            </figcaption>
                        </figure>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

@endsection
