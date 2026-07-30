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
            'marca' => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'modelo' => ['required', 'string', 'max:100'],
            'placa' => [
                'required', 'string', 'max:20',
                'regex:/^\d{3,4}[A-Za-z]{3}$/',
                Rule::unique('vehiculos', 'placa')->ignore($id)->whereNull('deleted_at'),
            ],
            'anio' => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'color' => ['nullable', 'string', 'max:50', 'regex:/^(?!.*#).*$/i'],
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
            'marca' => 'marca',
            'modelo' => 'modelo',
            'placa' => 'placa',
            'anio' => 'año',
            'color' => 'color',
            'numero_chasis' => 'número de chasis',
            'kilometraje_actual' => 'kilometraje actual',
            'observaciones' => 'observaciones',
            'estado' => 'estado',
        ];
    }

    public function messages(): array
    {
        return [
            'marca.regex' => 'La marca solo debe contener letras, sin números ni caracteres especiales.',
            'placa.regex' => 'La placa debe tener 3 o 4 números seguidos de 3 letras (ej: 1234ABC).',
            'color.regex' => 'El color debe ser un nombre válido, no un código hexadecimal.',
        ];
    }
}
