<?php

namespace App\Notifications;

use App\Models\Cita;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class CitaSolicitada extends Notification
{
    use Queueable;

    public function __construct(public Cita $cita) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $esMecanico = $notifiable->tieneRol('Mecánico');

        return [
            'titulo' => $esMecanico ? 'Nueva cita asignada' : 'Nueva cita solicitada',
            'mensaje' => $esMecanico
                ? 'Te asignaron una cita para el ' . $this->cita->fecha?->format('d/m/Y') .
                  ' a las ' . $this->cita->hora . ' — ' . ($this->cita->cliente?->nombre_completo ?? '—')
                : 'El cliente ' . ($this->cita->cliente?->nombre_completo ?? '—') .
                  ' ha solicitado una cita para el ' . $this->cita->fecha?->format('d/m/Y') .
                  ' a las ' . $this->cita->hora . '.',
            'url' => $esMecanico
                ? route('mecanico.ordenes.index')
                : route('admin.citas.index') . '?cita=' . $this->cita->id,
            'icono' => 'bi-calendar-plus',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
