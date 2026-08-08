@extends('layouts.app')

@section('page-title', 'Reservas')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Reservas</h5>
            <a href="{{ route('reportes.index') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-bar-chart-line"></i> Ir a reportes
            </a>
        </div>
        <div class="card-body">
            <table id="tabla-reservas" class="table table-striped table-hover w-100">
                <thead>
                    <tr>
                        <th>Edificio</th>
                        <th>Departamento</th>
                        <th>Plataforma</th>
                        <th>Código</th>
                        <th>Huésped</th>
                        <th>Fecha de reserva</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Estado</th>
                        <th class="text-end">Líquido propietario</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reservas as $reserva)
                        <tr>
                            <td>{{ $reserva->departamento->edificio->nombre }}</td>
                            <td>{{ $reserva->departamento->nombre }}</td>
                            <td>{{ $reserva->plataforma->nombre }}</td>
                            <td>{{ $reserva->codigo_externo }}</td>
                            <td>{{ $reserva->huesped }}</td>
                            <td>{{ $reserva->fecha_reserva?->format('d/m/Y') }}</td>
                            <td>{{ $reserva->fecha_checkin->format('d/m/Y') }}</td>
                            <td>{{ $reserva->fecha_checkout->format('d/m/Y') }}</td>
                            <td>{{ $reserva->estado }}</td>
                            <td class="text-end">{{ number_format($reserva->ingreso_liquido_propietario, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => new DataTable('#tabla-reservas', { order: [[6, 'desc']] }));
    </script>
@endpush
