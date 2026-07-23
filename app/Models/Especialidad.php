<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Especialidad extends Model
{
    protected $table = 'especialidades';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
    ];

    public function mecanicos(): HasMany
    {
        return $this->hasMany(Mecanico::class);
    }
}
