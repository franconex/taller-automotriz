<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmpleadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['nullable', 'exists:users,id'],
            'sucursal_id' => ['nullable', 'exists:sucursales,id'],
            'nombre' => ['required', 'string', 'max:50'],
            'apellido' => ['required', 'string', 'max:50'],
            'ci' => ['required', 'string', 'max:20', 'unique:empleados,ci'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'direccion' => ['nullable', 'string', 'max:200'],
            'fecha_nacimiento' => ['nullable', 'date', 'before:today'],
            'fecha_ingreso' => ['nullable', 'date'],
            'cargo' => ['nullable', 'string', 'max:80'],
            'salario' => ['nullable', 'numeric', 'min:0'],
            'estado' => ['sometimes', 'boolean'],

            'crear_usuario' => ['sometimes', 'boolean'],
            'username' => ['required_if:crear_usuario,true', 'nullable', 'string', 'max:50', 'unique:users,username'],
            'email' => ['required_if:crear_usuario,true', 'nullable', 'string', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required_if:crear_usuario,true', 'nullable', 'string', 'min:8', 'confirmed'],
            'rol_id' => ['required_if:crear_usuario,true', 'nullable', 'exists:roles,id,estado,1'],
        ];
    }

    public function messages(): array
    {
        return [
            'ci.unique' => 'El CI ya está registrado.',
            'username.required_if' => 'El nombre de usuario es obligatorio si creará una cuenta de acceso.',
            'email.required_if' => 'El correo electrónico es obligatorio si creará una cuenta de acceso.',
            'password.required_if' => 'La contraseña es obligatoria si creará una cuenta de acceso.',
            'rol_id.required_if' => 'El rol es obligatorio si creará una cuenta de acceso.',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
        ];
    }
}
