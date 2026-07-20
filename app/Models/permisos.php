<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class permisos extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'codigo',
        'modulo',
        'descripcion',
    ];

    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'permiso_rol', 'permiso_id', 'rol_id')
            ->withPivot('created_at');
    }
}
