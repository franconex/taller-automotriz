<?php

namespace App\Policies;

use App\Models\Rol;
use App\Models\User;

class RolPolicy
{
    public function viewAny(User $authUser): bool
    {
        return true;
    }

    public function create(User $authUser): bool
    {
        return true;
    }

    public function update(User $authUser, Rol $rol): bool
    {
        return true;
    }

    public function delete(User $authUser, Rol $rol): bool
    {
        if ($rol->users()->count() > 0) {
            return false;
        }

        return true;
    }
}
