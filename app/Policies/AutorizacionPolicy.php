<?php

namespace App\Policies;

use App\Models\Autorizacion;
use App\Models\User;

class AutorizacionPolicy
{
    private function clienteId(Autorizacion $a): ?int
    {
        return $a->ordenTrabajo?->cliente_id ?? $a->cita?->cliente_id;
    }

    public function view(User $user, Autorizacion $autorizacion): bool
    {
        if ($user->esCliente()) {
            return $user->cliente_id === $this->clienteId($autorizacion);
        }
        return $user->tienePermiso('ordenes.ver');
    }

    public function respond(User $user, Autorizacion $autorizacion): bool
    {
        if (! $autorizacion->esRespondible()) {
            return false;
        }
        if ($user->esCliente()) {
            return $user->cliente_id === $this->clienteId($autorizacion);
        }
        return false;
    }

    public function create(User $user): bool
    {
        return $user->tienePermiso('ordenes.actualizar_estado');
    }
}
