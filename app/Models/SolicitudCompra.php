<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SolicitudCompra extends Model
{
    protected $table = 'solicitudes_compra';

    protected $fillable = [
        'numero',
        'sucursal_id',
        'usuario_solicitante_id',
        'usuario_autoriza_id',
        'prioridad',
        'estado',
        'observaciones',
        'fecha_solicitud',
        'fecha_aprobacion',
    ];

    protected function casts(): array
    {
        return [
            'fecha_solicitud' => 'datetime',
            'fecha_aprobacion' => 'datetime',
        ];
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function usuarioSolicitante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_solicitante_id');
    }

    public function usuarioAutoriza(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_autoriza_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleSolicitudCompra::class, 'solicitud_compra_id');
    }

    public function cotizaciones(): HasMany
    {
        return $this->hasMany(Cotizacion::class, 'solicitud_compra_id');
    }

    public function ordenesCompra(): HasMany
    {
        return $this->hasMany(OrdenCompra::class, 'solicitud_compra_id');
    }
}
