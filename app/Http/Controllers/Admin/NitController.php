<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SiatNitService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NitController extends Controller
{
    public function verificar(Request $request): JsonResponse
    {
        $request->validate(['nit' => 'required|string|digits_between:9,12']);

        $nit = $request->input('nit');

        $service = new SiatNitService();
        $resultado = $service->verificar($nit);

        return response()->json($resultado);
    }
}
