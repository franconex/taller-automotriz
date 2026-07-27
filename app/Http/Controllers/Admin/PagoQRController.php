<?php

namespace App\Http\Controllers\Admin;

use App\Models\Pago;
use App\Services\QrService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PagoQRController extends AdminController
{
    public function mostrar(Pago $pago): View
    {
        $pago->load(['ordenTrabajo', 'metodoPago']);

        $contenido = implode("\n", [
            'PAGO TALLER',
            'Orden: ' . ($pago->ordenTrabajo->numero_orden ?? '—'),
            'Monto: Bs ' . number_format((float) $pago->monto, 2, ',', '.'),
            'Ref: ' . ($pago->numero_comprobante ?? $pago->id),
            'Fecha: ' . $pago->fecha_pago?->format('d/m/Y H:i') ?? '—',
        ]);

        $qrService = new QrService();
        $qrSvg = $qrService->generar($contenido);

        return view('admin.pagos.qr', [
            'pago' => $pago,
            'qrSvg' => $qrSvg,
        ]);
    }

    public function qrData(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'contenido' => ['required', 'string', 'max:1000'],
        ]);

        try {
            $qrService = new QrService();
            $svg = $qrService->generar($request->input('contenido'));

            return response()->json(['ok' => true, 'svg' => $svg]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => 'Error al generar QR.'], 500);
        }
    }
}
