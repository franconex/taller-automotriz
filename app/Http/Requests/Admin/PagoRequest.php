<?php

namespace App\Http\Requests\Admin;

use App\Models\OrdenTrabajo;
use Illuminate\Validation\Validator;

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

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $ordenId = $this->input('orden_trabajo_id');
            if (!$ordenId) return;

            $orden = OrdenTrabajo::find($ordenId);
            if (!$orden) return;

            if ($orden->estado !== 'finalizada') {
                $validator->errors()->add(
                    'orden_trabajo_id',
                    'Solo se pueden registrar pagos en órdenes con estado "Finalizada" (listas para entregar).'
                );
            }

            $totalPagado = (float) $orden->pagos()
                ->where('estado', 'confirmado')
                ->sum('monto');

            $montoIngresado = (float) $this->input('monto');
            $saldoPendiente = max(0, (float) $orden->total_general - $totalPagado);

            if ($montoIngresado > $saldoPendiente) {
                $validator->errors()->add(
                    'monto',
                    "El monto excede el saldo pendiente. Saldo disponible: Bs " . number_format($saldoPendiente, 2, ',', '.')
                );
            }
        });
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
