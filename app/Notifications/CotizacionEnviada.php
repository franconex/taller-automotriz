<?php

namespace App\Notifications;

use App\Models\Autorizacion;
use App\Models\Cita;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CotizacionEnviada extends Notification
{
    use Queueable;

    public function __construct(
        public Autorizacion $autorizacion,
        public Cita $cita
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $orden = $this->autorizacion->ordenTrabajo;

        $mensaje = 'El mecánico envió una cotización para tu ' . ($orden?->numero_orden ? 'orden ' . $orden->numero_orden : 'vehículo') . ': "' . $this->autorizacion->titulo . '" por $' . number_format($this->autorizacion->importe, 2);
        if ($this->autorizacion->tiempo_estimado_minutos) {
            $mensaje .= ' con tiempo estimado de ' . $this->autorizacion->tiempo_estimado_label;
        }
        $mensaje .= '.';

        return [
            'titulo' => 'Cotización disponible',
            'mensaje' => $mensaje,
            'url' => route('cliente.autorizaciones'),
            'icono' => 'bi-file-earmark-text',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
