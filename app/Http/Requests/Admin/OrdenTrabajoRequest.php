<?php

namespace App\Http\Requests\Admin;

class OrdenTrabajoRequest extends AdminFormRequest
{
    public function rules(): array
    {
        return [
            'cliente_id' => ['required', 'exists:clientes,id'],
            'vehiculo_id' => ['required', 'exists:vehiculos,id'],
            'sucursal_id' => ['required', 'exists:sucursales,id'],
            'descripcion_problema' => ['required', 'string', 'max:2000'],
            'descuento' => ['nullable', 'numeric', 'min:0'],
            'estado' => ['nullable', 'string', 'in:recibida,diagnostico,en_proceso,finalizada,entregada,anulada'],
        ];
    }

    public function attributes(): array
    {
        return [
            'cliente_id' => 'cliente',
            'vehiculo_id' => 'vehículo',
            'sucursal_id' => 'sucursal',
            'descripcion_problema' => 'descripción del problema',
            'descuento' => 'descuento',
            'estado' => 'estado',
        ];
    }
}
