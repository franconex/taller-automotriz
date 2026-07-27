<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehiculo extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'cliente_id',
        'modelo_vehiculo_id',
        'marca',
        'modelo',
        'placa',
        'anio',
        'color',
        'numero_chasis',
        'kilometraje_actual',
        'observaciones',
        'foto',
        'estado',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function modelo(): BelongsTo
    {
        return $this->belongsTo(ModeloVehiculo::class, 'modelo_vehiculo_id');
    }

        public function tipoVehiculo(): BelongsTo
    {
        return $this->belongsTo(TipoVehiculo::class);
    }

    public function tipoUso(): BelongsTo
    {
        return $this->belongsTo(TipoUso::class);
    }
public function citas(): HasMany
    {
        return $this->hasMany(Cita::class);
    }

    public function ordenesTrabajo(): HasMany
    {
        return $this->hasMany(OrdenTrabajo::class);
    }
}


