<?php

namespace App\Http\Requests\Admin;

class EntradaInventarioRequest extends AdminFormRequest
{
    public function rules(): array
    {
        return [
            'repuesto_id' => ['required', 'exists:repuestos,id'],
            'sucursal_id' => ['nullable', 'exists:sucursales,id'],
            'cantidad' => ['required', 'integer', 'min:1'],
            'precio_unitario' => ['nullable', 'numeric', 'min:0'],
            'proveedor_id' => ['nullable', 'exists:proveedores,id'],
            'factura' => ['nullable', 'string', 'max:50'],
            'observaciones' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'repuesto_id' => 'repuesto',
            'sucursal_id' => 'sucursal',
            'cantidad' => 'cantidad',
            'precio_unitario' => 'precio unitario',
            'proveedor_id' => 'proveedor',
            'factura' => 'factura',
            'observaciones' => 'observaciones',
        ];
    }
}
