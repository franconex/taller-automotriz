<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vacacion extends Model
{
    protected $table = 'vacaciones';

    protected $fillable = [
        'usuario_solicitante_id',
        'usuario_admin_id',
        'fecha_inicio',
        'fecha_fin',
        'motivo',
        'estado',
        'respuesta_admin',
        'fecha_respuesta',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
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
}
