<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CotizacionRequest;
use App\Models\Cotizacion;
use App\Models\DetalleCotizacion;
use App\Models\DetalleOrdenCompra;
use App\Models\OrdenCompra;
use App\Models\Proveedor;
use App\Models\SolicitudCompra;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CotizacionController extends AdminController
{
    public function create(Request $request): View
    {
        $solicitudCompraId = $request->input('solicitud_compra_id');
        $proveedorId = $request->input('proveedor_id');

        $solicitud = SolicitudCompra::with('detalles.repuesto')->findOrFail($solicitudCompraId);

        $proveedores = Proveedor::where('estado', true)->orderBy('nombre_empresa')->get();

        $proveedorSeleccionado = null;
        if ($proveedorId) {
            $proveedorSeleccionado = Proveedor::find($proveedorId);
        }

        return view('admin.cotizaciones.create', [
            'solicitud' => $solicitud,
            'proveedores' => $proveedores,
            'proveedorSeleccionado' => $proveedorSeleccionado,
        ]);
    }

    public function store(CotizacionRequest $request): RedirectResponse
    {
        $datos = $request->validated();

        $cotizacion = DB::transaction(function () use ($datos, $request) {
            $archivoRuta = null;
            if ($request->hasFile('archivo')) {
                $archivoRuta = $request->file('archivo')->store('cotizaciones', 'public');
            }

            $cotizacion = Cotizacion::create([
                'solicitud_compra_id' => $datos['solicitud_compra_id'],
                'proveedor_id' => $datos['proveedor_id'],
                'usuario_id' => Auth::id(),
                'medio_contacto' => $datos['medio_contacto'],
                'nombre_contacto' => $datos['nombre_contacto'] ?? null,
                'fecha_cotizacion' => now(),
                'fecha_vencimiento' => $datos['fecha_vencimiento'] ?? null,
                'estado' => 'respondio',
                'observaciones' => $datos['observaciones'] ?? null,
                'archivo' => $archivoRuta,
            ]);

            foreach ($datos['productos'] as $item) {
                $precio = (float) ($item['precio_unitario'] ?? 0);
                $cantidad = (int) ($item['cantidad_solicitada'] ?? 0);
                $descuento = (float) ($item['descuento'] ?? 0);
                $impuesto = (float) ($item['impuesto'] ?? 0);
                $envio = (float) ($item['costo_envio'] ?? 0);
                $subtotal = ($precio * $cantidad) - $descuento + $impuesto;

                DetalleCotizacion::create([
                    'cotizacion_id' => $cotizacion->id,
                    'repuesto_id' => $item['repuesto_id'],
                    'cantidad_solicitada' => $cantidad,
                    'cantidad_disponible' => $item['cantidad_disponible'] ?? null,
                    'marca_ofrecida' => $item['marca_ofrecida'] ?? null,
                    'precio_unitario' => $precio,
                    'descuento' => $descuento,
                    'impuesto' => $impuesto,
                    'costo_envio' => $envio,
                    'subtotal' => $subtotal,
                    'tiempo_entrega_dias' => $item['tiempo_entrega_dias'] ?? null,
                    'garantia_dias' => $item['garantia_dias'] ?? null,
                ]);
            }

            $cotizacion->load('proveedor');
            return $cotizacion;
        });

        return redirect()->route('admin.solicitudes-compra.show', $cotizacion->solicitud_compra_id)
            ->with('success', "Cotización de {$cotizacion->proveedor->nombre_empresa} registrada.");
    }

    public function show(Cotizacion $cotizacione): View
    {
        $cotizacione->load([
            'solicitudCompra.sucursal',
            'proveedor',
            'usuario',
            'detalles.repuesto',
        ]);

        return view('admin.cotizaciones.show', [
            'cotizacion' => $cotizacione,
        ]);
    }

    public function edit(Cotizacion $cotizacione): View
    {
        $cotizacione->load(['detalles.repuesto', 'solicitudCompra.detalles.repuesto', 'proveedor']);

        $proveedores = Proveedor::where('estado', true)->orderBy('nombre_empresa')->get();

        return view('admin.cotizaciones.edit', [
            'cotizacion' => $cotizacione,
            'proveedores' => $proveedores,
        ]);
    }

    public function update(CotizacionRequest $request, Cotizacion $cotizacione): RedirectResponse
    {
        $datos = $request->validated();

        DB::transaction(function () use ($datos, $request, $cotizacione) {
            if ($request->hasFile('archivo')) {
                if ($cotizacione->archivo) {
                    Storage::disk('public')->delete($cotizacione->archivo);
                }
                $archivoRuta = $request->file('archivo')->store('cotizaciones', 'public');
                $cotizacione->archivo = $archivoRuta;
            }

            $cotizacione->update([
                'proveedor_id' => $datos['proveedor_id'],
                'medio_contacto' => $datos['medio_contacto'],
                'nombre_contacto' => $datos['nombre_contacto'] ?? null,
                'fecha_vencimiento' => $datos['fecha_vencimiento'] ?? null,
                'observaciones' => $datos['observaciones'] ?? null,
            ]);

            $cotizacione->detalles()->delete();

            foreach ($datos['productos'] as $item) {
                $precio = (float) ($item['precio_unitario'] ?? 0);
                $cantidad = (int) ($item['cantidad_solicitada'] ?? 0);
                $descuento = (float) ($item['descuento'] ?? 0);
                $impuesto = (float) ($item['impuesto'] ?? 0);
                $envio = (float) ($item['costo_envio'] ?? 0);
                $subtotal = ($precio * $cantidad) - $descuento + $impuesto;

                DetalleCotizacion::create([
                    'cotizacion_id' => $cotizacione->id,
                    'repuesto_id' => $item['repuesto_id'],
                    'cantidad_solicitada' => $cantidad,
                    'cantidad_disponible' => $item['cantidad_disponible'] ?? null,
                    'marca_ofrecida' => $item['marca_ofrecida'] ?? null,
                    'precio_unitario' => $precio,
                    'descuento' => $descuento,
                    'impuesto' => $impuesto,
                    'costo_envio' => $envio,
                    'subtotal' => $subtotal,
                    'tiempo_entrega_dias' => $item['tiempo_entrega_dias'] ?? null,
                    'garantia_dias' => $item['garantia_dias'] ?? null,
                ]);
            }
        });

        return redirect()->route('admin.cotizaciones.show', $cotizacione)
            ->with('success', 'Cotización actualizada.');
    }

    public function seleccionar(Request $request, Cotizacion $cotizacione): RedirectResponse
    {
        $request->validate([
            'motivo_seleccion' => ['required', 'string', 'in:mejor_precio,disponibilidad_inmediata,entrega_rapida,mejor_garantia,proveedor_confiable,otro'],
            'motivo_seleccion_otro' => ['nullable', 'string', 'max:255', 'required_if:motivo_seleccion,otro'],
        ]);

        if ($cotizacione->estado !== 'respondio') {
            return back()->with('error', 'Solo se pueden seleccionar cotizaciones respondidas.');
        }

        $orden = DB::transaction(function () use ($cotizacione, $request) {
            $cotizacione->update([
                'estado' => 'seleccionado',
                'motivo_seleccion' => $request->motivo_seleccion,
                'motivo_seleccion_otro' => $request->motivo_seleccion_otro,
            ]);

            Cotizacion::where('solicitud_compra_id', $cotizacione->solicitud_compra_id)
                ->where('id', '!=', $cotizacione->id)
                ->where('estado', 'respondio')
                ->update(['estado' => 'no_seleccionado']);

            $solicitud = $cotizacione->solicitudCompra;
            $detallesCotizacion = $cotizacione->detalles;

            $subtotal = 0;
            $totalEnvio = 0;
            $totalImpuesto = 0;
            $totalDescuento = 0;

            $itemsData = [];
            foreach ($detallesCotizacion as $det) {
                $subtotal += (float) $det->subtotal;
                $totalEnvio += (float) $det->costo_envio;
                $totalImpuesto += (float) $det->impuesto;
                $totalDescuento += (float) $det->descuento;

                $itemsData[] = [
                    'repuesto_id' => $det->repuesto_id,
                    'cantidad_solicitada' => $det->cantidad_solicitada,
                    'precio_unitario' => $det->precio_unitario,
                    'descuento' => $det->descuento,
                    'impuesto' => $det->impuesto,
                    'subtotal' => $det->subtotal,
                ];
            }

            $total = $subtotal + $totalEnvio + $totalImpuesto - $totalDescuento;

            $orden = OrdenCompra::create([
                'numero' => $this->generarNumeroOrden(),
                'solicitud_compra_id' => $cotizacione->solicitud_compra_id,
                'cotizacion_id' => $cotizacione->id,
                'proveedor_id' => $cotizacione->proveedor_id,
                'sucursal_id' => $solicitud->sucursal_id,
                'usuario_solicitante_id' => Auth::id(),
                'fecha_emision' => now(),
                'forma_pago' => null,
                'subtotal' => $subtotal,
                'costo_envio' => $totalEnvio,
                'impuesto' => $totalImpuesto,
                'descuento' => $totalDescuento,
                'total' => max(0, $total),
                'estado' => 'pendiente_aprobacion',
                'observaciones' => 'Generada desde la cotización seleccionada.',
            ]);

            foreach ($itemsData as $item) {
                DetalleOrdenCompra::create(array_merge($item, [
                    'orden_compra_id' => $orden->id,
                ]));
            }

            return $orden;
        });

        return redirect()->route('admin.ordenes-compra.show', $orden)
            ->with('success', "Cotización seleccionada. Se generó la orden {$orden->numero}.");
    }

    protected function generarNumeroOrden(): string
    {
        $anio = now()->format('Y');
        $ultimo = OrdenCompra::whereYear('created_at', $anio)
            ->orderByDesc('id')
            ->value('numero');

        if ($ultimo && preg_match('/OC-' . $anio . '-(\d+)/', $ultimo, $m)) {
            $correlativo = (int) $m[1] + 1;
        } else {
            $correlativo = 1;
        }

        return 'OC-' . $anio . '-' . str_pad($correlativo, 6, '0', STR_PAD_LEFT);
    }
}
