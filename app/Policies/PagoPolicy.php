<?php

namespace App\Policies;

use App\Models\Pago;
use App\Models\User;

class PagoPolicy
{
    public function view(User $user, Pago $pago): bool
    {
        if ($user->esCliente()) {
            return $user->cliente_id === $pago->ordenTrabajo?->cliente_id;
        }
        return $user->tienePermiso('pagos.ver');
    }
}
