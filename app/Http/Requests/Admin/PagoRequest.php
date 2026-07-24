<?php

namespace App\Http\Requests\Admin;

class PagoRequest extends AdminFormRequest
{
    public function rules(): array
    {
        return [
            'orden_trabajo_id' => ['required', 'exists:ordenes_trabajo,id'],
            'metodo_pago_id' => ['required', 'exists:metodos_pago,id'],
            'fecha_pago' => ['required', 'date'],
            'monto' => ['required', 'numeric', 'min:0.01'],
            'numero_comprobante' => ['nullable', 'string', 'max:80'],
            'referencia' => ['nullable', 'string', 'max:120'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'orden_trabajo_id' => 'orden de trabajo',
            'metodo_pago_id' => 'método de pago',
            'fecha_pago' => 'fecha de pago',
            'monto' => 'monto',
            'numero_comprobante' => 'número de comprobante',
            'referencia' => 'referencia',
            'observaciones' => 'observaciones',
        ];
    }
}
