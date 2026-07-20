<?php

namespace App\Services;

use App\Models\Auditoria;
use Illuminate\Support\Facades\Request;

class AuditService
{
    private const SENSITIVE_FIELDS = [
        'password',
        'password_confirmation',
        'remember_token',
        'contrasenia',
    ];

    public function register(
        string $accion,
        string $entidadAfectada,
        ?int $entidadId = null,
        ?array $valoresAnteriores = null,
        ?array $valoresNuevos = null,
        ?string $detalle = null,
    ): Auditoria {
        $valoresAnteriores = $this->sanitize($valoresAnteriores);
        $valoresNuevos = $this->sanitize($valoresNuevos);

        return Auditoria::create([
            'usuario_id' => auth()->id(),
            'accion' => $accion,
            'entidad_afectada' => $entidadAfectada,
            'entidad_id' => $entidadId,
            'valores_anteriores' => $valoresAnteriores,
            'valores_nuevos' => $valoresNuevos,
            'detalle' => $detalle,
            'direccion_ip' => Request::ip(),
            'navegador' => Request::userAgent(),
            'ruta' => Request::path(),
        ]);
    }

    private function sanitize(?array $data): ?array
    {
        if ($data === null) {
            return null;
        }

        return array_filter($data, fn ($key) => ! in_array($key, self::SENSITIVE_FIELDS, true), ARRAY_FILTER_USE_KEY);
    }
}
