<?php

namespace App\Policies;

use App\Models\User;

class SubservicioPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->tienePermiso('subservicios.ver');
    }

    public function create(User $user): bool
    {
        return $user->tienePermiso('subservicios.crear');
    }

    public function update(User $user): bool
    {
        return $user->tienePermiso('subservicios.editar');
    }
}
