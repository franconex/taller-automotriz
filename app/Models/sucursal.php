<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class sucursal extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'sucursales';

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'email',
        'horario_atencion',
        'latitud',
        'longitud',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
    ];

    public function empleados()
    {
        return $this->hasMany(empleado::class, 'sucursal_id');
    }
}
