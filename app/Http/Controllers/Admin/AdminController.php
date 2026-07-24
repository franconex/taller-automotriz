<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    protected function usuarioSucursalId(): ?int
    {
        return Auth::user()?->sucursal_id;
    }

    protected function scopeSucursal(Builder $query, string $column = 'sucursal_id'): Builder
    {
        $sucursalId = $this->usuarioSucursalId();

        if ($sucursalId === null) {
            return $query;
        }

        return $query->where($column, $sucursalId);
    }

    protected function aplicarFiltros(Request $request, Builder $query, array $campos): Builder
    {
        foreach ($campos as $campo) {
            $valor = $request->input($campo);

            if ($valor !== null && $valor !== '') {
                $query->where($campo, $valor);
            }
        }

        return $query;
    }

    protected function aplicarBusqueda(Builder $query, Request $request, array $campos): Builder
    {
        $termino = trim((string) $request->input('q', ''));

        if ($termino === '') {
            return $query;
        }

        $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $termino) . '%';

        return $query->where(function (Builder $q) use ($campos, $like) {
            foreach ($campos as $campo) {
                if (Str::contains($campo, '.')) {
                    [$relacion, $columna] = explode('.', $campo, 2);
                    $q->orWhereHas($relacion, function (Builder $sub) use ($columna, $like) {
                        $sub->where($columna, 'like', $like);
                    });
                } else {
                    $q->orWhere($campo, 'like', $like);
                }
            }
        });
    }

    protected function redirigirConExito(string $modulo, string $accion = 'guardado'): RedirectResponse
    {
        return back()->with('success', "El registro de {$modulo} fue {$accion} correctamente.");
    }

    protected function redirigirConError(string $mensaje): RedirectResponse
    {
        return back()->with('error', $mensaje)->withInput();
    }

    protected function cargarRelaciones(Request $request, array $relaciones): array
    {
        $datos = [];

        foreach ($relaciones as $llave => $config) {
            $datos[$llave] = $config['modelo']::query()
                ->when(
                    $config['sucursal'] ?? false,
                    fn (Builder $q) => $this->scopeSucursal($q, $config['columna_sucursal'] ?? 'sucursal_id')
                )
                ->orderBy($config['orden'] ?? 'nombre')
                ->get();
        }

        return $datos;
    }

    protected function cambiarEstado(Request $request, Model $modelo, string $modulo): RedirectResponse
    {
        $nuevoEstado = ! $modelo->{$this->columnaEstado($modelo)};
        $modelo->{$this->columnaEstado($modelo)} = $nuevoEstado;
        $modelo->save();

        $texto = $nuevoEstado ? 'activado' : 'desactivado';

        return back()->with('success', "El registro de {$modulo} fue {$texto} correctamente.");
    }

    protected function columnaEstado(Model $modelo): string
    {
        foreach (['estado', 'activo'] as $candidato) {
            if (array_key_exists($candidato, $modelo->getAttributes())) {
                return $candidato;
            }
        }

        return 'estado';
    }
}
