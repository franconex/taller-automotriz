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
                'regex:/^[\pL\s]+$/u',
                Rule::unique('sucursales', 'nombre')->ignore($id)->whereNull('deleted_at'),
            ],
            'direccion' => ['required', 'string', 'max:255'],
            'codigo_pais' => ['required', 'string', 'in:+591'],
            'telefono_numero' => ['required', 'string', 'regex:/^\d+$/', 'max:15'],
            'horario_atencion' => ['nullable', 'json'],
            'latitud' => ['nullable', 'numeric', 'between:-90,90'],
            'longitud' => ['nullable', 'numeric', 'between:-180,180'],
            'estado' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre',
            'direccion' => 'dirección',
            'codigo_pais' => 'código de país',
            'telefono_numero' => 'número de teléfono',
            'horario_atencion' => 'horario de atención',
            'latitud' => 'latitud',
            'longitud' => 'longitud',
            'estado' => 'estado',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.regex' => 'El nombre solo puede contener letras y espacios, no se permiten números ni caracteres especiales.',
            'codigo_pais.in' => 'El código de país seleccionado no es válido.',
            'telefono_numero.regex' => 'El número de teléfono solo debe contener dígitos.',
        ];
    }
}
