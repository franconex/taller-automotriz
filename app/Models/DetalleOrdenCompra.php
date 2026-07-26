<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleOrdenCompra extends Model
{
    protected $table = 'detalles_orden_compra';

    protected $fillable = [
        'orden_compra_id',
        'repuesto_id',
        'cantidad_solicitada',
        'precio_unitario',
        'descuento',
        'impuesto',
        'subtotal',
        'cantidad_recibida',
        'cantidad_aceptada',
        'cantidad_rechazada',
        'motivo_rechazo',
    ];

    protected function casts(): array
    {
        return [
            'precio_unitario' => 'decimal:2',
            'descuento' => 'decimal:2',
            'impuesto' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function ordenCompra(): BelongsTo
    {
        return $this->belongsTo(OrdenCompra::class, 'orden_compra_id');
    }

    public function repuesto(): BelongsTo
    {
        return $this->belongsTo(Repuesto::class);
    }
}
