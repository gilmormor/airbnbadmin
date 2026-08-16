@extends('layouts.app')

@section('page-title', 'Sucursales')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Sucursales</h5>
            <a href="{{ route('sucursales.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Nueva sucursal
            </a>
        </div>
        <div class="card-body">
            <table id="tabla-sucursales" class="table table-striped table-hover w-100">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Ubicación</th>
                        <th>Edificios</th>
                        <th>WhatsApp</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sucursales as $sucursal)
                        <tr>
                            <td>
                                {{ $sucursal->nombre }}
                                <div class="small text-secondary">{{ $sucursal->slug }}</div>
                            </td>
                            <td>
                                {{ $sucursal->ciudad }}@if ($sucursal->provincia), {{ $sucursal->provincia }}@endif
                            </td>
                            <td>{{ $sucursal->edificios_count }}</td>
                            <td>
                                @if ($sucursal->whatsapp)
                                    {{ $sucursal->whatsapp }}
                                @else
                                    <span class="badge text-bg-warning">Sin número</span>
                                @endif
                            </td>
                            <td>
                                @if ($sucursal->publicada)
                                    <span class="badge text-bg-success">Publicada</span>
                                @else
                                    <span class="badge text-bg-secondary">Borrador</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('sucursales.edit', $sucursal) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('sucursales.destroy', $sucursal) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta sucursal?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => new DataTable('#tabla-sucursales'));
    </script>
@endpush
