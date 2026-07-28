<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_completo' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'ci' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'terminos' => ['accepted'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre_completo' => 'nombre completo',
            'email' => 'correo electrónico',
            'telefono' => 'teléfono',
            'ci' => 'CI',
            'password' => 'contraseña',
            'terminos' => 'términos y condiciones',
        ];
    }

    public function messages(): array
    {
        return [
            'terminos.accepted' => 'Debes aceptar los términos y condiciones.',
        ];
    }
}
