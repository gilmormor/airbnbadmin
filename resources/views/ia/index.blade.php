@extends('layouts.app')

@section('page-title', 'Asistente IA')

@section('content')
    <style>
        .chat-layout { height: calc(100vh - 220px); min-height: 420px; }
        .chat-sidebar { width: 280px; min-width: 280px; overflow-y: auto; }
        .chat-hilo { overflow-y: auto; }
        .mensaje-user { text-align: right; }
        .mensaje-user .burbuja { display: inline-block; background: var(--bs-primary); color: #fff; border-radius: .75rem; padding: .5rem .85rem; max-width: 75%; text-align: left; }
        .mensaje-assistant .burbuja { display: inline-block; background: var(--bs-tertiary-bg); border-radius: .75rem; padding: .5rem .85rem; max-width: 75%; }
        .conversacion-item.active { background: var(--bs-tertiary-bg); }
    </style>

    <div class="card">
        <div class="card-body">
            <div class="d-flex chat-layout">
                <div class="chat-sidebar border-end pe-3 me-3">
                    <a href="{{ route('ia.index') }}" class="btn btn-primary w-100 mb-3">
                        <i class="bi bi-plus-lg"></i> Nueva conversación
                    </a>
                    <div class="list-group">
                        @forelse ($conversaciones as $conv)
                            <div class="list-group-item conversacion-item d-flex align-items-center justify-content-between p-2 {{ $conversacionActual?->id === $conv->id ? 'active' : '' }}">
                                <a href="{{ route('ia.show', $conv) }}" class="text-truncate flex-grow-1 text-decoration-none text-body">
                                    {{ $conv->titulo }}
                                </a>
                                <form action="{{ route('ia.destroy', $conv) }}" method="POST" onsubmit="return confirm('¿Eliminar esta conversación?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-link text-danger p-0 ms-2">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        @empty
                            <p class="text-body-secondary small px-2">Sin conversaciones todavía.</p>
                        @endforelse
                    </div>
                </div>

                <div class="flex-grow-1 d-flex flex-column">
                    <div id="hilo-mensajes" class="chat-hilo flex-grow-1 mb-3" data-vacio="{{ $mensajes->isEmpty() ? '1' : '0' }}">
                        <p class="text-body-secondary mensaje-vacio" @if(! $mensajes->isEmpty()) style="display:none;" @endif>
                            Escribe tu primera pregunta sobre tus reservas.
                        </p>
                    </div>
                    <form id="form-mensaje" class="d-flex gap-2">
                        <input type="hidden" id="conversacion-id" value="{{ $conversacionActual?->id }}">
                        <input type="text" id="input-mensaje" class="form-control" placeholder="Escribe tu pregunta sobre tus reservas..." autocomplete="off" required>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const MENSAJES_INICIALES = @json($mensajesParaJs);
        const IA_EXPORT_BASE = '{{ url('ia/mensajes') }}';

        document.addEventListener('DOMContentLoaded', () => {
            const hilo = document.getElementById('hilo-mensajes');

            function escaparHtml(texto) {
                return texto.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            }

            function aplicarInline(texto) {
                return escaparHtml(texto).replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
            }

            function formatearMarkdown(texto) {
                const lineas = texto.split('\n');
                const bloques = [];
                let i = 0;

                while (i < lineas.length) {
                    const linea = lineas[i];

                    if (linea.includes('|') && lineas[i + 1] && /^[\s|:-]+$/.test(lineas[i + 1]) && lineas[i + 1].includes('-')) {
                        const encabezado = linea.split('|').map(c => c.trim()).filter(c => c !== '');
                        const filas = [];
                        let j = i + 2;
                        while (j < lineas.length && lineas[j].includes('|')) {
                            filas.push(lineas[j].split('|').map(c => c.trim()).filter(c => c !== ''));
                            j++;
                        }
                        let tabla = '<table class="table table-sm table-bordered mb-2"><thead><tr>';
                        encabezado.forEach(c => tabla += `<th>${aplicarInline(c)}</th>`);
                        tabla += '</tr></thead><tbody>';
                        filas.forEach(f => {
                            tabla += '<tr>';
                            f.forEach(c => tabla += `<td>${aplicarInline(c)}</td>`);
                            tabla += '</tr>';
                        });
                        tabla += '</tbody></table>';
                        bloques.push(tabla);
                        i = j;
                        continue;
                    }

                    if (/^\s*-\s+/.test(linea)) {
                        const items = [];
                        let j = i;
                        while (j < lineas.length && /^\s*-\s+/.test(lineas[j])) {
                            items.push(lineas[j].replace(/^\s*-\s+/, ''));
                            j++;
                        }
                        bloques.push('<ul class="mb-2">' + items.map(it => `<li>${aplicarInline(it)}</li>`).join('') + '</ul>');
                        i = j;
                        continue;
                    }

                    if (linea.trim() === '') {
                        i++;
                        continue;
                    }

                    bloques.push(`<p class="mb-2">${aplicarInline(linea)}</p>`);
                    i++;
                }

                return bloques.join('') || '';
            }

            function agregarBurbuja(rol, contenido, id, exportable) {
                hilo.querySelector('.mensaje-vacio')?.remove();

                const div = document.createElement('div');
                div.className = `mensaje mensaje-${rol} mb-3`;
                const burbuja = document.createElement('div');
                burbuja.className = 'burbuja';

                if (rol === 'assistant') {
                    burbuja.innerHTML = formatearMarkdown(contenido);
                } else {
                    burbuja.textContent = contenido;
                }

                div.appendChild(burbuja);

                if (rol === 'assistant' && exportable && id) {
                    const acciones = document.createElement('div');
                    acciones.className = 'mt-1';
                    acciones.innerHTML = `
                        <a href="${IA_EXPORT_BASE}/${id}/excel" class="btn btn-sm btn-outline-secondary" target="_blank" rel="noopener">
                            <i class="bi bi-file-earmark-excel"></i> Excel
                        </a>
                        <a href="${IA_EXPORT_BASE}/${id}/pdf" class="btn btn-sm btn-outline-secondary ms-1" target="_blank" rel="noopener">
                            <i class="bi bi-file-earmark-pdf"></i> PDF
                        </a>
                        <a href="${IA_EXPORT_BASE}/${id}/csv" class="btn btn-sm btn-outline-secondary ms-1" target="_blank" rel="noopener">
                            <i class="bi bi-file-earmark-text"></i> CSV
                        </a>
                    `;
                    div.appendChild(acciones);
                }

                hilo.appendChild(div);
                hilo.scrollTop = hilo.scrollHeight;
            }

            MENSAJES_INICIALES.forEach(m => agregarBurbuja(m.rol, m.contenido, m.id, m.exportable));
            hilo.scrollTop = hilo.scrollHeight;

            document.getElementById('form-mensaje').addEventListener('submit', async function (e) {
                e.preventDefault();
                const input = document.getElementById('input-mensaje');
                const mensaje = input.value.trim();
                if (!mensaje) {
                    return;
                }

                const conversacionIdInput = document.getElementById('conversacion-id');
                const esNueva = !conversacionIdInput.value;

                agregarBurbuja('user', mensaje);
                input.value = '';
                input.disabled = true;

                const token = document.querySelector('meta[name="csrf-token"]').content;

                try {
                    const response = await fetch('{{ route('ia.mensajes') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ conversacion_id: conversacionIdInput.value || null, mensaje }),
                    });

                    if (!response.ok) {
                        throw new Error('Error de servidor');
                    }

                    const data = await response.json();

                    if (esNueva) {
                        window.location.href = `/ia/${data.conversacion_id}`;
                        return;
                    }

                    agregarBurbuja('assistant', data.respuesta, data.mensaje_id, data.exportable);
                } catch (err) {
                    agregarBurbuja('assistant', 'Ocurrió un error al enviar el mensaje. Intenta de nuevo.', null, false);
                } finally {
                    input.disabled = false;
                    input.focus();
                }
            });
        });
    </script>
@endpush
