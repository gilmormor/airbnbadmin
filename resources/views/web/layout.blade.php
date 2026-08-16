<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', $villa->nombre ?? config('app.name'))</title>
    <meta name="description" content="@yield('descripcion', '')">
    @vite(['resources/css/web.css', 'resources/js/web.js'])
</head>
<body class="bg-arena-50 text-mar-900 antialiased">

    <header class="absolute inset-x-0 top-0 z-10">
        <nav class="mx-auto flex max-w-6xl items-center justify-between px-6 py-6">
            <a href="{{ route('web.inicio') }}" class="text-lg font-semibold tracking-wide text-white">
                {{ $villa->nombre ?? config('app.name') }}
            </a>

            @isset($villa)
                @if ($villa->whatsapp)
                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $villa->whatsapp) }}"
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
            @isset($villa)
                <p class="text-lg font-semibold text-white">{{ $villa->nombre }}</p>
                <p class="mt-2 text-sm text-mar-100/80">
                    {{ $villa->direccion }}@if ($villa->ciudad), {{ $villa->ciudad }}@endif
                    @if ($villa->provincia), {{ $villa->provincia }}@endif
                </p>

                <div class="mt-6 flex flex-wrap gap-x-8 gap-y-2 text-sm">
                    @if ($villa->telefono)
                        <a href="tel:{{ preg_replace('/[^\d+]/', '', $villa->telefono) }}" class="hover:text-white">
                            {{ $villa->telefono }}
                        </a>
                    @endif
                    @if ($villa->email)
                        <a href="mailto:{{ $villa->email }}" class="hover:text-white">{{ $villa->email }}</a>
                    @endif
                </div>
            @endisset

            <p class="mt-10 text-xs text-mar-100/50">
                &copy; {{ date('Y') }} {{ $villa->nombre ?? config('app.name') }}
            </p>
        </div>
    </footer>

</body>
</html>
