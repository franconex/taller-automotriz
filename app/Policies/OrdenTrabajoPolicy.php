<?php

namespace App\Policies;

use App\Models\OrdenTrabajo;
use App\Models\User;

class OrdenTrabajoPolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->esCliente()) {
            return $user->cliente !== null;
        }
        if ($user->tieneRol('Mecánico')) {
            return $user->empleado?->mecanico !== null;
        }
        return $user->tienePermiso('ordenes.ver');
    }

    public function view(User $user, OrdenTrabajo $ordenTrabajo): bool
    {
        if ($user->esCliente()) {
            return $user->cliente_id === $ordenTrabajo->cliente_id;
        }

        if ($user->tieneRol('Administrador') || $user->tieneRol('Gerente')) {
            return $user->tienePermiso('ordenes.ver');
        }

        if ($user->tieneRol('Mecánico')) {
            if (! $user->empleado?->mecanico) {
                return false;
            }
            return $ordenTrabajo->asignaciones()
                ->where('mecanico_id', $user->empleado->mecanico->id)
                ->exists();
        }

        return false;
    }

    public function work(User $user, OrdenTrabajo $ordenTrabajo): bool
    {
        if ($user->tieneRol('Administrador') || $user->tieneRol('Gerente')) {
            return $user->tienePermiso('ordenes.actualizar_estado');
        }

        if ($user->tieneRol('Mecánico')) {
            if (! $user->empleado?->mecanico) {
                return false;
            }
            return $ordenTrabajo->asignaciones()
                ->where('mecanico_id', $user->empleado->mecanico->id)
                ->whereNull('fecha_finalizacion')
                ->exists();
        }

        return false;
    }

    public function createNote(User $user, OrdenTrabajo $ordenTrabajo): bool
    {
        return $this->work($user, $ordenTrabajo);
    }

    public function uploadEvidence(User $user, OrdenTrabajo $ordenTrabajo): bool
    {
        return $this->work($user, $ordenTrabajo);
    }
}
