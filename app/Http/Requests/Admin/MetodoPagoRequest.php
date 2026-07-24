<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class MetodoPagoRequest extends AdminFormRequest
{
    public function rules(): array
    {
        $id = $this->route('metodoPago')?->id;

        return [
            'nombre' => [
                'required', 'string', 'max:50',
                Rule::unique('metodos_pago', 'nombre')->ignore($id),
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
}
