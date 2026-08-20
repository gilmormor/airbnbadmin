<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', $sucursal->nombre ?? config('app.name'))</title>
    <meta name="description" content="@yield('descripcion', '')">

    @if (isset($sucursal) && $sucursal->faviconUrl())
        {{-- El mismo archivo sirve para la pestaña y para cuando alguien guarda
             el sitio en la pantalla de inicio de su teléfono. --}}
        <link rel="icon" href="{{ $sucursal->faviconUrl() }}" sizes="any">
        <link rel="apple-touch-icon" href="{{ $sucursal->faviconUrl() }}">
    @endif

    @vite(['resources/css/web.css', 'resources/js/web.js'])
</head>
<body class="bg-arena-50 text-mar-900 antialiased">

    <header class="absolute inset-x-0 top-0 z-10">
        <nav class="mx-auto flex max-w-6xl items-center justify-between px-6 py-6">
            <a href="{{ route('web.inicio') }}" class="text-lg font-semibold tracking-wide text-white">
                @if (isset($sucursal) && $sucursal->logoUrl())
                    {{-- El nombre va en `alt` y en `title`: el primero para lectores
                         de pantalla y buscadores, el segundo para el globo al pasar
                         el ratón. Sin logo se muestra el nombre en texto. --}}
                    <img src="{{ $sucursal->logoUrl() }}"
                         alt="{{ $sucursal->nombre }}"
                         title="{{ $sucursal->nombre }}"
                         class="h-14 w-auto sm:h-20">
                @else
                    {{ $sucursal->nombre ?? config('app.name') }}
                @endif
            </a>

            @isset($sucursal)
                @if ($sucursal->whatsappDigitos())
                    <a href="https://wa.me/{{ $sucursal->whatsappDigitos() }}"
                       class="rounded-full bg-white/95 px-5 py-2 text-sm font-medium text-mar-700 transition hover:bg-white">
                        Escríbenos
                    </a>
                @endif
            @endisset
        </nav>
    </header>

    <main>
        @yield('contenido')
    </main>

    <footer class="bg-mar-900 text-mar-100">
        <div class="mx-auto max-w-6xl px-6 py-12">
            @isset($sucursal)
                <p class="text-lg font-semibold text-white">{{ $sucursal->nombre }}</p>
                <p class="mt-2 text-sm text-mar-100/80">
                    {{ $sucursal->direccion }}@if ($sucursal->ciudad), {{ $sucursal->ciudad }}@endif
                    @if ($sucursal->provincia), {{ $sucursal->provincia }}@endif
                </p>

                <div class="mt-6 flex flex-wrap gap-x-8 gap-y-2 text-sm">
                    @if ($sucursal->telefono)
                        <a href="tel:{{ preg_replace('/[^\d+]/', '', $sucursal->telefono) }}" class="hover:text-white">
                            {{ $sucursal->telefono }}
                        </a>
                    @endif
                    @if ($sucursal->email)
                        <a href="mailto:{{ $sucursal->email }}" class="hover:text-white">{{ $sucursal->email }}</a>
                    @endif
                </div>
            @endisset

            @php $empresa = $sucursal->empresa ?? null; @endphp

            @if ($empresa && $empresa->mostrar_en_pie)
                <div class="mt-8 border-t border-mar-100/15 pt-6 text-xs text-mar-100/60">
                    <p>{{ $empresa->razon_social }}</p>
                    @if ($empresa->identificacion_fiscal)
                        <p class="mt-1">{{ $empresa->identificacion_fiscal }}</p>
                    @endif
                </div>
            @endif

            <p class="mt-8 text-xs text-mar-100/50">
                &copy; {{ date('Y') }} {{ $empresa?->nombreVisible() ?? $sucursal->nombre ?? config('app.name') }}
            </p>
        </div>
    </footer>

    @isset($sucursal)
        @if ($sucursal->whatsappDigitos())
            {{-- Botón flotante presente en todas las pantallas. El texto previo
                 evita que el huésped tenga que explicar de dónde escribe. --}}
            <a href="https://wa.me/{{ $sucursal->whatsappDigitos() }}?text={{ urlencode(__('Hola, quiero consultar disponibilidad en').' '.$sucursal->nombre) }}"
               target="_blank" rel="noopener"
               aria-label="Escríbenos por WhatsApp"
               title="Escríbenos por WhatsApp"
               class="fixed bottom-5 right-5 z-40 flex h-14 w-14 items-center justify-center rounded-full bg-[#25D366] shadow-lg transition hover:scale-105 hover:bg-[#1ebe5b] motion-reduce:transition-none motion-reduce:hover:scale-100">
                <svg viewBox="0 0 24 24" class="h-8 w-8 fill-white" aria-hidden="true">
                    <path d="M17.47 14.38c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.64.07-.3-.15-1.26-.46-2.4-1.48-.89-.79-1.49-1.77-1.66-2.06-.17-.3-.02-.46.13-.6.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.61-.92-2.21-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48s1.06 2.88 1.21 3.08c.15.2 2.1 3.2 5.08 4.49.71.3 1.26.49 1.69.63.71.22 1.36.19 1.87.12.57-.09 1.76-.72 2.01-1.41.25-.7.25-1.29.17-1.42-.07-.13-.27-.2-.57-.35z"/>
                    <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.46 1.32 4.96L2 22l5.25-1.38a9.87 9.87 0 0 0 4.79 1.22h.01c5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2zm0 18.02h-.01a8.2 8.2 0 0 1-4.19-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.19 8.19 0 0 1-1.26-4.38c0-4.54 3.7-8.23 8.25-8.23a8.23 8.23 0 0 1 0 16.47z"/>
                </svg>
            </a>
        @endif
    @endisset

</body>
</html>
