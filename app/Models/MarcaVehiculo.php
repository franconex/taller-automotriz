<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarcaVehiculo extends Model
{
    protected $table = 'marcas_vehiculos';

    protected $fillable = [
        'nombre',
        'pais_origen',
        'estado',
    ];

    public function modelos(): HasMany
    {
        return $this->hasMany(ModeloVehiculo::class);
    }
}
