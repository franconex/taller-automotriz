<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Autorizacion extends Model
{
    protected $table = 'autorizaciones';

    protected $fillable = [
        'orden_trabajo_id',
        'cita_id',
        'usuario_solicitante_id',
        'titulo',
        'descripcion',
        'diagnostico_mecanico',
        'foto_diagnostico',
        'importe',
        'tiempo_estimado_minutos',
        'tiempo_estimado_unidad',
        'mano_de_obra',
        'estado',
        'fecha_solicitud',
        'fecha_respuesta',
        'comentario_cliente',
        'respondido_por_id',
    ];

    protected function casts(): array
    {
        return [
            'importe' => 'decimal:2',
            'tiempo_estimado_minutos' => 'integer',
            'mano_de_obra' => 'decimal:2',
            'fecha_solicitud' => 'datetime',
            'fecha_respuesta' => 'datetime',
        ];
    }

    public const ESTADOS = [
        'pendiente' => 'Pendiente',
        'autorizada' => 'Autorizada',
        'rechazada' => 'Rechazada',
        'requiere_informacion' => 'Requiere información',
        'cancelada' => 'Cancelada',
    ];

    public function ordenTrabajo(): BelongsTo
    {
        return $this->belongsTo(OrdenTrabajo::class);
    }

    public function cita(): BelongsTo
    {
        return $this->belongsTo(Cita::class);
    }

    public function servicios()
    {
        return $this->hasMany(OrdenServicio::class);
    }

    public function repuestos()
    {
        return $this->hasMany(OrdenRepuesto::class);
    }

    public function usuarioSolicitante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_solicitante_id');
    }

    public function respondidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'respondido_por_id');
    }

    public function getEstadoLabelAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? ucfirst($this->estado);
    }

    public function esRespondible(): bool
    {
        return in_array($this->estado, ['pendiente', 'requiere_informacion'], true);
    }

    public function esFinal(): bool
    {
        return in_array($this->estado, ['autorizada', 'rechazada', 'cancelada'], true);
    }

    public function getTiempoEstimadoLabelAttribute(): string
    {
        $min = $this->tiempo_estimado_minutos;
        $unidad = $this->tiempo_estimado_unidad;

        if (! $min) return '—';

        if ($unidad === 'dias') {
            $dias = round($min / 1440, 1);
            return ($dias == intval($dias) ? intval($dias) : number_format($dias, 1)) . ' día(s)';
        }

        if ($unidad === 'horas') {
            $horas = round($min / 60, 1);
            return ($horas == intval($horas) ? intval($horas) : number_format($horas, 1)) . ' hora(s)';
        }

        if ($min >= 1440) {
            $dias = round($min / 1440, 1);
            return ($dias == intval($dias) ? intval($dias) : number_format($dias, 1)) . ' día(s)';
        }

        if ($min >= 60) {
            $horas = round($min / 60, 1);
            return ($horas == intval($horas) ? intval($horas) : number_format($horas, 1)) . ' hora(s)';
        }

        return intval($min) . ' min';
    }
}
