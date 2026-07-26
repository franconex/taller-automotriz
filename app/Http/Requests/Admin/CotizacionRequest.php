<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class CotizacionRequest extends AdminFormRequest
{
    public function rules(): array
    {
        return [
            'solicitud_compra_id' => ['required', 'exists:solicitudes_compra,id'],
            'proveedor_id' => ['required', 'exists:proveedores,id'],
            'medio_contacto' => ['required', Rule::in(['whatsapp', 'llamada', 'correo', 'presencial', 'doc_fisico', 'otro'])],
            'nombre_contacto' => ['nullable', 'string', 'max:150'],
            'fecha_vencimiento' => ['nullable', 'date', 'after_or_equal:today'],
            'observaciones' => ['nullable', 'string', 'max:2000'],
            'archivo' => ['nullable', 'file', 'max:5120', 'mimes:pdf,jpg,jpeg,png'],
            'productos' => ['required', 'array', 'min:1'],
            'productos.*.repuesto_id' => ['required', 'exists:repuestos,id'],
            'productos.*.cantidad_solicitada' => ['required', 'integer', 'min:1'],
            'productos.*.cantidad_disponible' => ['nullable', 'integer', 'min:0'],
            'productos.*.marca_ofrecida' => ['nullable', 'string', 'max:150'],
            'productos.*.precio_unitario' => ['required', 'numeric', 'min:0'],
            'productos.*.descuento' => ['nullable', 'numeric', 'min:0'],
            'productos.*.impuesto' => ['nullable', 'numeric', 'min:0'],
            'productos.*.costo_envio' => ['nullable', 'numeric', 'min:0'],
            'productos.*.tiempo_entrega_dias' => ['nullable', 'integer', 'min:0'],
            'productos.*.garantia_dias' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'solicitud_compra_id' => 'solicitud de compra',
            'proveedor_id' => 'proveedor',
            'medio_contacto' => 'medio de contacto',
            'nombre_contacto' => 'nombre de quien respondió',
            'fecha_vencimiento' => 'fecha de vencimiento',
            'observaciones' => 'observaciones',
            'archivo' => 'archivo adjunto',
            'productos' => 'productos',
            'productos.*.repuesto_id' => 'repuesto',
            'productos.*.cantidad_solicitada' => 'cantidad solicitada',
            'productos.*.cantidad_disponible' => 'cantidad disponible',
            'productos.*.marca_ofrecida' => 'marca ofrecida',
            'productos.*.precio_unitario' => 'precio unitario',
            'productos.*.descuento' => 'descuento',
            'productos.*.impuesto' => 'impuesto',
            'productos.*.costo_envio' => 'costo de envío',
            'productos.*.tiempo_entrega_dias' => 'tiempo de entrega',
            'productos.*.garantia_dias' => 'garantía',
        ];
    }
}
