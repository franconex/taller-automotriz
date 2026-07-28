<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvidenciaTrabajo extends Model
{
    protected $table = 'evidencias_trabajo';

    protected $fillable = [
        'asignacion_trabajo_id',
        'usuario_id',
        'archivo',
        'descripcion',
    ];

    public function asignacionTrabajo(): BelongsTo
    {
        return $this->belongsTo(AsignacionTrabajo::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
