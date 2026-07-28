<?php

namespace App\Policies;

use App\Models\Cita;
use App\Models\User;

class CitaPolicy
{
    public function view(User $user, Cita $cita): bool
    {
        if ($user->esCliente()) {
            return $user->cliente_id === $cita->cliente_id;
        }
        return $user->tienePermiso('citas.ver');
    }
}
