<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proveedor extends Model
{
    use SoftDeletes;

    protected $table = 'proveedores';

    protected $fillable = [
        'nombre_empresa',
        'contacto',
        'telefono',
        'email',
        'direccion',
        'nit',
        'tiempo_entrega_dias',
        'estado',
    ];

    public function repuestos(): HasMany
    {
        return $this->hasMany(Repuesto::class);
    }
}
