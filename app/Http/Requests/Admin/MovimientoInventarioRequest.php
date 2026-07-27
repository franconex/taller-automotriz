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
        'transferencia',
        'entrada',
        'salida',
        'ajuste',
    ];

    public function rules(): array
    {
        return [
            'inventario_id' => ['nullable', 'exists:inventarios,id'],
            'repuesto_id' => ['required', 'exists:repuestos,id'],
            'tipo' => ['required', Rule::in(self::TIPOS)],
            'cantidad' => ['required', 'integer', 'min:1'],
            'motivo' => ['required', 'string', 'max:255'],
            'orden_trabajo_id' => ['nullable', 'exists:ordenes_trabajo,id'],
            'sucursal_origen_id' => ['required', 'exists:sucursales,id'],
            'sucursal_destino_id' => ['nullable', 'exists:sucursales,id', 'different:sucursal_origen_id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'repuesto_id' => 'repuesto',
            'tipo' => 'tipo',
            'cantidad' => 'cantidad',
            'motivo' => 'motivo',
            'orden_trabajo_id' => 'orden de trabajo',
            'sucursal_origen_id' => 'sucursal origen',
            'sucursal_destino_id' => 'sucursal destino',
        ];
    }
}
