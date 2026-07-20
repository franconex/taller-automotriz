<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    protected $fillable = [
        'usuario_id',
        'accion',
        'entidad_afectada',
        'entidad_id',
        'valores_anteriores',
        'valores_nuevos',
        'detalle',
        'direccion_ip',
        'navegador',
        'ruta',
    ];

    protected $casts = [
        'valores_anteriores' => 'array',
        'valores_nuevos' => 'array',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
