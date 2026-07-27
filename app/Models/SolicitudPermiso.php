<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolicitudPermiso extends Model
{
    protected $table = 'solicitudes_permiso';

    protected $fillable = [
        'usuario_solicitante_id',
        'permiso_id',
        'motivo',
        'estado',
        'respuesta_admin',
        'usuario_admin_id',
        'fecha_respuesta',
    ];

    protected function casts(): array
    {
        return [
            'fecha_respuesta' => 'datetime',
        ];
    }

    public function solicitante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_solicitante_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_admin_id');
    }

    public function permiso(): BelongsTo
    {
        return $this->belongsTo(Permiso::class);
    }
}
