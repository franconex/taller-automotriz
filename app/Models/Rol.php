<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;

    protected $table = 'roles';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'rol_id');
    }

    public function permisos()
    {
        return $this->belongsToMany(permisos::class, 'permiso_rol', 'rol_id', 'permiso_id')
            ->withPivot('created_at');
    }
}
