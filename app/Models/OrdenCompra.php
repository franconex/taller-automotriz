<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrdenCompra extends Model
{
    protected $table = 'ordenes_compra';

    protected $fillable = [
        'numero',
        'solicitud_compra_id',
        'cotizacion_id',
        'proveedor_id',
        'sucursal_id',
        'usuario_solicitante_id',
        'usuario_aprobador_id',
        'fecha_emision',
        'fecha_entrega_estimada',
        'forma_pago',
        'subtotal',
        'costo_envio',
        'impuesto',
        'descuento',
        'total',
        'estado',
        'enviada_medio',
        'enviada_fecha',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fecha_emision' => 'datetime',
            'fecha_entrega_estimada' => 'date',
            'enviada_fecha' => 'datetime',
            'subtotal' => 'decimal:2',
            'costo_envio' => 'decimal:2',
            'impuesto' => 'decimal:2',
            'descuento' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function solicitudCompra(): BelongsTo
    {
        return $this->belongsTo(SolicitudCompra::class, 'solicitud_compra_id');
    }

    public function cotizacion(): BelongsTo
    {
        return $this->belongsTo(Cotizacion::class, 'cotizacion_id');
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function usuarioSolicitante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_solicitante_id');
    }

    public function usuarioAprobador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_aprobador_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleOrdenCompra::class, 'orden_compra_id');
    }
}
