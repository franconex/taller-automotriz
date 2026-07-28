<?php

namespace App\Policies;

use App\Models\Autorizacion;
use App\Models\User;

class AutorizacionPolicy
{
    public function view(User $user, Autorizacion $autorizacion): bool
    {
        if ($user->esCliente()) {
            return $user->cliente_id === $autorizacion->ordenTrabajo?->cliente_id;
        }
        return $user->tienePermiso('ordenes.ver');
    }

    public function respond(User $user, Autorizacion $autorizacion): bool
    {
        if (! $autorizacion->esRespondible()) {
            return false;
        }
        if ($user->esCliente()) {
            return $user->cliente_id === $autorizacion->ordenTrabajo?->cliente_id;
        }
        return false;
    }

    public function create(User $user): bool
    {
        return $user->tienePermiso('ordenes.actualizar_estado');
    }
}
