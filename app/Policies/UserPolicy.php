<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $authUser): bool
    {
        return true;
    }

    public function view(User $authUser, User $user): bool
    {
        return true;
    }

    public function create(User $authUser): bool
    {
        return true;
    }

    public function update(User $authUser, User $user): bool
    {
        return true;
    }

    public function delete(User $authUser, User $user): bool
    {
        if ($user->id === $authUser->id) {
            return false;
        }

        if ($this->isLastAdmin($user)) {
            return false;
        }

        return $user->empleado()->exists() === false;
    }

    public function desactivar(User $authUser, User $user): bool
    {
        if ($user->id === $authUser->id) {
            return false;
        }

        if ($this->isLastAdmin($user)) {
            return false;
        }

        return true;
    }

    private function isLastAdmin(User $user): bool
    {
        if ($user->rol->nombre !== 'Administrador') {
            return false;
        }

        return User::whereHas('rol', fn ($q) => $q->where('nombre', 'Administrador'))
            ->where('estado', true)
            ->count() <= 1;
    }
}
