@extends('layouts.admin')

@section('title', 'Código QR — Pago #' . $pago->id)
@section('navbar-title', 'Código QR')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.pagos.index') }}">Pagos</a></li>
    <li><a href="{{ route('admin.pagos.show', $pago) }}">Pago #{{ $pago->id }}</a></li>
    <li class="active" aria-current="page">Código QR</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Código QR de pago"
        description="Escanea con tu aplicación bancaria para realizar la transferencia." />

    <div class="row justify-content-center">
        <div class="col-12 col-md-6 col-lg-4">
            <div class="admin-table-wrap p-4 text-center">
                <div id="qr-container" class="mb-3">
                    {!! $qrSvg !!}
                </div>

                <dl class="admin-meta text-start mt-3">
                    <dt>Orden</dt>
                    <dd>{{ $pago->ordenTrabajo->numero_orden ?? '—' }}</dd>
                    <dt>Monto</dt>
                    <dd><strong>Bs {{ number_format((float) $pago->monto, 2, ',', '.') }}</strong></dd>
                    <dt>Referencia</dt>
                    <dd>{{ $pago->numero_comprobante ?? $pago->id }}</dd>
                    <dt>Método</dt>
                    <dd>{{ $pago->metodoPago->nombre ?? '—' }}</dd>
                    <dt>Fecha</dt>
                    <dd>{{ $pago->fecha_pago?->format('d/m/Y H:i') ?? '—' }}</dd>
                </dl>

                <div class="d-flex gap-2 justify-content-center mt-3">
                    <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                        <i class="bi bi-printer" aria-hidden="true"></i>
                        Imprimir
                    </button>
                    <a href="{{ route('admin.pagos.show', $pago) }}" class="btn btn-primary">
                        <i class="bi bi-arrow-left" aria-hidden="true"></i>
                        Volver al pago
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    const container = document.getElementById('qr-container');
    if (!container) return;
    const svg = container.querySelector('svg');
    if (!svg) return;

    const btnDescargar = document.createElement('button');
    btnDescargar.className = 'btn btn-outline-primary mt-2';
    btnDescargar.innerHTML = '<i class="bi bi-download" aria-hidden="true"></i> Descargar QR';
    btnDescargar.addEventListener('click', function () {
        const svgData = new XMLSerializer().serializeToString(svg);
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        const img = new Image();
        img.onload = function () {
            canvas.width = img.width;
            canvas.height = img.height;
            ctx.drawImage(img, 0, 0);
            const link = document.createElement('a');
            link.download = 'qr-pago-{{ $pago->id }}.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        };
        img.src = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svgData)));
    });
    container.parentNode.insertBefore(btnDescargar, container.nextSibling);
})();
</script>
@endpush
