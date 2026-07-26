<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventario extends Model
{
    protected $fillable = [
        'sucursal_id',
        'repuesto_id',
        'cantidad_actual',
        'cantidad_reservada',
        'stock_minimo',
        'stock_maximo',
        'costo_promedio',
        'fecha_actualizacion',
    ];

    protected function casts(): array
    {
        return [
            'fecha_actualizacion' => 'datetime',
            'costo_promedio' => 'decimal:2',
            'stock_minimo' => 'integer',
            'stock_maximo' => 'integer',
        ];
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function repuesto(): BelongsTo
    {
        return $this->belongsTo(Repuesto::class);
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class);
    }
}
