<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmpleadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $empleadoId = $this->route('empleado');

        return [
            'sucursal_id' => ['nullable', 'exists:sucursales,id'],
            'nombre' => ['required', 'string', 'max:50'],
            'apellido' => ['required', 'string', 'max:50'],
            'ci' => ['required', 'string', 'max:20', Rule::unique('empleados', 'ci')->ignore($empleadoId)],
            'telefono' => ['nullable', 'string', 'max:20'],
            'direccion' => ['nullable', 'string', 'max:200'],
            'fecha_nacimiento' => ['nullable', 'date', 'before:today'],
            'fecha_ingreso' => ['nullable', 'date'],
            'cargo' => ['nullable', 'string', 'max:80'],
            'salario' => ['nullable', 'numeric', 'min:0'],
            'estado' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'ci.unique' => 'El CI ya está registrado.',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
        ];
    }
}
