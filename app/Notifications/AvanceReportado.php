<?php

namespace App\Notifications;

use App\Models\AvanceOrden;
use App\Models\OrdenTrabajo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AvanceReportado extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public OrdenTrabajo $orden, public AvanceOrden $avance) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'titulo' => 'Avance reportado',
            'mensaje' => 'Tu orden ' . $this->orden->numero_orden .
                ' tiene un nuevo avance: ' . $this->avance->titulo .
                ' (' . ($this->avance->porcentaje ?? 0) . '%).',
            'url' => route('cliente.seguimiento'),
            'icono' => 'bi-graph-up-arrow',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
