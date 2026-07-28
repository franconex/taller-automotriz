<?php

namespace App\Notifications;

use App\Models\OrdenTrabajo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class OrdenAsignada extends Notification implements ShouldQueue
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
            'titulo' => 'Nueva orden asignada',
            'mensaje' => 'Se te ha asignado la orden ' . $this->orden->numero_orden .
                ' para el vehículo ' . ($this->orden->vehiculo?->placa ?? '—') . '.',
            'url' => route('mecanico.ordenes.show', $this->orden),
            'icono' => 'bi-clipboard-check',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
