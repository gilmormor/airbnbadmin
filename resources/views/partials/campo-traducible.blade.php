{{--
    Campo de texto con una pestaña por idioma.

    Parámetros:
      $campo     nombre del campo (se envía como campo[es] y campo[en])
      $etiqueta  texto de la etiqueta
      $valores   array con las traducciones actuales
      $tipo      'text' (por defecto) o 'textarea'
      $filas     filas del textarea
      $ayuda     texto de ayuda opcional
      $limite    maxlength opcional
--}}
@php
    $tipo = $tipo ?? 'text';
    $filas = $filas ?? 3;
    $valores = $valores ?? [];
    $idiomas = ['es' => 'Español', 'en' => 'Inglés'];
    $identificador = 'campo-'.str_replace('_', '-', $campo);
@endphp

<div class="mb-3">
    <label class="form-label">{{ $etiqueta }}</label>

    <ul class="nav nav-pills nav-sm mb-2" role="tablist">
        @foreach ($idiomas as $codigo => $nombre)
            <li class="nav-item">
                <button class="nav-link btn-sm py-1 px-2 @if ($loop->first) active @endif"
                        data-bs-toggle="pill"
                        data-bs-target="#{{ $identificador }}-{{ $codigo }}"
                        type="button">
                    {{ $nombre }}
                </button>
            </li>
        @endforeach
    </ul>

    <div class="tab-content">
        @foreach ($idiomas as $codigo => $nombre)
            <div class="tab-pane fade @if ($loop->first) show active @endif"
                 id="{{ $identificador }}-{{ $codigo }}">
                @if ($tipo === 'textarea')
                    <textarea name="{{ $campo }}[{{ $codigo }}]"
                              class="form-control @error($campo.'.'.$codigo) is-invalid @enderror"
                              rows="{{ $filas }}"
                              @isset($limite) maxlength="{{ $limite }}" @endisset
                              >{{ $valores[$codigo] ?? '' }}</textarea>
                @else
                    <input type="text"
                           name="{{ $campo }}[{{ $codigo }}]"
                           class="form-control @error($campo.'.'.$codigo) is-invalid @enderror"
                           value="{{ $valores[$codigo] ?? '' }}"
                           @isset($limite) maxlength="{{ $limite }}" @endisset>
                @endif

                @error($campo.'.'.$codigo)
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        @endforeach
    </div>

    @isset($ayuda)
        <div class="form-text">{{ $ayuda }}</div>
    @endisset
</div>
