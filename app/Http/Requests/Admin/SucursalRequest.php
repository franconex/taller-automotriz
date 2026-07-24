<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class SucursalRequest extends AdminFormRequest
{
    public function rules(): array
    {
        $id = $this->route('sucursale')?->id ?? $this->route('sucursal')?->id;

        return [
            'nombre' => [
                'required', 'string', 'max:120',
                Rule::unique('sucursales', 'nombre')->ignore($id)->whereNull('deleted_at'),
            ],
            'direccion' => ['required', 'string', 'max:255'],
            'telefono' => ['required', 'string', 'max:20'],
            'horario_atencion' => ['nullable', 'string', 'max:255'],
            'estado' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre',
            'direccion' => 'dirección',
            'telefono' => 'teléfono',
            'horario_atencion' => 'horario de atención',
            'estado' => 'estado',
        ];
    }
}
