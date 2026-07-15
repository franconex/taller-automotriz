<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class sucursal extends Model
{
    public function empleado()
{
    return $this->hasMany(Empleado::class);
}
}
