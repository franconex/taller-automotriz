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
                Rule::unique('roles', 'nombre')->ignore($id)->whereNull('deleted_at'),
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
}
