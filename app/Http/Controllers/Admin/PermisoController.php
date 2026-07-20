<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\permisos;

class PermisoController extends Controller
{
    public function index()
    {
        $permisos = permisos::withCount('roles')
            ->orderBy('modulo')
            ->orderBy('nombre')
            ->paginate(15);

        return view('admin.permisos.index', compact('permisos'));
    }
}
