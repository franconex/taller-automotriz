<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class empleado extends Model
{
    public function user()
{
    return $this->belongsTo(User::class);
}

public function sucursal()
{
    return $this->belongsTo(Sucursal::class);
}
}
