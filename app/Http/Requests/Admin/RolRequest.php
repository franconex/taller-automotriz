<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class RolRequest extends AdminFormRequest
{
    public function rules(): array
    {
        $id = $this->route('role')?->id;

        return [
            'nombre' => [
                'required', 'string', 'max:50',
                'regex:/^[\pL\s]+$/u',
                Rule::unique('roles', 'nombre')->ignore($id),
            ],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'estado' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre',
            'descripcion' => 'descripción',
            'estado' => 'estado',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.regex' => 'El nombre del rol solo puede contener letras y espacios, no se permiten números ni caracteres especiales.',
        ];
    }
}
