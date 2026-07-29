<?php

namespace App\Http\Controllers\Admin;

use App\Models\Pago;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PagoQRController extends AdminController
{
    public function mostrar(Pago $pago): View
    {
        $pago->load(['ordenTrabajo', 'metodoPago']);

        return view('admin.pagos.qr', [
            'pago' => $pago,
        ]);
    }

    public function qrData(Request $request): JsonResponse
    {
        return response()->json([
            'ok' => true,
            'qr_url' => asset('img/QR-Pago.jpeg'),
        ]);
    }
}
