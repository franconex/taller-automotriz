<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $clienteId = $this->route('cliente');

        return [
            'nombre' => ['required', 'string', 'max:50'],
            'apellido' => ['required', 'string', 'max:50'],
            'ci' => ['required', 'string', 'max:20', Rule::unique('clientes', 'ci')->ignore($clienteId)],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'string', 'email', 'max:100'],
            'direccion' => ['nullable', 'string', 'max:200'],
            'nit' => ['nullable', 'string', 'max:30'],
            'razon_social' => ['nullable', 'string', 'max:100'],
            'observaciones' => ['nullable', 'string'],
            'estado' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'ci.unique' => 'El CI ya está registrado para otro cliente.',
        ];
    }
}
