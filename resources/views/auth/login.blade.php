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

            <div class="login-overlay position-absolute top-0 start-0 w-100 h-100"></div>

            <div class="position-relative z-1 d-flex flex-column h-100 px-4 px-lg-5 py-4 py-lg-5">

                <div class="d-flex align-items-center gap-3 pt-2 pt-lg-3 mb-auto">
                    <img src="{{ asset('img/logo-modo-oscuro.png') }}"
                         alt="Taller Pro"
                         class="login-brand-icon flex-shrink-0">
                </div>

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

                <div class="text-center login-form-header">
                    <img src="{{ asset('img/logo.png') }}" alt="Taller Pro" class="login-form-logo">
                </div>

                {{-- TABS --}}
                <div class="d-flex mb-4" role="tablist">
                    <button class="flex-fill text-center py-2 fw-semibold login-tab active"
                            id="tab-login" role="tab" aria-selected="true"
                            onclick="switchTab('login')">Iniciar sesión</button>
                    <button class="flex-fill text-center py-2 fw-semibold login-tab"
                            id="tab-register" role="tab" aria-selected="false"
                            onclick="switchTab('register')">Registrarme</button>
                </div>

                {{-- ERRORS --}}
                @if ($errors->any())
                    <div class="d-flex align-items-center gap-2 px-3 py-2 mb-3 rounded-3 login-error" role="alert">
                        <i class="bi bi-exclamation-circle flex-shrink-0" aria-hidden="true"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                {{-- LOGIN FORM --}}
                <div id="form-login">
                    <form method="POST" action="{{ route('login') }}" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="login" class="visually-hidden">Correo o nombre de usuario</label>
                            <div class="input-group login-input-group">
                                <span class="input-group-text login-input-icon">
                                    <i class="bi bi-envelope" aria-hidden="true"></i>
                                </span>
                                <input id="login" type="text" name="login" value="{{ old('login') }}"
                                       class="form-control login-input"
                                       placeholder="Correo o nombre de usuario"
                                       autocomplete="username" autofocus>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="visually-hidden">Contraseña</label>
                            <div class="input-group login-input-group">
                                <span class="input-group-text login-input-icon">
                                    <i class="bi bi-lock" aria-hidden="true"></i>
                                </span>
                                <input id="password" type="password" name="password"
                                       class="form-control login-input"
                                       placeholder="Contraseña"
                                       autocomplete="current-password">
                                <button type="button"
                                        class="input-group-text password-toggle-btn"
                                        id="togglePassword"
                                        aria-label="Mostrar contraseña"
                                        tabindex="-1">
                                    <i class="bi bi-eye-slash" id="togglePasswordIcon"></i>
                                </button>
                            </div>
                        </div>

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

                        <button type="submit"
                                class="btn login-submit w-100 d-flex align-items-center justify-content-center gap-2 border-0 fw-semibold text-white"
                                id="loginButton">
                            <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i>
                            Ingresar
                        </button>
                    </form>

                    <div class="d-flex align-items-center gap-3 my-4">
                        <span class="flex-grow-1" style="height:1px;background:#E5E7EB;"></span>
                        <span class="small" style="color:var(--tp-text-secondary);">o continuar con</span>
                        <span class="flex-grow-1" style="height:1px;background:#E5E7EB;"></span>
                    </div>

                    <a href="{{ route('auth.google.redirect') }}"
                       class="btn w-100 d-flex align-items-center justify-content-center gap-2 border fw-semibold"
                       style="min-height:56px;border-radius:10px;border-color:var(--tp-border);color:var(--tp-text);background:#fff;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                        Continuar con Google
                    </a>
                </div>

                {{-- REGISTER FORM --}}
                <div id="form-register" style="display:none;">
                    <form method="POST" action="{{ route('register') }}" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="reg-nombre" class="visually-hidden">Nombre completo</label>
                            <div class="input-group login-input-group">
                                <span class="input-group-text login-input-icon">
                                    <i class="bi bi-person" aria-hidden="true"></i>
                                </span>
                                <input id="reg-nombre" type="text" name="nombre_completo"
                                       value="{{ old('nombre_completo') }}"
                                       class="form-control login-input"
                                       placeholder="Nombre completo" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="reg-email" class="visually-hidden">Correo electrónico</label>
                            <div class="input-group login-input-group">
                                <span class="input-group-text login-input-icon">
                                    <i class="bi bi-envelope" aria-hidden="true"></i>
                                </span>
                                <input id="reg-email" type="email" name="email"
                                       value="{{ old('email') }}"
                                       class="form-control login-input"
                                       placeholder="Correo electrónico" required>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label for="reg-telefono" class="visually-hidden">Teléfono</label>
                                <div class="input-group login-input-group">
                                    <span class="input-group-text login-input-icon">
                                        <i class="bi bi-telephone" aria-hidden="true"></i>
                                    </span>
                                    <input id="reg-telefono" type="text" name="telefono"
                                           value="{{ old('telefono') }}"
                                           class="form-control login-input" placeholder="Teléfono">
                                </div>
                            </div>
                            <div class="col">
                                <label for="reg-ci" class="visually-hidden">CI</label>
                                <div class="input-group login-input-group">
                                    <span class="input-group-text login-input-icon">
                                        <i class="bi bi-card-text" aria-hidden="true"></i>
                                    </span>
                                    <input id="reg-ci" type="text" name="ci"
                                           value="{{ old('ci') }}"
                                           class="form-control login-input" placeholder="CI (opcional)">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="reg-password" class="visually-hidden">Contraseña</label>
                            <div class="input-group login-input-group">
                                <span class="input-group-text login-input-icon">
                                    <i class="bi bi-lock" aria-hidden="true"></i>
                                </span>
                                <input id="reg-password" type="password" name="password"
                                       class="form-control login-input"
                                       placeholder="Contraseña (mín. 8 caracteres)" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="reg-password-confirm" class="visually-hidden">Confirmar contraseña</label>
                            <div class="input-group login-input-group">
                                <span class="input-group-text login-input-icon">
                                    <i class="bi bi-lock-fill" aria-hidden="true"></i>
                                </span>
                                <input id="reg-password-confirm" type="password" name="password_confirmation"
                                       class="form-control login-input"
                                       placeholder="Confirmar contraseña" required>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2 mb-3">
                            <input type="checkbox" name="terminos" id="terminos" value="1"
                                   class="form-check-input m-0 login-remember-check" required>
                            <label for="terminos" class="small login-remember-label">
                                Acepto los <a href="#" class="login-forgot-link">términos y condiciones</a>
                            </label>
                        </div>

                        <button type="submit"
                                class="btn login-submit w-100 d-flex align-items-center justify-content-center gap-2 border-0 fw-semibold text-white">
                            <i class="bi bi-person-plus" aria-hidden="true"></i>
                            Crear cuenta
                        </button>
                    </form>

                    <div class="d-flex align-items-center gap-3 my-4">
                        <span class="flex-grow-1" style="height:1px;background:#E5E7EB;"></span>
                        <span class="small" style="color:var(--tp-text-secondary);">o registrarse con</span>
                        <span class="flex-grow-1" style="height:1px;background:#E5E7EB;"></span>
                    </div>

                    <a href="{{ route('auth.google.redirect') }}"
                       class="btn w-100 d-flex align-items-center justify-content-center gap-2 border fw-semibold"
                       style="min-height:56px;border-radius:10px;border-color:var(--tp-border);color:var(--tp-text);background:#fff;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                        Continuar con Google
                    </a>
                </div>

                <div class="login-form-footer">
                    <p class="text-center small mb-0 login-footer-text">
                        <i class="bi bi-shield-check me-1"></i>
                        Acceso seguro · Tus datos están protegidos
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
    var loginForm = document.querySelector('#form-login form');
    var loginBtn = document.getElementById('loginButton');
    if (loginForm && loginBtn) {
        loginForm.addEventListener('submit', function() {
            loginBtn.disabled = true;
            loginBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Ingresando...';
        });
    }
    var registerForm = document.querySelector('#form-register form');
    if (registerForm) {
        registerForm.addEventListener('submit', function() {
            var btn = registerForm.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Creando cuenta...';
        });
    }
    @if ($errors->has('nombre_completo') || $errors->has('email') || $errors->has('password') || $errors->has('terminos'))
    switchTab('register');
    @endif
})();

function switchTab(tab) {
    document.getElementById('form-login').style.display = tab === 'login' ? 'block' : 'none';
    document.getElementById('form-register').style.display = tab === 'register' ? 'block' : 'none';
    document.getElementById('tab-login').classList.toggle('active', tab === 'login');
    document.getElementById('tab-register').classList.toggle('active', tab === 'register');
    document.getElementById('tab-login').setAttribute('aria-selected', tab === 'login');
    document.getElementById('tab-register').setAttribute('aria-selected', tab === 'register');
    if (tab === 'login') {
        document.getElementById('login').focus();
    } else {
        document.getElementById('reg-nombre').focus();
    }
}
</script>

</body>
</html>
