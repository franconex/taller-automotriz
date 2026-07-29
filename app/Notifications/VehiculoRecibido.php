<?php

namespace App\Notifications;

use App\Models\OrdenTrabajo;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class VehiculoRecibido extends Notification
{
    use Queueable;

    public function __construct(public OrdenTrabajo $orden) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'titulo' => 'Vehículo recibido en taller',
            'mensaje' => 'El vehículo ' . ($this->orden->vehiculo?->placa ?? '—') .
                ' de la orden ' . $this->orden->numero_orden . ' ya llegó al taller.',
            'url' => route('mecanico.ordenes.show', $this->orden),
            'icono' => 'bi-car-front',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
