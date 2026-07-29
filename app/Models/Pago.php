<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pago extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'orden_trabajo_id',
        'metodo_pago_id',
        'usuario_id',
        'fecha_pago',
        'monto',
        'numero_comprobante',
        'referencia',
        'estado',
        'observaciones',
        'stripe_payment_intent_id',
        'confirmado_por_id',
        'confirmado_en',
        'comprobante_imagen',
        'motivo_rechazo',
    ];

    protected function casts(): array
    {
        return [
            'fecha_pago' => 'datetime',
            'monto' => 'decimal:2',
        ];
    }

    public function ordenTrabajo(): BelongsTo
    {
        return $this->belongsTo(OrdenTrabajo::class);
    }

    public function metodoPago(): BelongsTo
    {
        return $this->belongsTo(MetodoPago::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function comprobante(): HasOne
    {
        return $this->hasOne(Comprobante::class);
    }
}

