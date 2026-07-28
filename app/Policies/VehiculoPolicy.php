<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehiculo;

class VehiculoPolicy
{
    public function view(User $user, Vehiculo $vehiculo): bool
    {
        if ($user->esCliente()) {
            return $user->cliente_id === $vehiculo->cliente_id;
        }
        return $user->tienePermiso('vehiculos.ver');
    }
}
