<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleSolicitudCompra extends Model
{
    protected $table = 'detalles_solicitud_compra';

    protected $fillable = [
        'solicitud_compra_id',
        'repuesto_id',
        'cantidad_solicitada',
        'stock_actual',
        'stock_minimo',
        'observaciones',
    ];

    public function solicitudCompra(): BelongsTo
    {
        return $this->belongsTo(SolicitudCompra::class, 'solicitud_compra_id');
    }

    public function repuesto(): BelongsTo
    {
        return $this->belongsTo(Repuesto::class);
    }
}
