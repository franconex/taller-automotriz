<?php

namespace App\Notifications;

use App\Models\Cita;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CitaConfirmada extends Notification
{
    use Queueable;

    public function __construct(public Cita $cita) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $mecanico = $this->cita->mecanico?->empleado?->nombre_completo ?? '—';
        $orden = $this->cita->ordenTrabajo?->numero_orden ?? '—';

        return [
            'titulo' => 'Cita confirmada',
            'mensaje' => 'Tu cita para el ' . $this->cita->fecha?->format('d/m/Y') .
                ' a las ' . $this->cita->hora . ' fue confirmada.' .
                ($mecanico !== '—' ? ' Mecánico: ' . $mecanico . '.' : '') .
                ($orden !== '—' ? ' Orden: ' . $orden . '.' : ''),
            'url' => route('cliente.citas'),
            'icono' => 'bi-calendar-check',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
