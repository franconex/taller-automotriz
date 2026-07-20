<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rolId = $this->route('rol');

        return [
            'nombre' => ['required', 'string', 'max:50', Rule::unique('roles', 'nombre')->ignore($rolId)],
            'descripcion' => ['nullable', 'string', 'max:200'],
            'estado' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.unique' => 'El nombre del rol ya existe.',
        ];
    }
}
