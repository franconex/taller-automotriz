<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cita extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'cliente_id',
        'vehiculo_id',
        'sucursal_id',
        'usuario_id',
        'fecha',
        'hora',
        'tipo',
        'descripcion_problema',
        'estado',
        'deja_vehiculo',
        'costo_consulta',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'hora' => 'string',
            'deja_vehiculo' => 'boolean',
            'costo_consulta' => 'decimal:2',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ordenTrabajo()
    {
        return $this->hasOne(OrdenTrabajo::class);
    }
}
