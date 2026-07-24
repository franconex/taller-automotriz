<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class MecanicoRequest extends AdminFormRequest
{
    public function rules(): array
    {
        $id = $this->route('mecanico')?->id;

        return [
            'empleado_id' => [
                'required', 'exists:empleados,id',
                Rule::unique('mecanicos', 'empleado_id')->ignore($id)->whereNull('deleted_at'),
                function ($attribute, $value, $fail) {
                    $empleado = \App\Models\Empleado::find($value);
                    if ($empleado && $empleado->rol && strcasecmp($empleado->rol->nombre, 'Mecánico') !== 0) {
                        $fail('El empleado seleccionado debe tener el rol Mecánico.');
                    }
                },
            ],
            'especialidad_id' => ['required', 'exists:especialidades,id'],
            'disponibilidad' => ['required', 'in:disponible,ocupado,ausente'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'empleado_id' => 'empleado',
            'especialidad_id' => 'especialidad',
            'disponibilidad' => 'disponibilidad',
            'observaciones' => 'observaciones',
        ];
    }
}
