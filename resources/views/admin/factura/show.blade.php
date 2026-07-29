<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura {{ $comprobante->numero }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.4;
            color: #1a1a1a;
            background: #f0f0f0;
            padding: 20px;
        }
        .factura-wrap {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            border: 2px solid #1a1a1a;
            padding: 30px;
        }
        .factura-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #1a1a1a;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .factura-logo {
            max-width: 120px;
            max-height: 80px;
        }
        .factura-empresa {
            text-align: right;
        }
        .factura-empresa h1 {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .factura-empresa p {
            font-size: 11px;
            margin-top: 2px;
        }
        .factura-titulo {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 6px;
            text-transform: uppercase;
            border-bottom: 2px solid #1a1a1a;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .factura-ref {
            font-size: 11px;
            text-align: right;
            margin-bottom: 15px;
        }
        .factura-info-grid {
            display: flex;
            gap: 30px;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            background: #fafafa;
        }
        .factura-info-grid h3 {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 2px;
        }
        .factura-info-grid p {
            font-size: 11px;
            margin-top: 2px;
        }
        .factura-info-grid .col {
            flex: 1;
        }
        table.factura-items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.factura-items th {
            background: #1a1a1a;
            color: #fff;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 6px 8px;
            text-align: left;
        }
        table.factura-items th.right { text-align: right; }
        table.factura-items td {
            padding: 5px 8px;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
        }
        table.factura-items td.right { text-align: right; }
        table.factura-items .seccion td {
            font-weight: bold;
            background: #f0f0f0;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 4px 8px;
        }
        table.factura-items .total-row td {
            font-weight: bold;
            border-top: 2px solid #1a1a1a;
            font-size: 12px;
        }
        table.factura-items .total-row.grand td {
            font-size: 14px;
            border-top: 3px double #1a1a1a;
        }
        .factura-footer {
            border-top: 2px solid #1a1a1a;
            padding-top: 10px;
            margin-top: 10px;
            text-align: center;
            font-size: 11px;
        }
        .factura-footer p { margin-top: 3px; }
        .no-print { display: block; text-align: center; margin-bottom: 10px; }
        .no-print button {
            background: #1a1a1a;
            color: #fff;
            border: none;
            padding: 8px 24px;
            font-size: 13px;
            cursor: pointer;
            font-family: 'Courier New', monospace;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .no-print button:hover { background: #333; }
        @media print {
            body { background: #fff; padding: 0; }
            .factura-wrap { border: none; max-width: 100%; padding: 20px; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">Imprimir Factura</button>
        <button onclick="history.back()" style="background:#666;margin-left:8px;">Volver</button>
    </div>

    <div class="factura-wrap">
        <div class="factura-header">
            <div>
                <img src="{{ asset('img/logo.png') }}" alt="Logo" class="factura-logo" onerror="this.style.display='none'">
            </div>
            <div class="factura-empresa">
                <h1>{{ $empresa['razon_social'] }}</h1>
                <p>NIT: {{ $empresa['nit'] }}</p>
                <p>{{ $empresa['direccion'] }}</p>
                <p>Tel: {{ $empresa['telefono'] }} &middot; {{ $empresa['email'] }}</p>
            </div>
        </div>

        <div class="factura-titulo">Factura</div>

        <div class="factura-ref">
            N&deg; FACTURA: <strong>{{ $comprobante->numero }}</strong><br>
            Fecha: {{ $comprobante->fecha_emision?->format('d/m/Y H:i') }}
        </div>

        <div class="factura-info-grid">
            <div class="col">
                <h3>Cliente</h3>
                <p><strong>{{ $cliente->nombre_completo }}</strong></p>
                @if ($comprobante->nit_ci)
                    <p>NIT: {{ $comprobante->nit_ci }}</p>
                @endif
                @if ($comprobante->razon_social && $comprobante->razon_social !== $cliente->nombre_completo)
                    <p>Raz&oacute;n Social: {{ $comprobante->razon_social }}</p>
                @endif
                <p>Direcci&oacute;n: {{ $cliente->direccion ?? '—' }}</p>
                <p>Tel&eacute;fono: {{ $cliente->telefono ?? '—' }}</p>
            </div>
            <div class="col">
                <h3>Orden de Trabajo</h3>
                <p>N&deg; Orden: <strong>{{ $orden->numero_orden }}</strong></p>
                <p>Veh&iacute;culo: {{ $vehiculo->marca ?? '' }} {{ $vehiculo?->modelo?->nombre ?? '' }}</p>
                <p>Placa: {{ $vehiculo->placa ?? '—' }}</p>
                @if ($orden->kilometraje_ingreso)
                    <p>Kilometraje: {{ number_format($orden->kilometraje_ingreso, 0, ',', '.') }} km</p>
                @endif
                <p>M&eacute;todo de pago: <strong>{{ $pago->metodoPago->nombre ?? '—' }}</strong></p>
            </div>
        </div>

        <table class="factura-items">
            <thead>
                <tr>
                    <th style="width:40px;">Cant</th>
                    <th>Descripci&oacute;n</th>
                    <th style="width:100px;" class="right">P. Unitario</th>
                    <th style="width:100px;" class="right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @if ($servicios->isNotEmpty())
                    <tr class="seccion"><td colspan="4">Servicios Realizados</td></tr>
                    @foreach ($servicios as $s)
                        <tr>
                            <td>1</td>
                            <td>{{ $s->nombre_servicio ?? $s->servicio?->nombre ?? 'Servicio' }}</td>
                            <td class="right">{{ number_format((float) $s->precio_base, 2, ',', '.') }}</td>
                            <td class="right">{{ number_format((float) $s->precio_base, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @endif

                @if ($repuestos->isNotEmpty())
                    <tr class="seccion"><td colspan="4">Repuestos / Piezas</td></tr>
                    @foreach ($repuestos as $r)
                        @php
                            $precioU = (float) $r->precio_unitario_snapshot;
                            $cant = (float) $r->cantidad;
                            $subtotalR = $precioU * $cant;
                        @endphp
                        <tr>
                            <td>{{ $cant }}</td>
                            <td>{{ $r->repuesto?->nombre ?? 'Repuesto' }}</td>
                            <td class="right">{{ number_format($precioU, 2, ',', '.') }}</td>
                            <td class="right">{{ number_format($subtotalR, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @endif

                @if ($manoDeObra > 0)
                    <tr class="seccion"><td colspan="4">Mano de Obra</td></tr>
                    <tr>
                        <td>1</td>
                        <td>Mano de obra</td>
                        <td class="right">{{ number_format($manoDeObra, 2, ',', '.') }}</td>
                        <td class="right">{{ number_format($manoDeObra, 2, ',', '.') }}</td>
                    </tr>
                @endif

                <tr class="total-row">
                    <td colspan="3" class="right">Subtotal Servicios</td>
                    <td class="right">{{ number_format($subtotalServicios, 2, ',', '.') }}</td>
                </tr>
                <tr class="total-row">
                    <td colspan="3" class="right">Subtotal Repuestos</td>
                    <td class="right">{{ number_format($subtotalRepuestos, 2, ',', '.') }}</td>
                </tr>
                @if ($manoDeObra > 0)
                    <tr class="total-row">
                        <td colspan="3" class="right">Mano de Obra</td>
                        <td class="right">{{ number_format($manoDeObra, 2, ',', '.') }}</td>
                    </tr>
                @endif
                @if ($descuento > 0)
                    <tr class="total-row">
                        <td colspan="3" class="right">Descuento</td>
                        <td class="right">-{{ number_format($descuento, 2, ',', '.') }}</td>
                    </tr>
                @endif
                <tr class="total-row grand">
                    <td colspan="3" class="right" style="font-size:14px;">TOTAL {{ env('MONEDA', 'Bs') }}</td>
                    <td class="right" style="font-size:14px;font-weight:bold;">{{ number_format($total, 2, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        @if ($comprobante->observaciones)
            <div style="margin-bottom:10px;padding:8px;border:1px solid #ccc;font-size:10px;background:#fafafa;">
                <strong>Observaciones:</strong><br>
                {{ $comprobante->observaciones }}
            </div>
        @endif

        <div class="factura-footer">
            <p><strong>{{ $empresa['razon_social'] }}</strong></p>
            <p>NIT: {{ $empresa['nit'] }} &middot; {{ $empresa['direccion'] }} &middot; Tel: {{ $empresa['telefono'] }}</p>
            <p style="margin-top:8px;font-size:10px;letter-spacing:2px;">&iexcl;Gracias por su preferencia!</p>
        </div>
    </div>

    <div class="no-print" style="margin-top:10px;">
        <button onclick="window.print()">Imprimir Factura</button>
        <button onclick="history.back()" style="background:#666;margin-left:8px;">Volver</button>
    </div>
</body>
</html>
