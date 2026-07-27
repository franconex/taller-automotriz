<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrdenTrabajoFoto extends Model
{
    protected $table = 'orden_trabajo_fotos';

    protected $fillable = [
        'orden_trabajo_id',
        'usuario_id',
        'ruta',
        'nombre_original',
        'tipo',
        'descripcion',
    ];

    public function ordenTrabajo(): BelongsTo
    {
        return $this->belongsTo(OrdenTrabajo::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
