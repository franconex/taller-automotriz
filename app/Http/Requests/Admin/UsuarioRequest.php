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
            'username' => [
                'required', 'string', 'max:50',
                Rule::unique('users', 'username')->ignore($id)->whereNull('deleted_at'),
            ],
            'empleado_id' => [
                'required',
                Rule::unique('users', 'empleado_id')->ignore($id)->whereNull('deleted_at'),
            ],
            'password' => array_filter([
                $esCreacion ? 'required' : 'nullable',
                'string',
                Password::min(6),
                $esCreacion ? 'confirmed' : null,
            ]),
            'password_confirmation' => array_filter([
                $esCreacion ? 'required' : 'nullable',
                'string',
            ]),
            'estado' => ['nullable', 'string', 'in:activo,inactivo'],
        ];
    }

    public function attributes(): array
    {
        return [
            'username' => 'nombre de usuario',
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
