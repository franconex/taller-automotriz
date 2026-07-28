<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Autorizacion extends Model
{
    protected $table = 'autorizaciones';

    protected $fillable = [
        'orden_trabajo_id',
        'usuario_solicitante_id',
        'titulo',
        'descripcion',
        'importe',
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
}
