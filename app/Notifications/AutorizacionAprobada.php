<?php

namespace App\Notifications;

use App\Models\Autorizacion;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AutorizacionAprobada extends Notification
{
    use Queueable;

    public function __construct(public Autorizacion $autorizacion) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $orden = $this->autorizacion->ordenTrabajo;
        $mensaje = 'El cliente aprobó la cotización "' . $this->autorizacion->titulo . '"';
        if ($this->autorizacion->tiempo_estimado_minutos) {
            $mensaje .= ' (' . $this->autorizacion->tiempo_estimado_label . ' estimados)';
        }
        $mensaje .= '.';

        return [
            'titulo' => 'Cotización aprobada',
            'mensaje' => $mensaje,
            'url' => $orden ? route('mecanico.ordenes.show', $orden) : route('mecanico.dashboard'),
            'icono' => 'bi-check-circle',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
