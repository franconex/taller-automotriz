<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleOrdenTrabajo extends Model
{
    protected $table = 'detalles_orden_trabajo';

    protected $fillable = [
        'orden_trabajo_id',
        'tipo',
        'servicio_id',
        'repuesto_id',
        'asignacion_trabajo_id',
        'descripcion',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'cantidad' => 'decimal:2',
            'precio_unitario' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function ordenTrabajo(): BelongsTo
    {
        return $this->belongsTo(OrdenTrabajo::class);
    }

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class);
    }

    public function repuesto(): BelongsTo
    {
        return $this->belongsTo(Repuesto::class);
    }

    public function asignacionTrabajo(): BelongsTo
    {
        return $this->belongsTo(AsignacionTrabajo::class);
    }
}
