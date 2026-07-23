<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comprobante extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'pago_id',
        'cliente_id',
        'numero',
        'fecha_emision',
        'nit_ci',
        'razon_social',
        'monto_total',
        'estado',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fecha_emision' => 'datetime',
            'monto_total' => 'decimal:2',
        ];
    }

    public function pago(): BelongsTo
    {
        return $this->belongsTo(Pago::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }
}
