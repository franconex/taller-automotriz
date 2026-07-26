<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class MovimientoInventarioRequest extends AdminFormRequest
{
    public const TIPOS = [
        'entrada_inicial',
        'entrada_compra',
        'salida_orden',
        'reserva',
        'liberacion_reserva',
        'consumo',
        'devolucion',
        'ajuste_positivo',
        'ajuste_negativo',
        'dañado',
        'vencido',
        'perdida',
        'devolucion_proveedor',
        'entrada',
        'salida',
        'ajuste',
    ];

    public function rules(): array
    {
        return [
            'inventario_id' => ['nullable', 'exists:inventarios,id'],
            'sucursal_id' => ['required', 'exists:sucursales,id'],
            'repuesto_id' => ['required', 'exists:repuestos,id'],
            'tipo' => ['required', Rule::in(self::TIPOS)],
            'cantidad' => ['required', 'integer', 'min:1'],
            'motivo' => ['required', 'string', 'max:255'],
            'orden_trabajo_id' => ['nullable', 'exists:ordenes_trabajo,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'sucursal_id' => 'sucursal',
            'repuesto_id' => 'repuesto',
            'tipo' => 'tipo',
            'cantidad' => 'cantidad',
            'motivo' => 'motivo',
            'orden_trabajo_id' => 'orden de trabajo',
        ];
    }
}
