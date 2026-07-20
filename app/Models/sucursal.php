<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class sucursal extends Model
{
    protected $table = 'sucursales';

    public function empleado()
{
    return $this->hasMany(Empleado::class);
}
}
