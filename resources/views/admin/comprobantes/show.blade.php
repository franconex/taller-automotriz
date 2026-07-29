@php use App\Models\Setting; @endphp

@extends('layouts.admin')

@section('title', $comprobante->numero)
@section('navbar-title', 'Comprobante ' . $comprobante->numero)

@section('content')
<x-admin.page-header
    :title="'Comprobante ' . $comprobante->numero"
    :description="$comprobante->cliente->nombre_completo ?? ''">
    <x-slot:actions>
        <a href="{{ route('admin.comprobantes.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
        <a href="{{ route('admin.factura.show', $comprobante) }}" target="_blank" class="btn btn-sm" style="border:1px solid #0B1D3A;border-radius:3px;color:#0B1D3A;">
            <i class="bi bi-printer"></i> Imprimir Factura
        </a>
        @if (Auth::user()->tienePermiso('comprobantes.editar'))
            <a href="{{ route('admin.comprobantes.edit', $comprobante) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil-square"></i> Editar
            </a>
        @endif
    </x-slot:actions>
</x-admin.page-header>

<div style="border:1px solid #dee2e6;background:#fff;padding:20px;max-width:800px;">

    <div style="display:flex;justify-content:space-between;align-items:flex-start;border-bottom:2px solid #1a1a1a;padding-bottom:12px;margin-bottom:12px;">
        <div>
            <img src="{{ asset('img/logo.png') }}" alt="Logo" style="max-width:100px;max-height:60px;" onerror="this.style.display='none'">
        </div>
        <div style="text-align:right;">
            <div style="font-weight:bold;text-transform:uppercase;letter-spacing:1px;">{{ Setting::obtener('razon_social', 'Taller') }}</div>
            <div style="font-size:11px;">NIT: {{ Setting::obtener('nit', '') }}</div>
        </div>
    </div>

    <div style="font-size:18px;font-weight:bold;text-align:center;text-transform:uppercase;letter-spacing:4px;border-bottom:2px solid #1a1a1a;padding-bottom:6px;margin-bottom:12px;">
        {{ $comprobante->estado === 'emitido' ? 'Comprobante' : 'Comprobante Anulado' }}
    </div>

    <div style="display:flex;gap:20px;margin-bottom:12px;padding:8px;border:1px solid #ccc;background:#fafafa;">
        <div style="flex:1;">
            <div style="font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:1px;border-bottom:1px solid #ccc;padding-bottom:2px;margin-bottom:3px;">Cliente</div>
            <p style="font-size:11px;margin:2px 0;"><strong>{{ $comprobante->cliente->nombre_completo ?? '—' }}</strong></p>
            @if ($comprobante->nit_ci)
                <p style="font-size:11px;margin:2px 0;">NIT: {{ $comprobante->nit_ci }}</p>
            @endif
            @if ($comprobante->razon_social && $comprobante->razon_social !== ($comprobante->cliente->nombre_completo ?? ''))
                <p style="font-size:11px;margin:2px 0;">{{ $comprobante->razon_social }}</p>
            @endif
            @if ($comprobante->cliente)
                <p style="font-size:11px;margin:2px 0;">Tel: {{ $comprobante->cliente->telefono ?? '—' }}</p>
            @endif
        </div>
        <div style="flex:1;">
            <div style="font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:1px;border-bottom:1px solid #ccc;padding-bottom:2px;margin-bottom:3px;">Comprobante</div>
            <p style="font-size:11px;margin:2px 0;"><strong>{{ $comprobante->numero }}</strong></p>
            <p style="font-size:11px;margin:2px 0;">{{ $comprobante->fecha_emision?->format('d/m/Y H:i') ?? '—' }}</p>
            @if ($comprobante->pago?->metodoPago)
                <p style="font-size:11px;margin:2px 0;">{{ $comprobante->pago->metodoPago->nombre }}</p>
            @endif
        </div>
    </div>

    @if ($comprobante->pago?->ordenTrabajo)
        @php
            $orden = $comprobante->pago->ordenTrabajo;
            $servicios = $orden->serviciosMecanico;
            $repuestos = $orden->repuestosMecanico;
            $subtotalServicios = (float) $servicios->sum('precio_base');
            $subtotalRepuestos = (float) $repuestos->sum(fn($r) => $r->cantidad * $r->precio_unitario_snapshot);
            $manoDeObra = (float) $orden->autorizaciones->sum('mano_de_obra');
            $descuento = (float) $orden->descuento;
            $total = $subtotalServicios + $subtotalRepuestos + $manoDeObra - $descuento;
        @endphp

        <table style="width:100%;border-collapse:collapse;margin-bottom:12px;">
            <thead>
                <tr style="background:#1a1a1a;color:#fff;font-size:10px;text-transform:uppercase;letter-spacing:1px;">
                    <th style="padding:5px 8px;text-align:left;">Cant</th>
                    <th style="padding:5px 8px;text-align:left;">Descripci&oacute;n</th>
                    <th style="padding:5px 8px;text-align:right;">P. Unitario</th>
                    <th style="padding:5px 8px;text-align:right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @if ($servicios->isNotEmpty())
                    <tr style="background:#f0f0f0;font-weight:bold;font-size:10px;text-transform:uppercase;letter-spacing:1px;">
                        <td colspan="4" style="padding:4px 8px;">Servicios Realizados</td>
                    </tr>
                    @foreach ($servicios as $s)
                        <tr>
                            <td style="padding:4px 8px;border-bottom:1px solid #ddd;font-size:11px;">1</td>
                            <td style="padding:4px 8px;border-bottom:1px solid #ddd;font-size:11px;">{{ $s->nombre_servicio ?? $s->servicio?->nombre ?? 'Servicio' }}</td>
                            <td style="padding:4px 8px;border-bottom:1px solid #ddd;font-size:11px;text-align:right;">{{ number_format((float) $s->precio_base, 2, ',', '.') }}</td>
                            <td style="padding:4px 8px;border-bottom:1px solid #ddd;font-size:11px;text-align:right;">{{ number_format((float) $s->precio_base, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @endif

                @if ($repuestos->isNotEmpty())
                    <tr style="background:#f0f0f0;font-weight:bold;font-size:10px;text-transform:uppercase;letter-spacing:1px;">
                        <td colspan="4" style="padding:4px 8px;">Repuestos / Piezas</td>
                    </tr>
                    @foreach ($repuestos as $r)
                        @php
                            $precioU = (float) $r->precio_unitario_snapshot;
                            $cant = (float) $r->cantidad;
                            $subtotalR = $precioU * $cant;
                        @endphp
                        <tr>
                            <td style="padding:4px 8px;border-bottom:1px solid #ddd;font-size:11px;">{{ $cant }}</td>
                            <td style="padding:4px 8px;border-bottom:1px solid #ddd;font-size:11px;">{{ $r->repuesto?->nombre ?? 'Repuesto' }}</td>
                            <td style="padding:4px 8px;border-bottom:1px solid #ddd;font-size:11px;text-align:right;">{{ number_format($precioU, 2, ',', '.') }}</td>
                            <td style="padding:4px 8px;border-bottom:1px solid #ddd;font-size:11px;text-align:right;">{{ number_format($subtotalR, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @endif

                @if ($manoDeObra > 0)
                    <tr style="background:#f0f0f0;font-weight:bold;font-size:10px;text-transform:uppercase;letter-spacing:1px;">
                        <td colspan="4" style="padding:4px 8px;">Mano de Obra</td>
                    </tr>
                    <tr>
                        <td style="padding:4px 8px;border-bottom:1px solid #ddd;font-size:11px;">1</td>
                        <td style="padding:4px 8px;border-bottom:1px solid #ddd;font-size:11px;">Mano de obra</td>
                        <td style="padding:4px 8px;border-bottom:1px solid #ddd;font-size:11px;text-align:right;">{{ number_format($manoDeObra, 2, ',', '.') }}</td>
                        <td style="padding:4px 8px;border-bottom:1px solid #ddd;font-size:11px;text-align:right;">{{ number_format($manoDeObra, 2, ',', '.') }}</td>
                    </tr>
                @endif

                @if ($descuento > 0)
                    <tr style="font-weight:bold;">
                        <td colspan="3" style="padding:4px 8px;border-top:1px solid #1a1a1a;font-size:11px;text-align:right;">Descuento</td>
                        <td style="padding:4px 8px;border-top:1px solid #1a1a1a;font-size:11px;text-align:right;">-{{ number_format($descuento, 2, ',', '.') }}</td>
                    </tr>
                @endif

                <tr>
                    <td colspan="3" style="padding:4px 8px;text-align:right;font-weight:bold;border-top:2px solid #1a1a1a;font-size:12px;">Subtotal Servicios</td>
                    <td style="padding:4px 8px;text-align:right;border-top:2px solid #1a1a1a;font-size:12px;">{{ number_format($subtotalServicios, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="3" style="padding:4px 8px;text-align:right;font-weight:bold;font-size:11px;">Subtotal Repuestos</td>
                    <td style="padding:4px 8px;text-align:right;font-size:11px;">{{ number_format($subtotalRepuestos, 2, ',', '.') }}</td>
                </tr>
                @if ($manoDeObra > 0)
                    <tr>
                        <td colspan="3" style="padding:4px 8px;text-align:right;font-weight:bold;font-size:11px;">Mano de Obra</td>
                        <td style="padding:4px 8px;text-align:right;font-size:11px;">{{ number_format($manoDeObra, 2, ',', '.') }}</td>
                    </tr>
                @endif
                <tr>
                    <td colspan="3" style="padding:4px 8px;text-align:right;font-weight:bold;font-size:14px;border-top:3px double #1a1a1a;">TOTAL Bs</td>
                    <td style="padding:4px 8px;text-align:right;font-weight:bold;font-size:14px;border-top:3px double #1a1a1a;">{{ number_format($total, 2, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    @else
        <div style="padding:12px;border:1px solid #ccc;margin-bottom:12px;font-size:12px;">
            <strong>Monto:</strong> {{ number_format((float) $comprobante->monto_total, 2, ',', '.') }}
        </div>
    @endif

    @if ($comprobante->observaciones)
        <div style="margin-bottom:8px;padding:8px;border:1px solid #ccc;font-size:10px;background:#fafafa;">
            <strong>Observaciones:</strong><br>
            {{ $comprobante->observaciones }}
        </div>
    @endif
</div>
@endsection
