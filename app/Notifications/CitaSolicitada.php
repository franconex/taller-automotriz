<?php

namespace App\Notifications;

use App\Models\Cita;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class CitaSolicitada extends Notification implements ShouldQueue
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
            'titulo' => 'Nueva cita solicitada',
            'mensaje' => 'El cliente ' . ($this->cita->cliente?->nombre_completo ?? '—') .
                ' ha solicitado una cita para el ' . $this->cita->fecha?->format('d/m/Y') .
                ' a las ' . $this->cita->hora . '.',
            'url' => route('admin.citas.index', ['estado' => 'solicitada']),
            'icono' => 'bi-calendar-plus',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
