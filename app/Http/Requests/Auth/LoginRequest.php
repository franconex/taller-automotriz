<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->getCredentials(), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'login' => 'Credenciales incorrectas. Verifica tu correo/usuario y contraseña.',
            ]);
        }

        $user = Auth::user();

        if ($user->sucursal && ! $user->sucursal->estado) {
            Auth::logout();
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'login' => 'Credenciales incorrectas. Verifica tu correo/usuario y contraseña.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    private function getCredentials(): array
    {
        $login = $this->string('login')->toString();
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        return [
            $field => $login,
            'password' => $this->string('password')->toString(),
            'estado' => 'activo',
        ];
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        $minutes = ceil($seconds / 60);
        $message = $minutes > 1
            ? "Demasiados intentos de inicio de sesión. Por favor, inténtalo de nuevo en {$minutes} minutos."
            : "Demasiados intentos de inicio de sesión. Por favor, inténtalo de nuevo en {$seconds} segundos.";

        throw ValidationException::withMessages([
            'login' => $message,
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(
            Str::lower($this->string('login')) . '|' . $this->ip()
        );
    }
}
