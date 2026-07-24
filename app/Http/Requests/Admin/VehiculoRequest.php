<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class VehiculoRequest extends AdminFormRequest
{
    public function rules(): array
    {
        $id = $this->route('vehiculo')?->id;

        return [
            'cliente_id' => ['required', 'exists:clientes,id'],
            'modelo_vehiculo_id' => ['required', 'exists:modelos_vehiculos,id'],
            'placa' => [
                'required', 'string', 'max:20',
                Rule::unique('vehiculos', 'placa')->ignore($id)->whereNull('deleted_at'),
            ],
            'anio' => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'color' => ['nullable', 'string', 'max:50'],
            'numero_chasis' => [
                'nullable', 'string', 'max:50',
                Rule::unique('vehiculos', 'numero_chasis')->ignore($id)->whereNull('deleted_at'),
            ],
            'kilometraje_actual' => ['nullable', 'integer', 'min:0'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
            'estado' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'cliente_id' => 'cliente',
            'modelo_vehiculo_id' => 'modelo',
            'placa' => 'placa',
            'anio' => 'año',
            'color' => 'color',
            'numero_chasis' => 'número de chasis',
            'kilometraje_actual' => 'kilometraje actual',
            'observaciones' => 'observaciones',
            'estado' => 'estado',
        ];
    }
}
