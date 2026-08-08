<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #222; }
        h1 { font-size: 16px; margin-bottom: 4px; }
        .periodo { color: #555; margin-bottom: 16px; }
        h2 { font-size: 12px; margin-top: 18px; margin-bottom: 4px; background: #f0f0f0; padding: 4px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        th, td { border: 1px solid #ccc; padding: 4px 6px; text-align: left; }
        th { background: #f7f7f7; }
        .text-end { text-align: right; }
        tfoot td { font-weight: bold; background: #fafafa; }
        .total-general { margin-top: 16px; padding: 8px; background: #eef; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Reporte de reservas</h1>
    <p class="periodo">Período: {{ $filtros['fecha_desde'] ?? '' }} al {{ $filtros['fecha_hasta'] ?? '' }}</p>

    @foreach ($porDepartamento as $grupo)
        <h2>{{ $grupo['departamento']->edificio->nombre }} — {{ $grupo['departamento']->nombre }} ({{ $grupo['departamento']->propietario->nombre }})</h2>
        <table>
            <thead>
                <tr>
                    <th>Plataforma</th>
                    <th>Código</th>
                    <th>Huésped</th>
                    <th>Fecha reserva</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th class="text-end">Bruto</th>
                    <th class="text-end">Com. plataforma</th>
                    <th class="text-end">Com. coanfitrión</th>
                    <th class="text-end">Líquido</th>
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
                        <td class="text-end">{{ number_format($reserva->monto_bruto, 2) }}</td>
                        <td class="text-end">{{ number_format($reserva->comision_plataforma, 2) }}</td>
                        <td class="text-end">{{ number_format($reserva->comision_coanfitrion, 2) }}</td>
                        <td class="text-end">{{ number_format($reserva->ingreso_liquido_propietario, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6">Subtotal</td>
                    <td class="text-end">{{ number_format($grupo['monto_bruto'], 2) }}</td>
                    <td class="text-end">{{ number_format($grupo['comision_plataforma'], 2) }}</td>
                    <td class="text-end">{{ number_format($grupo['comision_coanfitrion'], 2) }}</td>
                    <td class="text-end">{{ number_format($grupo['ingreso_liquido_propietario'], 2) }}</td>
                </tr>
            </tfoot>
        </table>
    @endforeach

    @if ($totales)
        <div class="total-general">
            Total general — Bruto: {{ number_format($totales['monto_bruto'], 2) }} |
            Com. plataforma: {{ number_format($totales['comision_plataforma'], 2) }} |
            Com. coanfitrión: {{ number_format($totales['comision_coanfitrion'], 2) }} |
            Líquido propietario: {{ number_format($totales['ingreso_liquido_propietario'], 2) }}
        </div>
    @endif
</body>
</html>
