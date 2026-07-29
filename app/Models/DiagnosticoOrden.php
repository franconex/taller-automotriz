<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiagnosticoOrden extends Model
{
    protected $table = 'diagnosticos_orden';

    protected $fillable = [
        'orden_trabajo_id',
        'mecanico_id',
        'problema_encontrado',
        'causa_probable',
        'recomendacion',
        'observacion_cliente',
        'observacion_interna',
    ];

    public function ordenTrabajo(): BelongsTo
    {
        return $this->belongsTo(OrdenTrabajo::class);
    }

    public function mecanico(): BelongsTo
    {
        return $this->belongsTo(Mecanico::class);
    }
}
