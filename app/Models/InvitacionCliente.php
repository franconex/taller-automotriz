<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvitacionCliente extends Model
{
    protected $table = 'invitaciones_clientes';

    protected $fillable = [
        'cliente_id',
        'email',
        'token_hash',
        'expires_at',
        'used_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function esValida(): bool
    {
        return $this->used_at === null && $this->expires_at->isFuture();
    }
}
