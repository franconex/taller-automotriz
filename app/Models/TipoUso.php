<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoUso extends Model
{
    protected $table = 'tipos_uso';

    protected $fillable = ['nombre', 'estado'];

    public function vehiculos(): HasMany
    {
        return $this->hasMany(Vehiculo::class);
    }
}
