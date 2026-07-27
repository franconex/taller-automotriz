<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class AdminFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function attributes(): array
    {
        return [];
    }

    protected function failedValidation(Validator $validator): void
    {
        if ($this->expectsJson() || $this->ajax()) {
            throw new HttpResponseException(
                response()->json([
                    'ok' => false,
                    'message' => 'Error de validación.',
                    'errors' => $validator->errors()->toArray(),
                ], 422)
            );
        }

        parent::failedValidation($validator);
    }

    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'required_if' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser texto.',
            'max.string' => 'El campo :attribute no debe superar :max caracteres.',
            'max.file' => 'El :attribute no debe superar :max kilobytes.',
            'max.numeric' => 'El campo :attribute no debe ser mayor que :max.',
            'max' => 'El campo :attribute no debe superar :max.',
            'min.string' => 'El campo :attribute debe tener al menos :min caracteres.',
            'min.numeric' => 'El campo :attribute debe ser al menos :min.',
            'min.file' => 'El :attribute debe tener al menos :min kilobytes.',
            'min' => 'El campo :attribute debe tener al menos :min.',
            'email' => 'El campo :attribute debe ser un correo válido.',
            'unique' => 'El :attribute ya está registrado.',
            'exists' => 'El :attribute seleccionado no es válido.',
            'numeric' => 'El campo :attribute debe ser numérico.',
            'integer' => 'El campo :attribute debe ser un número entero.',
            'date' => 'El campo :attribute debe ser una fecha válida.',
            'boolean' => 'El campo :attribute debe ser verdadero o falso.',
            'confirmed' => 'La confirmación de :attribute no coincide.',
            'image' => 'El :attribute debe ser una imagen.',
            'mimes' => 'El :attribute debe ser un archivo de tipo: :values.',
            'file' => 'El :attribute debe ser un archivo.',
            'uploaded' => 'El :attribute no se pudo subir.',
            'size.file' => 'El :attribute debe tener :size kilobytes.',
            'mimetypes' => 'El :attribute debe ser un archivo de tipo: :values.',
            'after_or_equal' => 'El :attribute debe ser una fecha posterior o igual a :date.',
            'before_or_equal' => 'El :attribute debe ser una fecha anterior o igual a :date.',
            'after' => 'El :attribute debe ser una fecha posterior a :date.',
            'before' => 'El :attribute debe ser una fecha anterior a :date.',
            'in' => 'El :attribute seleccionado no es válido.',
            'regex' => 'El formato del :attribute no es válido.',
            'url' => 'El :attribute debe ser una URL válida.',
        ];
    }
}
