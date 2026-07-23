<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoServicio extends Model
{
    protected $table = 'tipos_servicio';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
    ];

    public function servicios(): HasMany
    {
        return $this->hasMany(Servicio::class);
    }
}
