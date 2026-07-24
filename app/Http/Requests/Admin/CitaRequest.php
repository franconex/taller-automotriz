<?php

namespace App\Http\Requests\Admin;

class CitaRequest extends AdminFormRequest
{
    public function rules(): array
    {
        return [
            'cliente_id' => ['required', 'exists:clientes,id'],
            'vehiculo_id' => ['required', 'exists:vehiculos,id'],
            'sucursal_id' => ['required', 'exists:sucursales,id'],
            'fecha' => ['required', 'date'],
            'hora' => ['required', 'date_format:H:i'],
            'tipo' => ['required', 'string', 'in:diagnostico,mantenimiento,reparacion,otro'],
            'descripcion_problema' => ['required', 'string', 'max:1000'],
            'costo_consulta' => ['nullable', 'numeric', 'min:0'],
            'deja_vehiculo' => ['nullable', 'boolean'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
            'estado' => ['nullable', 'string', 'in:pendiente,confirmada,atendida,cancelada'],
        ];
    }

    public function attributes(): array
    {
        return [
            'cliente_id' => 'cliente',
            'vehiculo_id' => 'vehículo',
            'sucursal_id' => 'sucursal',
            'fecha' => 'fecha',
            'hora' => 'hora',
            'tipo' => 'tipo',
            'descripcion_problema' => 'descripción del problema',
            'costo_consulta' => 'costo de consulta',
            'deja_vehiculo' => 'deja vehículo',
            'observaciones' => 'observaciones',
            'estado' => 'estado',
        ];
    }
}
