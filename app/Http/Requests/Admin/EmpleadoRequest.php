<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class EmpleadoRequest extends AdminFormRequest
{
    public function rules(): array
    {
        $id = $this->route('empleado')?->id;

        return [
            'sucursal_id' => ['required', 'exists:sucursales,id'],
            'nombre_completo' => ['required', 'string', 'max:150'],
            'ci' => [
                'required', 'string', 'max:20',
                Rule::unique('empleados', 'ci')->ignore($id)->whereNull('deleted_at'),
            ],
            'telefono' => ['required', 'string', 'max:20'],
            'email' => [
                'nullable', 'email', 'max:100',
                Rule::unique('empleados', 'email')->ignore($id)->whereNull('deleted_at'),
            ],
            'direccion' => ['nullable', 'string', 'max:255'],
            'cargo' => ['required', 'string', 'max:80'],
            'fecha_contratacion' => ['nullable', 'date'],
            'estado' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'sucursal_id' => 'sucursal',
            'nombre_completo' => 'nombre completo',
            'ci' => 'cédula de identidad',
            'telefono' => 'teléfono',
            'email' => 'correo electrónico',
            'direccion' => 'dirección',
            'cargo' => 'cargo',
            'fecha_contratacion' => 'fecha de contratación',
            'estado' => 'estado',
        ];
    }
}
