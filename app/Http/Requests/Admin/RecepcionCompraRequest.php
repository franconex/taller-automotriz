<?php

namespace App\Http\Requests\Admin;

class RecepcionCompraRequest extends AdminFormRequest
{
    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'exists:detalles_orden_compra,id'],
            'items.*.cantidad_recibida' => ['required', 'integer', 'min:0'],
            'items.*.cantidad_aceptada' => ['required', 'integer', 'min:0'],
            'items.*.cantidad_rechazada' => ['required', 'integer', 'min:0'],
            'items.*.motivo_rechazo' => ['nullable', 'string', 'max:500'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $items = $this->input('items', []);
            foreach ($items as $i => $item) {
                $recibida = (int) ($item['cantidad_recibida'] ?? 0);
                $aceptada = (int) ($item['cantidad_aceptada'] ?? 0);
                $rechazada = (int) ($item['cantidad_rechazada'] ?? 0);

                if ($aceptada + $rechazada !== $recibida) {
                    $validator->errors()->add(
                        "items.{$i}.cantidad_aceptada",
                        'La suma de aceptada y rechazada debe ser igual a la cantidad recibida.'
                    );
                }

                if ($rechazada > 0 && empty($item['motivo_rechazo'])) {
                    $validator->errors()->add(
                        "items.{$i}.motivo_rechazo",
                        'Debe indicar el motivo del rechazo.'
                    );
                }
            }
        });
    }

    public function attributes(): array
    {
        return [
            'items' => 'productos recibidos',
            'items.*.cantidad_recibida' => 'cantidad recibida',
            'items.*.cantidad_aceptada' => 'cantidad aceptada',
            'items.*.cantidad_rechazada' => 'cantidad rechazada',
            'items.*.motivo_rechazo' => 'motivo de rechazo',
            'observaciones' => 'observaciones',
        ];
    }
}
