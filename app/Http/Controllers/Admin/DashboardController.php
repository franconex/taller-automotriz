<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auditoria;
use App\Models\Cliente;
use App\Models\empleado;
use App\Models\Rol;
use App\Models\sucursal;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $usuariosActivos = User::where('estado', true)->count();
        $usuariosInactivos = User::where('estado', false)->count();
        $totalEmpleados = empleado::count();
        $sucursalesActivas = sucursal::where('estado', true)->count();
        $totalRoles = Rol::count();
        $totalClientes = Cliente::count();

        $ultimosAccesos = User::whereNotNull('ultimo_acceso')
            ->with('rol:id,nombre')
            ->orderByDesc('ultimo_acceso')
            ->take(5)
            ->get(['id', 'nombre', 'email', 'rol_id', 'ultimo_acceso']);

        $actividadReciente = Auditoria::with('usuario:id,nombre,email')
            ->orderByDesc('id')
            ->take(8)
            ->get();

        $alertas = collect();

        if ($usuariosInactivos > 0) {
            $alertas->push("{$usuariosInactivos} usuario(s) inactivo(s) sin acceso al sistema.");
        }

        $rolesSinPermisos = Rol::doesntHave('permisos')->where('estado', true)->count();
        if ($rolesSinPermisos > 0) {
            $alertas->push("{$rolesSinPermisos} rol(es) activo(s) sin permisos asignados.");
        }

        $empleadosSinSucursal = empleado::whereNull('sucursal_id')->count();
        if ($empleadosSinSucursal > 0) {
            $alertas->push("{$empleadosSinSucursal} empleado(s) sin sucursal asignada.");
        }

        $ultimosRegistros = [
            'usuarios' => User::latest()->take(3)->get(['id', 'nombre', 'email']),
            'clientes' => Cliente::latest()->take(3)->get(['id', 'nombre', 'apellido']),
        ];

        return view('admin.dashboard', compact(
            'usuariosActivos',
            'usuariosInactivos',
            'totalEmpleados',
            'sucursalesActivas',
            'totalRoles',
            'totalClientes',
            'ultimosAccesos',
            'actividadReciente',
            'alertas',
            'ultimosRegistros',
        ));
    }
}
