<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\ClienteAccountService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function __construct(
        private ClienteAccountService $accountService
    ) {}

    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = $this->accountService->registrarManual($request->validated());

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->intended(route('cliente.dashboard'))
            ->with('success', 'Cuenta creada correctamente. Bienvenido.');
    }
}
