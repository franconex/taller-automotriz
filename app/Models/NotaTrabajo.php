<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotaTrabajo extends Model
{
    protected $table = 'notas_trabajo';

    protected $fillable = [
        'asignacion_trabajo_id',
        'usuario_id',
        'contenido',
        'visible_cliente',
    ];

    protected function casts(): array
    {
        return [
            'visible_cliente' => 'boolean',
        ];
    }

    public function asignacionTrabajo(): BelongsTo
    {
        return $this->belongsTo(AsignacionTrabajo::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
