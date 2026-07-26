<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class SolicitudCompraRequest extends AdminFormRequest
{
    public function rules(): array
    {
        return [
            'sucursal_id' => ['nullable', 'exists:sucursales,id'],
            'prioridad' => ['required', Rule::in(['alta', 'media', 'baja'])],
            'observaciones' => ['nullable', 'string', 'max:1000'],
            'productos' => ['required', 'array', 'min:1'],
            'productos.*.repuesto_id' => ['required', 'exists:repuestos,id'],
            'productos.*.cantidad' => ['required', 'integer', 'min:1', 'max:999999'],
            'productos.*.stock_actual' => ['nullable', 'integer', 'min:0'],
            'productos.*.stock_minimo' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'sucursal_id' => 'sucursal',
            'prioridad' => 'prioridad',
            'observaciones' => 'observaciones',
            'productos' => 'productos',
            'productos.*.repuesto_id' => 'repuesto',
            'productos.*.cantidad' => 'cantidad solicitada',
            'productos.*.stock_actual' => 'stock actual',
            'productos.*.stock_minimo' => 'stock mínimo',
        ];
    }
}
