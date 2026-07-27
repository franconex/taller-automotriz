<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class ClienteRequest extends AdminFormRequest
{
    public function rules(): array
    {
        $id = $this->route('cliente')?->id;
        $esCreacion = ! $id;

        return array_merge([
            'nombre_completo' => ['required', 'string', 'max:150'],
            'ci' => [
                'nullable', 'string', 'max:20',
                Rule::unique('clientes', 'ci')->ignore($id)->whereNull('deleted_at'),
            ],
            'telefono' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'estado' => ['nullable', 'boolean'],
        ], $esCreacion ? [
            'vehiculos' => ['required', 'array', 'min:1'],
            'vehiculos.*.marca' => ['required', 'string', 'max:100'],
            'vehiculos.*.modelo' => ['required', 'string', 'max:100'],
            'vehiculos.*.placa' => [
                'required', 'string', 'max:20',
                Rule::unique('vehiculos', 'placa')->whereNull('deleted_at'),
            ],
            'vehiculos.*.anio' => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'vehiculos.*.color' => ['nullable', 'string', 'max:50'],
        ] : []);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $placas = $this->input('vehiculos.*.placa');
            if (is_array($placas) && count($placas) !== count(array_unique($placas))) {
                $validator->errors()->add('vehiculos.*.placa', 'No puedes registrar dos vehículos con la misma placa.');
            }
        });
    }

    public function attributes(): array
    {
        return [
            'nombre_completo' => 'nombre completo',
            'ci' => 'cédula de identidad',
            'telefono' => 'teléfono',
            'email' => 'correo electrónico',
            'direccion' => 'dirección',
            'estado' => 'estado',
            'vehiculos.*.marca' => 'marca',
            'vehiculos.*.modelo' => 'modelo',
            'vehiculos.*.placa' => 'placa',
            'vehiculos.*.anio' => 'año',
            'vehiculos.*.color' => 'color',
        ];
    }
}
