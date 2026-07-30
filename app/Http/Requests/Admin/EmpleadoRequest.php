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
            'especialidad' => ['nullable', 'integer', 'exists:especialidades,id'],
            'disponibilidad' => ['nullable', 'in:disponible,ocupado,ausente'],
            'observaciones_mecanico' => ['nullable', 'string', 'max:1000'],
        ] : [];

        return array_merge([
            'sucursal_id' => ['required', 'exists:sucursales,id'],
            'rol_id' => ['required', 'exists:roles,id'],
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
                'required', 'string', 'max:20', 'regex:/^\d+$/u',
                Rule::unique('empleados', 'ci')->ignore($id)->whereNull('deleted_at'),
            ],
            'codigo_pais' => ['required', 'string', 'in:+591'],
            'telefono_numero' => ['required', 'string', 'regex:/^\d+$/', 'max:15'],
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
            'codigo_pais' => 'código de país',
            'telefono_numero' => 'número de teléfono',
            'email' => 'correo electrónico',
            'direccion' => 'dirección',
            'cargo' => 'cargo',
            'fecha_contratacion' => 'fecha de contratación',
            'estado' => 'estado',
            'especialidad' => 'especialidad',
            'disponibilidad' => 'disponibilidad',
            'observaciones_mecanico' => 'observaciones',
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
        ];
    }
}
