<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class ProveedorRequest extends AdminFormRequest
{
    public function rules(): array
    {
        $id = $this->route('proveedore')?->id;

        return [
            'nombre_empresa' => [
                'required', 'string', 'max:150',
                'regex:/^[\pL\s]+$/u',
                Rule::unique('proveedores', 'nombre_empresa')->ignore($id)->whereNull('deleted_at'),
            ],
            'nit' => ['nullable', 'string', 'max:30', 'regex:/^\d+$/u'],
            'contacto' => ['required', 'string', 'max:120', 'regex:/^[\pL\s]+$/u'],
            'codigo_pais' => ['required', 'string', 'in:+591'],
            'telefono_numero' => ['required', 'string', 'regex:/^\d+$/', 'max:15'],
            'email' => ['required', 'email', 'max:100'],
            'direccion' => ['required', 'string', 'max:500'],
            'estado' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre_empresa' => 'nombre de la empresa',
            'nit' => 'NIT',
            'contacto' => 'persona de contacto',
            'codigo_pais' => 'código de país',
            'telefono_numero' => 'teléfono',
            'email' => 'correo electrónico',
            'direccion' => 'dirección',
            'estado' => 'estado',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_empresa.regex' => 'El nombre de la empresa solo puede contener letras y espacios.',
            'contacto.regex' => 'El nombre de contacto solo puede contener letras y espacios.',
            'nit.regex' => 'El NIT solo debe contener dígitos.',
            'codigo_pais.in' => 'El código de país seleccionado no es válido.',
            'telefono_numero.regex' => 'El teléfono solo debe contener dígitos.',
            'email.email' => 'El correo electrónico debe tener un formato válido.',
        ];
    }
}
