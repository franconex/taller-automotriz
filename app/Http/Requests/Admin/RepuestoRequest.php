<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class RepuestoRequest extends AdminFormRequest
{
    public function rules(): array
    {
        $id = $this->route('repuesto')?->id;

        return [
            'codigo' => [
                'required', 'string', 'max:50',
                Rule::unique('repuestos', 'codigo')->ignore($id)->whereNull('deleted_at'),
            ],
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'proveedor_id' => ['nullable', 'exists:proveedores,id'],
            'costo_compra' => ['required', 'numeric', 'min:0'],
            'precio_venta' => ['required', 'numeric', 'min:0'],
            'stock_minimo' => ['nullable', 'integer', 'min:0'],
            'estado' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'codigo' => 'código',
            'nombre' => 'nombre',
            'descripcion' => 'descripción',
            'proveedor_id' => 'proveedor',
            'costo_compra' => 'costo',
            'precio_venta' => 'precio de venta',
            'stock_minimo' => 'stock mínimo',
            'estado' => 'estado',
        ];
    }
}
