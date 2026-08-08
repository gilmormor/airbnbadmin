@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card text-bg-primary">
                <div class="card-body">
                    <div class="text-uppercase small">Reservas este mes</div>
                    <div class="fs-2 fw-bold">{{ $totalReservasMes }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-bg-success">
                <div class="card-body">
                    <div class="text-uppercase small">Ingreso líquido este mes</div>
                    <div class="fs-2 fw-bold">{{ number_format($ingresoMes, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Próximos check-ins</h5>
        </div>
        <div class="card-body">
            @forelse ($proximosCheckins as $reserva)
                <div class="d-flex justify-content-between border-bottom py-2">
                    <div>
                        <strong>{{ $reserva->departamento->edificio->nombre }} — {{ $reserva->departamento->nombre }}</strong>
                        <div class="text-body-secondary small">{{ $reserva->huesped }} · {{ $reserva->plataforma->nombre }}</div>
                    </div>
                    <div class="text-end">
                        {{ $reserva->fecha_checkin->format('d/m/Y') }}
                    </div>
                </div>
            @empty
                <p class="text-body-secondary mb-0">No hay check-ins próximos.</p>
            @endforelse
        </div>
    </div>
@endsection
