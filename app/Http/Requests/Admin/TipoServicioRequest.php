<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class TipoServicioRequest extends AdminFormRequest
{
    public function rules(): array
    {
        $id = $this->route('tipo_servicio')?->id;

        return [
            'nombre' => [
                'required', 'string', 'max:80', 'regex:/^[\pL\s]+$/u',
                Rule::unique('tipos_servicio', 'nombre')->ignore($id),
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
            'nombre.regex' => 'El nombre solo puede contener letras y espacios, sin números ni caracteres especiales.',
        ];
    }
}
