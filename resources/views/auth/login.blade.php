<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar sesión — Taller Pro</title>
    @vite(['resources/css/app.css', 'resources/css/login.css', 'resources/js/app.js'])
</head>
<body>

<div class="container-fluid min-vh-100 p-0 overflow-hidden position-relative">
    <div class="row g-0 min-vh-100">

        {{-- LEFT PANEL --}}
        <div class="col-lg-7 login-left-panel position-relative d-flex flex-column min-vh-100"
             style="background-image: url('{{ asset('img/servicio-mantenimiento-preventivo.png') }}');">

            {{-- Overlay --}}
            <div class="login-overlay position-absolute top-0 start-0 w-100 h-100"></div>

            {{-- Content --}}
            <div class="position-relative z-1 d-flex flex-column h-100 px-4 px-lg-5 py-4 py-lg-5">

                {{-- Logo --}}
                <div class="d-flex align-items-center gap-3 pt-2 pt-lg-3 mb-auto">
                    <img src="{{ asset('img/logo-modo-oscuro.png') }}"
                         alt="Taller Pro"
                         class="login-brand-icon flex-shrink-0">
                </div>

                {{-- Main content --}}
                <div class="flex-grow-1 d-flex flex-column justify-content-center my-4" style="max-width: 90%;">
                    <h1 class="text-white fw-bold lh-sm mb-3 login-main-title">
                        Gestión eficiente<br>
                        para un servicio<br>
                        automotriz <span class="text-danger">de calidad</span>
                    </h1>
                    <div class="login-red-line mb-4"></div>
                    <p class="text-white-50 lh-lg login-main-text">
                        Administra clientes, vehículos, citas<br>
                        y órdenes de trabajo desde un solo<br>
                        lugar.
                    </p>
                </div>

                {{-- Benefits --}}
                <div class="d-flex align-items-center justify-content-between pt-4 w-100 login-benefits">
                    <div class="benefit-item flex-fill d-flex flex-column align-items-center text-center px-2 py-2 position-relative">
                        <i class="bi bi-clipboard-check login-benefit-icon" aria-hidden="true"></i>
                        <span class="benefit-label">Control de órdenes</span>
                    </div>
                    <div class="benefit-item flex-fill d-flex flex-column align-items-center text-center px-2 py-2 position-relative">
                        <i class="bi bi-calendar-check login-benefit-icon" aria-hidden="true"></i>
                        <span class="benefit-label">Gestión de citas</span>
                    </div>
                    <div class="benefit-item flex-fill d-flex flex-column align-items-center text-center px-2 py-2 position-relative">
                        <i class="bi bi-car-front login-benefit-icon" aria-hidden="true"></i>
                        <span class="benefit-label">Seguimiento de vehículos</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- DIAGONAL DIVIDER --}}
        <div class="diagonal-divider d-none d-lg-block position-absolute top-0 h-100" aria-hidden="true"></div>

        {{-- RIGHT PANEL --}}
        <main class="col-lg-5 login-form-panel">
            <div class="login-form-content">

                {{-- Header --}}
                <div class="text-center login-form-header">
                    <img src="{{ asset('img/logo.png') }}" alt="Taller Pro" class="login-form-logo">
                    <h2 class="login-form-title">Acceso al personal</h2>
                    <p class="login-form-subtitle">
                        Ingresa con las credenciales asignadas<br>por el administrador
                    </p>
                </div>

                {{-- Errors --}}
                @if ($errors->any())
                    <div class="d-flex align-items-center gap-2 px-3 py-2 mb-3 rounded-3 login-error"
                         role="alert">
                        <i class="bi bi-exclamation-circle flex-shrink-0" aria-hidden="true"></i>
                        <span>{{ $errors->first('login') }}</span>
                    </div>
                @endif

                {{-- Form --}}
                <form method="POST" action="{{ route('login') }}" novalidate>
                    @csrf

                    {{-- Login field --}}
                    <div class="mb-3">
                        <label for="login" class="visually-hidden">Correo o nombre de usuario</label>
                        <div class="input-group login-input-group">
                            <span class="input-group-text login-input-icon">
                                <i class="bi bi-envelope" aria-hidden="true"></i>
                            </span>
                            <input id="login" type="text" name="login" value="{{ old('login') }}"
                                   class="form-control login-input @error('login') is-invalid @enderror"
                                   placeholder="Correo o nombre de usuario"
                                   autocomplete="username" required autofocus>
                        </div>
                        @error('login')
                            <div class="small mt-1 login-field-error" role="alert">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password field --}}
                    <div class="mb-3">
                        <label for="password" class="visually-hidden">Contraseña</label>
                        <div class="input-group login-input-group">
                            <span class="input-group-text login-input-icon">
                                <i class="bi bi-lock" aria-hidden="true"></i>
                            </span>
                            <input id="password" type="password" name="password"
                                   class="form-control login-input @error('password') is-invalid @enderror"
                                   placeholder="Contraseña"
                                   autocomplete="current-password" required>
                            <button type="button"
                                    class="input-group-text password-toggle-btn"
                                    id="togglePassword"
                                    aria-label="Mostrar contraseña"
                                    tabindex="-1">
                                <i class="bi bi-eye-slash" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="small mt-1 login-field-error" role="alert">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Options --}}
                    <div class="login-form-options d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <input type="checkbox" name="remember" id="remember" value="1"
                                   class="form-check-input m-0 login-remember-check">
                            <label for="remember" class="small login-remember-label">Recordar sesión</label>
                        </div>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                               class="small text-decoration-none fw-medium login-forgot-link">¿Olvidaste tu contraseña?</a>
                        @endif
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                            class="btn login-submit w-100 d-flex align-items-center justify-content-center gap-2 border-0 fw-semibold text-white"
                            id="loginButton">
                        <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i>
                        Ingresar al sistema
                    </button>
                </form>

                {{-- Footer --}}
                <div class="login-form-footer">
                    <div class="d-flex align-items-center gap-3">
                        <span class="flex-grow-1 login-footer-line"></span>
                        <i class="bi bi-shield-check flex-shrink-0 login-footer-shield" aria-hidden="true"></i>
                        <span class="flex-grow-1 login-footer-line"></span>
                    </div>
                    <p class="text-center small mb-0 login-footer-text">
                        Acceso exclusivo para personal autorizado
                    </p>
                </div>

            </div>
        </main>

    </div>
</div>

<script>
    (function() {
        var toggle = document.getElementById('togglePassword');
        var password = document.getElementById('password');
        var icon = document.getElementById('togglePasswordIcon');
        if (toggle && password && icon) {
            toggle.addEventListener('click', function() {
                var isPassword = password.type === 'password';
                password.type = isPassword ? 'text' : 'password';
                icon.className = isPassword ? 'bi bi-eye' : 'bi bi-eye-slash';
                toggle.setAttribute('aria-label', isPassword ? 'Ocultar contraseña' : 'Mostrar contraseña');
            });
        }
        var form = document.querySelector('form');
        var btn = document.getElementById('loginButton');
        if (form && btn) {
            form.addEventListener('submit', function() {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Ingresando...';
            });
        }
    })();
</script>

</body>
</html>
