<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nombre_completo',
        'ci',
        'telefono',
        'email',
        'direccion',
        'fecha_registro',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha_registro' => 'datetime',
        ];
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'cliente_id');
    }

    public function vehiculos(): HasMany
    {
        return $this->hasMany(Vehiculo::class);
    }

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class);
    }

    public function ordenesTrabajo(): HasMany
    {
        return $this->hasMany(OrdenTrabajo::class);
    }

    public function comprobantes(): HasMany
    {
        return $this->hasMany(Comprobante::class);
    }
}
