<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UsuarioRequest extends AdminFormRequest
{
    public function rules(): array
    {
        $id = $this->route('usuario')?->id;
        $esCreacion = $this->isMethod('POST');

        return [
            'nombre' => ['required', 'string', 'max:100'],
            'username' => [
                'required', 'string', 'max:50',
                Rule::unique('users', 'username')->ignore($id)->whereNull('deleted_at'),
            ],
            'email' => [
                'required', 'email', 'max:100',
                Rule::unique('users', 'email')->ignore($id)->whereNull('deleted_at'),
            ],
            'rol_id' => ['required', 'exists:roles,id'],
            'sucursal_id' => ['required', 'exists:sucursales,id'],
            'empleado_id' => [
                'nullable',
                Rule::unique('users', 'empleado_id')->ignore($id)->whereNull('deleted_at'),
            ],
            'password' => array_filter([
                $esCreacion ? 'required' : 'nullable',
                'string',
                Password::min(6),
            ]),
            'password_confirmation' => array_filter([
                $esCreacion ? 'required' : 'nullable',
                'string',
                $esCreacion ? 'confirmed' : 'nullable',
            ]),
            'estado' => ['nullable', 'string', 'in:activo,inactivo'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre',
            'username' => 'nombre de usuario',
            'email' => 'correo electrónico',
            'rol_id' => 'rol',
            'sucursal_id' => 'sucursal',
            'empleado_id' => 'empleado',
            'password' => 'contraseña',
            'password_confirmation' => 'confirmación de contraseña',
            'estado' => 'estado',
        ];
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'password.min' => 'La contraseña debe tener al menos :min caracteres.',
        ]);
    }
}
