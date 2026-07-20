<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'rol_id',
        'nombre',
        'name',
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
            'estado' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function getNameAttribute(): ?string
    {
        return $this->nombre;
    }

    public function setNameAttribute(string $value): void
    {
        $this->attributes['nombre'] = $value;
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class);
    }

    public function empleado(): HasOne
    {
        return $this->hasOne(Empleado::class);
    }
}
