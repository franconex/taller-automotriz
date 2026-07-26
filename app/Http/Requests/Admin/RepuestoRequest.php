<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class RepuestoRequest extends AdminFormRequest
{
    public function rules(): array
    {
        $id = $this->route('repuesto')?->id;

        return [
            'codigo' => [
                'required', 'string', 'max:50',
                Rule::unique('repuestos', 'codigo')->ignore($id)->whereNull('deleted_at'),
            ],
            'codigo_barras' => [
                'nullable', 'string', 'max:50',
                Rule::unique('repuestos', 'codigo_barras')->ignore($id)->whereNull('deleted_at'),
            ],
            'codigo_fabricante' => ['nullable', 'string', 'max:50'],
            'tipo' => ['required', 'string', 'in:repuesto,herramienta'],
            'nombre' => ['required', 'string', 'max:150'],
            'categoria' => ['nullable', 'string', 'max:100'],
            'marca' => ['nullable', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'costo_compra' => ['nullable', 'numeric', 'min:0'],
            'precio_venta' => ['nullable', 'numeric', 'min:0'],
            'estado' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'codigo' => 'código',
            'codigo_barras' => 'código de barras',
            'codigo_fabricante' => 'código del fabricante',
            'tipo' => 'tipo',
            'nombre' => 'nombre',
            'categoria' => 'categoría',
            'marca' => 'marca',
            'descripcion' => 'descripción',
            'costo_compra' => 'precio de compra',
            'precio_venta' => 'precio de venta',
            'estado' => 'estado',
        ];
    }
}
