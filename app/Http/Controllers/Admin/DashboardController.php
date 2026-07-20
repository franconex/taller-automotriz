<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\empleado;
use App\Models\sucursal;
use App\Models\Rol;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'totalUsuarios' => User::count(),
            'totalEmpleados' => empleado::count(),
            'totalSucursales' => sucursal::count(),
            'totalRoles' => Rol::count(),
        ]);
    }
}
