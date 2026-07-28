<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EstimacionOrden extends Model
{
    protected $table = 'estimaciones_orden';

    protected $fillable = [
        'orden_trabajo_id',
        'mecanico_id',
        'duracion_minima_minutos',
        'duracion_maxima_minutos',
        'fecha_estimada_entrega',
        'motivo',
        'observacion_cliente',
    ];

    protected function casts(): array
    {
        return [
            'fecha_estimada_entrega' => 'datetime',
        ];
    }

    public function ordenTrabajo(): BelongsTo
    {
        return $this->belongsTo(OrdenTrabajo::class);
    }

    public function mecanico(): BelongsTo
    {
        return $this->belongsTo(Mecanico::class);
    }
}
