<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subservicio extends Model
{
    protected $fillable = [
        'servicio_id',
        'nombre',
        'descripcion',
        'precio_base',
        'duracion_estimada_minutos',
        'requiere_diagnostico',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'precio_base' => 'decimal:2',
            'requiere_diagnostico' => 'boolean',
            'estado' => 'boolean',
        ];
    }

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class);
    }

    public function repuestos(): BelongsToMany
    {
        return $this->belongsToMany(Repuesto::class, 'subservicio_repuesto')
            ->withPivot('cantidad_sugerida')
            ->withTimestamps();
    }
}
