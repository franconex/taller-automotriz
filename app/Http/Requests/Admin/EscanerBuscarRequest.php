<?php

namespace App\Http\Requests\Admin;

class EscanerBuscarRequest extends AdminFormRequest
{
    public function rules(): array
    {
        return [
            'codigo' => ['required', 'string', 'max:100'],
        ];
    }

    public function attributes(): array
    {
        return [
            'codigo' => 'código',
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.required' => 'El código es obligatorio.',
            'codigo.string' => 'El código debe ser texto.',
            'codigo.max' => 'El código no debe superar :max caracteres.',
        ];
    }
}
