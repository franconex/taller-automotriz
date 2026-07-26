<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Repuesto extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'codigo',
        'codigo_barras',
        'codigo_fabricante',
        'tipo',
        'nombre',
        'categoria',
        'marca',
        'descripcion',
        'costo_compra',
        'precio_venta',
        'stock_minimo',
        'stock_maximo',
        'proveedor_id',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'costo_compra' => 'decimal:2',
            'precio_venta' => 'decimal:2',
            'stock_minimo' => 'integer',
            'stock_maximo' => 'integer',
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

    public function transferenciasOrigen(): HasMany
    {
        return $this->hasMany(DetalleTransferencia::class, 'repuesto_id');
    }
}
