<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class PerfilRequest extends AdminFormRequest
{
    public function rules(): array
    {
        $user = $this->user();

        return [
            'username' => [
                'required', 'string', 'max:50',
                Rule::unique('users', 'username')->ignore($user->id)->whereNull('deleted_at'),
            ],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'username' => 'nombre de usuario',
            'foto' => 'foto de perfil',
        ];
    }
}
