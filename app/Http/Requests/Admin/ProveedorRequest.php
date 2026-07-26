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
                Rule::unique('proveedores', 'nombre_empresa')->ignore($id)->whereNull('deleted_at'),
            ],
            'contacto' => ['required', 'string', 'max:120'],
            'telefono' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:100'],
            'nit' => ['nullable', 'string', 'max:30'],
            'direccion' => ['required', 'string', 'max:500'],
            'estado' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre_empresa' => 'nombre de la empresa',
            'contacto' => 'persona de contacto',
            'telefono' => 'teléfono',
            'email' => 'correo electrónico',
            'nit' => 'NIT',
            'direccion' => 'dirección',
            'estado' => 'estado',
        ];
    }
}
