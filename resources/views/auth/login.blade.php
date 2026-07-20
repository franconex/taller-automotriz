<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Acceso al Personal - Taller Pro</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&family=montserrat:700,800,900&display=swap" rel="stylesheet" />

    <x-admin.vite-assets :entry="['resources/css/login.css', 'resources/js/app.js']" />
</head>
<body>

<div class="login-wrapper">

    <div class="login-left" style="background-image: url('/img/login.png');">
        <div class="login-left-decoration"></div>
    </div>

    <div class="login-right">
        <div class="login-card">

            <div class="login-card-header">
                <img src="/img/logo.png" alt="Taller Pro" class="login-card-logo">
                <h2 class="login-card-title">Acceso al personal</h2>
                <p class="login-card-subtitle">
                    Ingresa con las credenciales asignadas por el administrador.
                </p>
            </div>

            @if (session('status'))
                <div class="login-session-status">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}">
                @csrf

                <div class="login-field-group">
                    <label for="email" class="login-label">
                        Correo electrónico
                    </label>
                    <div class="login-input-wrapper">
                        <span class="login-icon-left">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="4" width="20" height="16" rx="2"/>
                                <path d="M22 4L12 13L2 4"/>
                            </svg>
                        </span>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="tu@correo.com"
                            class="login-input @error('email') login-input--error @enderror"
                        >
                    </div>
                    @error('email')
                        <p class="login-error">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="15" y1="9" x2="9" y2="15"/>
                                <line x1="9" y1="9" x2="15" y2="15"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="login-field-group">
                    <label for="password" class="login-label">
                        Contraseña
                    </label>
                    <div class="login-input-wrapper">
                        <span class="login-icon-left">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </span>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="login-input login-input--has-toggle @error('password') login-input--error @enderror"
                        >
                        <button
                            type="button"
                            onclick="togglePassword()"
                            class="login-toggle"
                            tabindex="-1"
                        >
                            <svg id="password-show" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg id="password-hide" class="hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                                <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="login-error">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="15" y1="9" x2="9" y2="15"/>
                                <line x1="9" y1="9" x2="15" y2="15"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="login-options">
                    <label class="login-checkbox-wrapper">
                        <input
                            type="checkbox"
                            name="remember"
                            class="login-checkbox"
                        >
                        <span class="login-checkbox-label">
                            Recordar sesión
                        </span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="login-forgot">
                            ¿Olvidaste tu contraseña?
                        </a>
                    @endif
                </div>

                <button type="submit" class="login-submit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                        <polyline points="10 17 15 12 10 7"/>
                        <line x1="15" y1="12" x2="3" y2="12"/>
                    </svg>
                    Ingresar al sistema
                </button>
            </form>

            <div class="login-footer">
                <div class="login-footer-divider">
                    <div class="login-footer-line"></div>
                    <svg class="login-footer-shield" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                    <div class="login-footer-line"></div>
                </div>
                <p class="login-footer-text">
                    Acceso exclusivo para personal autorizado.
                </p>
            </div>

        </div>
    </div>

</div>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    const show = document.getElementById('password-show');
    const hide = document.getElementById('password-hide');
    if (input.type === 'password') {
        input.type = 'text';
        show.classList.add('hidden');
        hide.classList.remove('hidden');
    } else {
        input.type = 'password';
        show.classList.remove('hidden');
        hide.classList.add('hidden');
    }
}
</script>

</body>
</html>