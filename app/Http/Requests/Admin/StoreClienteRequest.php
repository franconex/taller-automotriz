<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:50'],
            'apellido' => ['required', 'string', 'max:50'],
            'ci' => ['required', 'string', 'max:20', 'unique:clientes,ci'],
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
