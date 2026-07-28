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
            'tiempo_estimado_horas' => ['nullable', 'numeric', 'min:0', 'max:999.9'],
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
            'tiempo_estimado_horas' => 'tiempo estimado',
            'estado' => 'estado',
        ];
    }
}
