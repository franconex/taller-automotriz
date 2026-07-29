<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comprobante;
use App\Models\Setting;
use Illuminate\View\View;

class FacturaController extends Controller
{
    public function show(Comprobante $comprobante): View
    {
        $comprobante->load(['pago.metodoPago', 'pago.ordenTrabajo.cliente', 'pago.ordenTrabajo.vehiculo.modelo', 'pago.ordenTrabajo.serviciosMecanico', 'pago.ordenTrabajo.repuestosMecanico', 'pago.ordenTrabajo.autorizaciones']);

        $pago = $comprobante->pago;
        $orden = $pago->ordenTrabajo;
        $cliente = $orden->cliente;
        $vehiculo = $orden->vehiculo;

        $servicios = $orden->serviciosMecanico;
        $repuestos = $orden->repuestosMecanico;

        $subtotalServicios = (float) $servicios->sum('precio_base');
        $subtotalRepuestos = (float) $repuestos->sum(fn($r) => $r->cantidad * $r->precio_unitario_snapshot);
        $manoDeObra = (float) $orden->autorizaciones->sum('mano_de_obra');
        $descuento = (float) $orden->descuento;
        $total = $subtotalServicios + $subtotalRepuestos + $manoDeObra - $descuento;

        $empresa = [
            'razon_social' => Setting::obtener('razon_social', 'Taller Automotriz'),
            'nit' => Setting::obtener('nit', ''),
            'direccion' => Setting::obtener('direccion', ''),
            'telefono' => Setting::obtener('telefono', ''),
            'email' => Setting::obtener('email', ''),
        ];

        return view('admin.factura.show', compact(
            'comprobante', 'pago', 'orden', 'cliente', 'vehiculo',
            'servicios', 'repuestos',
            'subtotalServicios', 'subtotalRepuestos',
            'manoDeObra', 'descuento', 'total', 'empresa'
        ) + ['ordenId' => $orden->id]);
    }
}
