@extends('layouts.app')

@section('page-title', 'Reportes de reservas')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Filtros</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reportes.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Desde</label>
                    <input type="date" name="fecha_desde" class="form-control" value="{{ $filtros['fecha_desde'] ?? '' }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control" value="{{ $filtros['fecha_hasta'] ?? '' }}" required>
                </div>

                @can('edificios.gestionar')
                    <div class="col-md-2">
                        <label class="form-label">Edificio</label>
                        <select name="edificio_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach ($edificios as $edificio)
                                <option value="{{ $edificio->id }}" @selected(($filtros['edificio_id'] ?? '') == $edificio->id)>{{ $edificio->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Propietario</label>
                        <select name="propietario_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach ($propietarios as $propietario)
                                <option value="{{ $propietario->id }}" @selected(($filtros['propietario_id'] ?? '') == $propietario->id)>{{ $propietario->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                @endcan

                <div class="col-md-2">
                    <label class="form-label">Departamento</label>
                    <select name="departamento_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach ($departamentos as $departamento)
                            <option value="{{ $departamento->id }}" @selected(($filtros['departamento_id'] ?? '') == $departamento->id)>{{ $departamento->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                    @if ($buscado)
                        <a href="{{ route('reportes.excel', $filtros) }}" class="btn btn-success">
                            <i class="bi bi-file-earmark-excel"></i> Exportar Excel
                        </a>
                        <a href="{{ route('reportes.pdf', $filtros) }}" class="btn btn-danger">
                            <i class="bi bi-file-earmark-pdf"></i> Exportar PDF
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if ($buscado)
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Resultados</h5>
            </div>
            <div class="card-body">
                @forelse ($porDepartamento as $grupo)
                    <h6 class="mt-3">
                        {{ $grupo['departamento']->edificio->nombre }} — {{ $grupo['departamento']->nombre }}
                        <span class="text-body-secondary small">({{ $grupo['departamento']->propietario->nombre }})</span>
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Plataforma</th>
                                    <th>Código</th>
                                    <th>Huésped</th>
                                    <th>Fecha de reserva</th>
                                    <th>Check-in</th>
                                    <th>Check-out</th>
                                    <th class="text-end">Bruto</th>
                                    <th class="text-end">Com. plataforma</th>
                                    <th class="text-end">Tarifas</th>
                                    <th class="text-end">Com. coanfitrión</th>
                                    <th class="text-end">Líquido propietario</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($grupo['reservas'] as $reserva)
                                    <tr>
                                        <td>{{ $reserva->plataforma->nombre }}</td>
                                        <td>{{ $reserva->codigo_externo }}</td>
                                        <td>{{ $reserva->huesped }}</td>
                                        <td>{{ $reserva->fecha_reserva?->format('d/m/Y') }}</td>
                                        <td>{{ $reserva->fecha_checkin->format('d/m/Y') }}</td>
                                        <td>{{ $reserva->fecha_checkout->format('d/m/Y') }}</td>
                                        <td class="text-end">{{ number_format($reserva->monto_bruto, 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($reserva->comision_plataforma, 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($reserva->tarifa_limpieza, 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($reserva->comision_coanfitrion, 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($reserva->ingreso_liquido_propietario, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="fw-bold table-light">
                                    <td colspan="6">Subtotal</td>
                                    <td class="text-end">{{ number_format($grupo['monto_bruto'], 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($grupo['comision_plataforma'], 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($grupo['tarifa_limpieza'], 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($grupo['comision_coanfitrion'], 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($grupo['ingreso_liquido_propietario'], 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @empty
                    <p class="text-body-secondary">No se encontraron reservas para los filtros seleccionados.</p>
                @endforelse

                @if ($totales)
                    <div class="alert alert-primary d-flex justify-content-between mt-4">
                        <strong>Total general</strong>
                        <span>
                            Bruto: {{ number_format($totales['monto_bruto'], 0, ',', '.') }} —
                            Com. plataforma: {{ number_format($totales['comision_plataforma'], 0, ',', '.') }} —
                            Tarifas: {{ number_format($totales['tarifa_limpieza'], 0, ',', '.') }} —
                            Com. coanfitrión: {{ number_format($totales['comision_coanfitrion'], 0, ',', '.') }} —
                            <strong>Líquido: {{ number_format($totales['ingreso_liquido_propietario'], 0, ',', '.') }}</strong>
                        </span>
                    </div>
                @endif
            </div>
        </div>
    @endif
@endsection
