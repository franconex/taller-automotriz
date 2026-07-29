<?php

namespace App\Notifications;

use App\Models\OrdenTrabajo;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TrabajoFinalizado extends Notification
{
    use Queueable;

    public function __construct(public OrdenTrabajo $orden) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $esCliente = ! is_null($notifiable->cliente_id);

        if ($esCliente) {
            return [
                'titulo' => '¡Tu vehículo está listo! 🎉',
                'mensaje' => 'El trabajo en tu ' . ($this->orden->vehiculo?->placa ?? 'vehículo') . ' ha finalizado. Podés pasar a retirarlo por el taller.',
                'url' => route('cliente.seguimiento'),
                'icono' => 'bi-check-circle-fill',
            ];
        }

        return [
            'titulo' => 'Orden finalizada — lista para entrega',
            'mensaje' => 'La orden ' . $this->orden->numero_orden .
                ' (' . ($this->orden->vehiculo?->placa ?? '—') . ') del cliente ' .
                ($this->orden->cliente?->nombre_completo ?? '—') . ' fue finalizada por el mecánico. Vehículo listo para entregar.',
            'url' => route('admin.ordenes.show', $this->orden),
            'icono' => 'bi-check-circle-fill',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
