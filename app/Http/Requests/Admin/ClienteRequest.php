<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class ClienteRequest extends AdminFormRequest
{
    public function rules(): array
    {
        $id = $this->route('cliente')?->id;

        return [
            'nombre_completo' => ['required', 'string', 'max:150'],
            'ci' => [
                'nullable', 'string', 'max:20',
                Rule::unique('clientes', 'ci')->ignore($id)->whereNull('deleted_at'),
            ],
            'telefono' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'estado' => ['nullable', 'boolean'],
        ];
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
        ];
    }
}
