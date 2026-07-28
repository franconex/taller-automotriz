<?php

namespace App\Policies;

use App\Models\Comprobante;
use App\Models\User;

class ComprobantePolicy
{
    public function view(User $user, Comprobante $comprobante): bool
    {
        if ($user->esCliente()) {
            return $user->cliente_id === $comprobante->cliente_id;
        }
        return $user->tienePermiso('comprobantes.ver');
    }
}
