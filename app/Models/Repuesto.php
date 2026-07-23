<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Repuesto extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'proveedor_id',
        'codigo',
        'nombre',
        'descripcion',
        'costo_compra',
        'precio_venta',
        'stock_minimo',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'costo_compra' => 'decimal:2',
            'precio_venta' => 'decimal:2',
        ];
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function inventarios(): HasMany
    {
        return $this->hasMany(Inventario::class);
    }
}
