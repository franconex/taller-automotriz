<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleCotizacion extends Model
{
    protected $table = 'detalles_cotizacion';

    protected $fillable = [
        'cotizacion_id',
        'repuesto_id',
        'cantidad_solicitada',
        'cantidad_disponible',
        'marca_ofrecida',
        'precio_unitario',
        'descuento',
        'impuesto',
        'costo_envio',
        'subtotal',
        'tiempo_entrega_dias',
        'garantia_dias',
    ];

    protected function casts(): array
    {
        return [
            'precio_unitario' => 'decimal:2',
            'descuento' => 'decimal:2',
            'impuesto' => 'decimal:2',
            'costo_envio' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function cotizacion(): BelongsTo
    {
        return $this->belongsTo(Cotizacion::class, 'cotizacion_id');
    }

    public function repuesto(): BelongsTo
    {
        return $this->belongsTo(Repuesto::class);
    }
}
