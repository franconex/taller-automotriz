<?php

namespace App\Policies;

use App\Models\EstimacionOrden;
use App\Models\User;

class EstimacionOrdenPolicy
{
    public function view(User $user, EstimacionOrden $estimacion): bool
    {
        $orden = $estimacion->ordenTrabajo;

        if ($user->esCliente()) {
            return $user->cliente_id === $orden->cliente_id;
        }

        if ($user->tieneRol('Mecánico')) {
            return $orden->asignaciones()->where('mecanico_id', $user->empleado?->mecanico?->id)->exists();
        }

        return $user->tienePermiso('ordenes.ver_estimacion');
    }

    public function create(User $user, EstimacionOrden $estimacion): bool
    {
        $orden = $estimacion->ordenTrabajo;
        if ($user->tieneRol('Mecánico')) {
            return $orden->asignaciones()->where('mecanico_id', $user->empleado?->mecanico?->id)->exists();
        }
        return $user->tienePermiso('ordenes.estimar_tiempo');
    }
}
