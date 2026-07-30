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
            'nombre_completo' => [
                'required', 'string', 'max:150',
                'regex:/^[\pL\s]+$/u',
                function ($attribute, $value, $fail) {
                    if (preg_match('/\s{2,}/', $value)) {
                        $fail('El nombre completo no debe contener espacios dobles.');
                    }
                },
            ],
            'ci' => [
                'nullable', 'string', 'max:20', 'regex:/^\d+$/u',
                Rule::unique('clientes', 'ci')->ignore($id)->whereNull('deleted_at'),
            ],
            'codigo_pais' => ['required', 'string', 'in:+591'],
            'telefono_numero' => ['required', 'string', 'regex:/^\d+$/', 'max:15'],
            'email' => ['nullable', 'email', 'max:100'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'estado' => ['nullable', 'boolean'],
        ], $esCreacion ? [
            'vehiculos' => ['required', 'array', 'min:1'],
            'vehiculos.*.marca' => ['required', 'string', 'max:100'],
            'vehiculos.*.modelo' => ['required', 'string', 'max:100'],
            'vehiculos.*.placa' => [
                'required', 'string', 'max:20',
                'regex:/^\d{3,4}[A-Za-z]{3}$/',
                Rule::unique('vehiculos', 'placa')->whereNull('deleted_at'),
            ],
            'vehiculos.*.anio' => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'vehiculos.*.color' => ['nullable', 'string', 'max:50', 'regex:/^(?!.*#).*$/i'],
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
            'codigo_pais' => 'código de país',
            'telefono_numero' => 'número de teléfono',
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

    public function messages(): array
    {
        return [
            'nombre_completo.regex' => 'El nombre completo solo puede contener letras y espacios, no se permiten números ni caracteres especiales.',
            'ci.regex' => 'La cédula de identidad solo debe contener dígitos.',
            'codigo_pais.in' => 'El código de país seleccionado no es válido.',
            'telefono_numero.regex' => 'El número de teléfono solo debe contener dígitos.',
            'email.email' => 'El correo electrónico debe tener un formato válido (ej: usuario@dominio.com).',
            'vehiculos.*.placa.regex' => 'La placa debe tener 3 o 4 números seguidos de 3 letras (ej: 1234ABC).',
            'vehiculos.*.color.regex' => 'El color debe ser un nombre válido, no un código hexadecimal.',
        ];
    }
}
