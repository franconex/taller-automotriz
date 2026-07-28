<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AsignacionTrabajo extends Model
{
    protected $table = 'asignaciones_trabajo';

    protected $fillable = [
        'orden_trabajo_id',
        'mecanico_id',
        'usuario_asignador_id',
        'actividad_asignada',
        'prioridad',
        'estado',
        'fecha_asignacion',
        'fecha_inicio',
        'fecha_finalizacion',
        'observaciones',
        'porcentaje_avance',
        'proximo_paso',
        'diagnostico_mecanico',
    ];

    protected function casts(): array
    {
        return [
            'fecha_asignacion' => 'datetime',
            'fecha_inicio' => 'datetime',
            'fecha_finalizacion' => 'datetime',
            'porcentaje_avance' => 'integer',
        ];
    }

    public function ordenTrabajo(): BelongsTo
    {
        return $this->belongsTo(OrdenTrabajo::class);
    }

    public function mecanico(): BelongsTo
    {
        return $this->belongsTo(Mecanico::class);
    }

    public function usuarioAsignador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_asignador_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleOrdenTrabajo::class);
    }

    public function notas(): HasMany
    {
        return $this->hasMany(NotaTrabajo::class);
    }

    public function evidencias(): HasMany
    {
        return $this->hasMany(EvidenciaTrabajo::class);
    }

    public function notasVisiblesCliente(): HasMany
    {
        return $this->hasMany(NotaTrabajo::class)->where('visible_cliente', true);
    }
}
