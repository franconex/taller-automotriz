<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('usuario');

        return [
            'rol_id' => ['required', 'exists:roles,id,estado,1'],
            'nombre' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($userId)],
            'email' => ['required', 'string', 'email', 'max:100', Rule::unique('users', 'email')->ignore($userId)],
            'estado' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'rol_id.exists' => 'El rol seleccionado no existe o está inactivo.',
            'username.unique' => 'El nombre de usuario ya está registrado.',
            'email.unique' => 'El correo electrónico ya está registrado.',
        ];
    }
}
