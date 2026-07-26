<?php

namespace App\Http\Requests\Admin;

class OrdenTrabajoDetalleRequest extends AdminFormRequest
{
    public function rules(): array
    {
        return [
            'repuesto_id' => ['required', 'exists:repuestos,id'],
            'cantidad' => ['required', 'integer', 'min:1', 'max:99999'],
            'precio_unitario' => ['nullable', 'numeric', 'min:0'],
            'descripcion' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'repuesto_id' => 'repuesto',
            'cantidad' => 'cantidad',
            'precio_unitario' => 'precio unitario',
            'descripcion' => 'descripción',
        ];
    }
}
