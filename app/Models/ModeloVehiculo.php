<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModeloVehiculo extends Model
{
    protected $table = 'modelos_vehiculos';

    protected $fillable = [
        'marca_vehiculo_id',
        'nombre',
        'anio_lanzamiento',
        'estado',
    ];

    public function marca(): BelongsTo
    {
        return $this->belongsTo(MarcaVehiculo::class, 'marca_vehiculo_id');
    }

    public function vehiculos(): HasMany
    {
        return $this->hasMany(Vehiculo::class);
    }
}
