<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empleado extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sucursal_id',
        'nombre_completo',
        'ci',
        'telefono',
        'email',
        'direccion',
        'cargo',
        'fecha_contratacion',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha_contratacion' => 'date',
        ];
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function mecanico(): HasOne
    {
        return $this->hasOne(Mecanico::class);
    }
}
