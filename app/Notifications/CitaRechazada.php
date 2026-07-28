<?php

namespace App\Notifications;

use App\Models\Cita;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CitaRechazada extends Notification
{
    use Queueable;

    public function __construct(public Cita $cita) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'titulo' => 'Cita rechazada',
            'mensaje' => 'Tu cita para el ' . $this->cita->fecha?->format('d/m/Y') .
                ' a las ' . $this->cita->hora . ' fue rechazada.' .
                ($this->cita->cancelado_motivo ? ' Motivo: ' . $this->cita->cancelado_motivo : ''),
            'url' => route('cliente.citas'),
            'icono' => 'bi-calendar-x',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
