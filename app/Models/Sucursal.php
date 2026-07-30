<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sucursal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sucursales';

    protected $fillable = [
        'nombre',
        'direccion',
        'latitud',
        'longitud',
        'telefono',
        'horario_atencion',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function getHorarioAtencionAttribute($value): array|string|null
    {
        if (is_null($value)) {
            return null;
        }
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : $value;
    }

    public function setHorarioAtencionAttribute($value): void
    {
        $this->attributes['horario_atencion'] = is_array($value) ? json_encode($value) : $value;
    }

    public function empleados(): HasMany
    {
        return $this->hasMany(Empleado::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function inventarios(): HasMany
    {
        return $this->hasMany(Inventario::class);
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
