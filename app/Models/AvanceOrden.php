<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AvanceOrden extends Model
{
    protected $table = 'avances_orden';

    protected $fillable = [
        'orden_trabajo_id',
        'mecanico_id',
        'titulo',
        'descripcion',
        'estado',
        'porcentaje',
        'nota_cliente',
        'nota_interna',
        'visible_cliente',
    ];

    protected function casts(): array
    {
        return [
            'visible_cliente' => 'boolean',
            'porcentaje' => 'integer',
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
