<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'empleado_id',
        'sucursal_id',
        'rol_id',
        'nombre',
        'username',
        'email',
        'password',
        'estado',
        'ultimo_acceso',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'ultimo_acceso' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class);
    }

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class);
    }

    public function perfil(): HasOne
    {
        return $this->hasOne(Perfil::class);
    }

    public function isActivo(): bool
    {
        return $this->estado === 'activo';
    }

    public function tieneRol(string $nombreRol): bool
    {
        return $this->rol !== null && $this->rol->nombre === $nombreRol;
    }

    public function tienePermiso(string $codigoPermiso): bool
    {
        if ($this->rol === null) {
            return false;
        }

        return $this->rol->permisos()->where('codigo', $codigoPermiso)->exists();
    }
}
