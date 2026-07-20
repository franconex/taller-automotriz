<?php

namespace App\Policies;

use App\Models\sucursal;
use App\Models\User;

class SucursalPolicy
{
    public function viewAny(User $authUser): bool
    {
        return true;
    }

    public function create(User $authUser): bool
    {
        return true;
    }

    public function update(User $authUser, sucursal $sucursal): bool
    {
        return true;
    }

    public function delete(User $authUser, sucursal $sucursal): bool
    {
        if ($sucursal->empleados()->count() > 0) {
            return false;
        }

        return true;
    }

    public function desactivar(User $authUser, sucursal $sucursal): bool
    {
        return true;
    }
}
