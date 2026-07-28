<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrdenServicio extends Model
{
    protected $table = 'orden_servicios';

    protected $fillable = [
        'orden_trabajo_id',
        'servicio_id',
        'subservicio_id',
        'nombre_servicio',
        'nombre_subservicio',
        'precio_base',
        'tiempo_estimado_minutos',
        'observacion',
    ];

    protected function casts(): array
    {
        return [
            'precio_base' => 'decimal:2',
        ];
    }

    public function ordenTrabajo(): BelongsTo
    {
        return $this->belongsTo(OrdenTrabajo::class);
    }

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class);
    }

    public function subservicio(): BelongsTo
    {
        return $this->belongsTo(Subservicio::class);
    }
}
