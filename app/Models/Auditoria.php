<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Auditoria extends Model
{
    protected $fillable = [
        'usuario_id',
        'accion',
        'modulo',
        'entidad_tipo',
        'entidad_id',
        'datos_anteriores',
        'datos_nuevos',
        'ip_address',
        'user_agent',
        'fecha_accion',
    ];

    protected function casts(): array
    {
        return [
            'datos_anteriores' => 'array',
            'datos_nuevos' => 'array',
            'fecha_accion' => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
