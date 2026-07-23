<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mecanico extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'empleado_id',
        'especialidad_id',
        'disponibilidad',
        'observaciones',
    ];

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class);
    }

    public function especialidad(): BelongsTo
    {
        return $this->belongsTo(Especialidad::class);
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(AsignacionTrabajo::class);
    }
}
