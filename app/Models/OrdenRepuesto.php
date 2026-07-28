<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrdenRepuesto extends Model
{
    protected $table = 'orden_repuestos';

    protected $fillable = [
        'orden_trabajo_id',
        'repuesto_id',
        'mecanico_id',
        'cantidad',
        'estado',
        'motivo',
        'precio_unitario_snapshot',
    ];

    protected function casts(): array
    {
        return [
            'cantidad' => 'decimal:2',
            'precio_unitario_snapshot' => 'decimal:2',
        ];
    }

    public function ordenTrabajo(): BelongsTo
    {
        return $this->belongsTo(OrdenTrabajo::class);
    }

    public function repuesto(): BelongsTo
    {
        return $this->belongsTo(Repuesto::class);
    }

    public function mecanico(): BelongsTo
    {
        return $this->belongsTo(Mecanico::class);
    }
}
