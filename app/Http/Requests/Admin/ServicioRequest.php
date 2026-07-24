<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class ServicioRequest extends AdminFormRequest
{
    public function rules(): array
    {
        $id = $this->route('servicio')?->id;

        return [
            'tipo_servicio_id' => ['required', 'exists:tipos_servicio,id'],
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'precio_base' => ['required', 'numeric', 'min:0'],
            'duracion_estimada_minutos' => ['nullable', 'integer', 'min:0'],
            'estado' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'tipo_servicio_id' => 'tipo de servicio',
            'nombre' => 'nombre',
            'descripcion' => 'descripción',
            'precio_base' => 'precio base',
            'duracion_estimada_minutos' => 'duración estimada',
            'estado' => 'estado',
        ];
    }
}
