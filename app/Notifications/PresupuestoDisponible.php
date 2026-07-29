<?php

namespace App\Notifications;

use App\Models\EstimacionOrden;
use App\Models\OrdenTrabajo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PresupuestoDisponible extends Notification
{
    use Queueable;

    public function __construct(public OrdenTrabajo $orden, public EstimacionOrden $estimacion) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'titulo' => 'Presupuesto disponible',
            'mensaje' => 'Tu orden ' . $this->orden->numero_orden .
                ' tiene un presupuesto estimado de ' .
                $this->estimacion->duracion_minima_minutos . ' a ' .
                $this->estimacion->duracion_maxima_minutos . ' minutos.' .
                ($this->estimacion->observacion_cliente ? ' ' . $this->estimacion->observacion_cliente : ''),
            'url' => route('cliente.seguimiento'),
            'icono' => 'bi-cash-coin',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
