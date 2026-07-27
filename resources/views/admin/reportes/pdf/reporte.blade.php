<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $datos['titulo'] }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9pt; color: #1e293b; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #3b82f6; padding-bottom: 10px; }
        .header h1 { font-size: 16pt; color: #0f172a; }
        .header p { font-size: 8pt; color: #64748b; margin-top: 4px; }
        .info { font-size: 8pt; color: #64748b; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th { background: #3b82f6; color: #fff; font-weight: 600; padding: 6px 8px; text-align: left; font-size: 8pt; }
        td { padding: 5px 8px; border-bottom: 1px solid #e2e8f0; font-size: 8pt; }
        tr:nth-child(even) td { background: #f8fafc; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .total-row td { font-weight: 700; border-top: 2px solid #0f172a; }
        .footer { margin-top: 20px; padding-top: 10px; border-top: 1px solid #e2e8f0; font-size: 7pt; color: #94a3b8; text-align: center; }
        .badge { display: inline-block; background: #e2e8f0; padding: 1px 6px; border-radius: 4px; font-size: 7pt; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name', 'Taller Automotriz') }}</h1>
        <p>{{ $datos['titulo'] }}</p>
    </div>
    <div class="info">Generado: {{ now()->format('d/m/Y H:i') }} | {{ $datos['descripcion'] }}</div>

    @if ($tipo === 'ingresos')
        <div style="margin-bottom:15px;text-align:center;">
            <strong style="font-size:14pt;">Total: {{ number_format($datos['total'], 2, ',', '.') }} Bs.</strong>
        </div>
        <table>
            <thead><tr><th>Método de pago</th><th class="text-end">Cantidad</th><th class="text-end">Total</th></tr></thead>
            <tbody>
                @foreach ($datos['por_metodo'] as $metodo => $info)
                    <tr><td>{{ $metodo }}</td><td class="text-end">{{ $info['cantidad'] }}</td><td class="text-end">{{ number_format($info['total'], 2, ',', '.') }}</td></tr>
                @endforeach
            </tbody>
        </table>
        <table>
            <thead><tr><th>Fecha</th><th>Orden</th><th>Método</th><th class="text-end">Monto</th></tr></thead>
            <tbody>
                @foreach ($datos['pagos'] as $pago)
                    <tr><td>{{ $pago->fecha_pago?->format('d/m/Y') }}</td><td>{{ $pago->ordenTrabajo->numero_orden ?? '—' }}</td><td>{{ $pago->metodoPago->nombre ?? '—' }}</td><td class="text-end">{{ number_format((float) $pago->monto, 2, ',', '.') }}</td></tr>
                @endforeach
            </tbody>
        </table>

    @elseif ($tipo === 'ordenes-estado')
        <table>
            <thead><tr><th>Estado</th><th class="text-end">Cantidad</th><th class="text-end">Monto total</th></tr></thead>
            <tbody>
                @forelse ($datos['por_estado'] as $row)
                    <tr><td>{{ ucfirst(str_replace('_', ' ', $row->estado)) }}</td><td class="text-end">{{ $row->cantidad }}</td><td class="text-end">{{ number_format((float) $row->monto, 2, ',', '.') }}</td></tr>
                @empty
                    <tr><td colspan="3" class="text-center">Sin datos</td></tr>
                @endforelse
            </tbody>
        </table>

    @elseif ($tipo === 'mecanicos-productividad')
        <table>
            <thead><tr><th>Mecánico</th><th class="text-end">Asignaciones</th><th class="text-end">Finalizadas</th></tr></thead>
            <tbody>
                @forelse ($datos['mecanicos'] as $m)
                    <tr><td>{{ $m['mecanico'] }}</td><td class="text-end">{{ $m['asignaciones'] }}</td><td class="text-end">{{ $m['finalizadas'] }}</td></tr>
                @empty
                    <tr><td colspan="3" class="text-center">Sin datos</td></tr>
                @endforelse
            </tbody>
        </table>

    @elseif ($tipo === 'stock-critico')
        <table>
            <thead><tr><th>Repuesto</th><th>Código</th><th class="text-end">Stock</th><th class="text-end">Mínimo</th></tr></thead>
            <tbody>
                @forelse ($datos['items'] as $it)
                    <tr><td>{{ $it->repuesto->nombre ?? '—' }}</td><td>{{ $it->repuesto->codigo ?? '' }}</td><td class="text-end">{{ $it->cantidad_actual }}</td><td class="text-end">{{ $it->repuesto->stock_minimo ?? 0 }}</td></tr>
                @empty
                    <tr><td colspan="4" class="text-center">Sin alertas</td></tr>
                @endforelse
            </tbody>
        </table>

    @elseif ($tipo === 'clientes-frecuentes')
        <table>
            <thead><tr><th>Cliente</th><th class="text-end">Órdenes</th><th class="text-end">Monto total</th></tr></thead>
            <tbody>
                @forelse ($datos['clientes'] as $c)
                    <tr><td>{{ $c['cliente'] }}</td><td class="text-end">{{ $c['ordenes'] }}</td><td class="text-end">{{ number_format($c['monto_total'], 2, ',', '.') }}</td></tr>
                @empty
                    <tr><td colspan="3" class="text-center">Sin datos</td></tr>
                @endforelse
            </tbody>
        </table>

    @elseif ($tipo === 'servicios-mas-vendidos')
        <table>
            <thead><tr><th>Servicio</th><th class="text-end">Veces</th></tr></thead>
            <tbody>
                @forelse ($datos['servicios'] as $s)
                    <tr><td>{{ $s['servicio'] }}</td><td class="text-end">{{ $s['veces'] }}</td></tr>
                @empty
                    <tr><td colspan="2" class="text-center">Sin datos</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif

    <div class="footer">Documento generado por {{ config('app.name', 'Taller Automotriz') }} - {{ now()->format('d/m/Y H:i') }}</div>
</body>
</html>
