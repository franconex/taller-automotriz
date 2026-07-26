<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cotizacion extends Model
{
    protected $table = 'cotizaciones';

    protected $fillable = [
        'solicitud_compra_id',
        'proveedor_id',
        'usuario_id',
        'medio_contacto',
        'nombre_contacto',
        'fecha_cotizacion',
        'fecha_vencimiento',
        'estado',
        'motivo_seleccion',
        'motivo_seleccion_otro',
        'observaciones',
        'archivo',
    ];

    protected function casts(): array
    {
        return [
            'fecha_cotizacion' => 'datetime',
            'fecha_vencimiento' => 'date',
        ];
    }

    public function solicitudCompra(): BelongsTo
    {
        return $this->belongsTo(SolicitudCompra::class, 'solicitud_compra_id');
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleCotizacion::class, 'cotizacion_id');
    }
}
