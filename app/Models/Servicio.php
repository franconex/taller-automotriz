<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Servicio extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tipo_servicio_id',
        'nombre',
        'descripcion',
        'precio_base',
        'duracion_estimada_minutos',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'precio_base' => 'decimal:2',
        ];
    }

    public function tipoServicio(): BelongsTo
    {
        return $this->belongsTo(TipoServicio::class);
    }

    public function subservicios(): HasMany
    {
        return $this->hasMany(Subservicio::class);
    }

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class);
    }
}
