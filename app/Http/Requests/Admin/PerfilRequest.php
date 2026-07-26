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
        ];
    }

    public function attributes(): array
    {
        return [
            'username' => 'nombre de usuario',
        ];
    }
}
