<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class EmpleadoRequest extends AdminFormRequest
{
    public function rules(): array
    {
        $id = $this->route('empleado')?->id;
        $rolId = $this->input('rol_id');
        $rol = $rolId ? \App\Models\Rol::find($rolId) : null;
        $esMecanico = $rol && strcasecmp($rol->nombre, 'Mecánico') === 0;

        $mecanicoRules = $esMecanico ? [
            'especialidad_id' => ['required', 'exists:especialidades,id'],
            'disponibilidad' => ['nullable', 'in:disponible,ocupado,ausente'],
            'observaciones_mecanico' => ['nullable', 'string', 'max:1000'],
        ] : [];

        return array_merge([
            'sucursal_id' => ['required', 'exists:sucursales,id'],
            'rol_id' => ['required', 'exists:roles,id'],
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
            'cargo' => ['nullable', 'string', 'max:80'],
            'fecha_contratacion' => ['nullable', 'date'],
            'estado' => ['nullable', 'boolean'],
        ], $mecanicoRules);
    }

    public function attributes(): array
    {
        return [
            'sucursal_id' => 'sucursal',
            'rol_id' => 'rol',
            'nombre_completo' => 'nombre completo',
            'ci' => 'cédula de identidad',
            'telefono' => 'teléfono',
            'email' => 'correo electrónico',
            'direccion' => 'dirección',
            'cargo' => 'cargo',
            'fecha_contratacion' => 'fecha de contratación',
            'estado' => 'estado',
            'especialidad_id' => 'especialidad',
            'disponibilidad' => 'disponibilidad',
            'observaciones_mecanico' => 'observaciones',
        ];
    }
}
