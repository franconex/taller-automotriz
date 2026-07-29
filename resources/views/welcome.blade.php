<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Taller Pro — Taller automotriz de confianza. Diagnóstico computarizado, mantenimiento preventivo, frenos, suspensión y más. Seguimiento en línea de tu vehículo.">
    <meta name="theme-color" content="#E31E24">
    <title>Taller Pro — Tu taller automotriz de confianza</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/landing.css', 'resources/js/landing.js'])
</head>
<body>

{{-- NAVBAR --}}
<nav class="navbar navbar-expand-lg landing-navbar fixed-top" aria-label="Navegación principal">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/">
            <img src="{{ asset('img/logo.png') }}" alt="Taller Pro">
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#landingNav" aria-label="Abrir menú">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="landingNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-1">
                <li class="nav-item"><a class="nav-link" href="#servicios">Servicios</a></li>
                <li class="nav-item"><a class="nav-link" href="#beneficios">Beneficios</a></li>
                <li class="nav-item"><a class="nav-link" href="#como-funciona">Cómo funciona</a></li>
                <li class="nav-item"><a class="nav-link" href="#contacto">Contacto</a></li>
                <li class="nav-item ms-lg-2">
                    <a href="{{ route('login') }}" class="btn btn-tp-outline btn-sm">Ingresar</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

{{-- HERO --}}
<section class="hero" aria-label="Inicio">
    <div class="hero__gradient"></div>
    <div class="hero__grid"></div>
    <div class="hero__orb hero__orb--1"></div>
    <div class="hero__orb hero__orb--2"></div>
    <div class="hero__bg-shape"></div>
    <div class="container hero__content">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="hero__badge animate-fade-up">
                    <span class="hero__badge-dot"></span>
                    Seguimiento en tiempo real
                </div>
                <h1 class="animate-fade-up delay-1">
                    Tu vehículo merece el<br>
                    <span class="highlight">mejor cuidado</span>
                </h1>
                <p class="lead animate-fade-up delay-2">
                    Diagnóstico preciso, servicio experto y seguimiento en tiempo real desde tu celular. Sin sorpresas, sin demoras.
                </p>
                <div class="d-flex flex-wrap gap-3 animate-fade-up delay-3">
                    <a href="{{ route('login') }}" class="btn btn-tp">
                        <i class="bi bi-calendar-check me-2"></i>Agendar cita
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-tp-light">
                        <i class="bi bi-person me-2"></i>Ingresar como cliente
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-tp-ghost">
                        <i class="bi bi-search me-2"></i>Consultar vehículo
                    </a>
                </div>
                <div class="hero__stats animate-fade-up delay-4">
                    <div>
                        <div class="hero__stat-value"><span class="num-highlight">+</span>10</div>
                        <div class="hero__stat-label">Años de experiencia</div>
                    </div>
                    <div>
                        <div class="hero__stat-value"><span class="num-highlight">+</span>5,000</div>
                        <div class="hero__stat-label">Vehículos reparados</div>
                    </div>
                    <div>
                        <div class="hero__stat-value"><span class="num-highlight">+</span>3</div>
                        <div class="hero__stat-label">Sucursales</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero__img-wrapper animate-fade-right">
                    <img src="{{ asset('img/portada-talle.png') }}" alt="Taller automotriz profesional" loading="lazy">
                    <div class="hero__img-floating">
                        <div class="hero__img-floating-icon">
                            <i class="bi bi-check-lg"></i>
                        </div>
                        <div class="hero__img-floating-text">
                            <strong>Diagnóstico en 30 min</strong>
                            <small>Resultados rápidos y precisos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- STATS BAR --}}
<section class="stats-bar" aria-label="Indicadores">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <div class="col-6 col-md-3">
                <div class="stats-bar__item animate-scale">
                    <div class="stats-bar__number" data-target="10">0</div>
                    <div class="stats-bar__label">Años de experiencia</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stats-bar__item animate-scale delay-1">
                    <div class="stats-bar__number" data-target="5000">0</div>
                    <div class="stats-bar__label">Vehículos reparados</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stats-bar__item animate-scale delay-2">
                    <div class="stats-bar__number" data-target="12">0</div>
                    <div class="stats-bar__label">Técnicos certificados</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stats-bar__item animate-scale delay-3">
                    <div class="stats-bar__number" data-target="3">0</div>
                    <div class="stats-bar__label">Sucursales</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- SERVICIOS --}}
<section class="services-section py-5" id="servicios" aria-label="Servicios">
    <div class="container py-5">
        <div class="section-header animate-fade-up">
            <h2>Servicios profesionales</h2>
            <div class="accent-line"></div>
            <p>Contamos con tecnología de punta y técnicos certificados para brindarte el mejor servicio.</p>
        </div>
        <div class="row g-4">
            @php
                $servicios = [
                    ['icon' => 'bi-gear', 'titulo' => 'Mecánica general', 'desc' => 'Reparación y mantenimiento de motor, transmisión, sistema de escape y más.'],
                    ['icon' => 'bi-laptop', 'titulo' => 'Diagnóstico computarizado', 'desc' => 'Escaneo electrónico completo para identificar fallas con precisión.'],
                    ['icon' => 'bi-droplet', 'titulo' => 'Cambio de aceite', 'desc' => 'Aceites de alta calidad para proteger y prolongar la vida del motor.'],
                    ['icon' => 'bi-tools', 'titulo' => 'Frenos y suspensión', 'desc' => 'Revisión y reparación de frenos, amortiguadores y dirección.'],
                    ['icon' => 'bi-battery-charging', 'titulo' => 'Electricidad automotriz', 'desc' => 'Sistema eléctrico, batería, alternador, sensores y módulos.'],
                    ['icon' => 'bi-calendar-range', 'titulo' => 'Mantenimiento preventivo', 'desc' => 'Programa tu mantenimiento según el kilometraje y recomendaciones del fabricante.'],
                ];
            @endphp
            @foreach ($servicios as $i => $s)
                <div class="col-md-6 col-lg-4">
                    <div class="service-card animate-fade-up delay-{{ min($i + 1, 4) }}">
                        <div class="service-card__icon"><i class="bi {{ $s['icon'] }}"></i></div>
                        <h5>{{ $s['titulo'] }}</h5>
                        <p>{{ $s['desc'] }}</p>
                        <span class="service-card__link">
                            Más información <i class="bi bi-arrow-right"></i>
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- BENEFICIOS --}}
<section class="py-5" id="beneficios" aria-label="Beneficios">
    <div class="container py-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <img src="{{ asset('img/taller-equipo.png') }}" alt="Equipo de Taller Pro" class="img-fluid rounded-4 shadow-sm animate-fade-left" loading="lazy">
            </div>
            <div class="col-lg-6">
                <div class="section-header text-start mb-4 animate-fade-right">
                    <h2>¿Por qué elegirnos?</h2>
                    <div class="accent-line accent-line--left"></div>
                </div>
                @php
                    $beneficios = [
                        ['icon' => 'bi-shield-check', 'titulo' => 'Garantía en todos los servicios', 'desc' => 'Respaldo real en cada reparación. Si algo no queda bien, lo arreglamos sin costo.'],
                        ['icon' => 'bi-phone', 'titulo' => 'Seguimiento desde tu celular', 'desc' => 'Consulta el estado de tu vehículo en tiempo real desde el portal del cliente.'],
                        ['icon' => 'bi-cash-stack', 'titulo' => 'Presupuesto sin compromiso', 'desc' => 'Te informamos antes de cualquier reparación. Tú decides.'],
                        ['icon' => 'bi-clock-history', 'titulo' => 'Trabajo en el tiempo acordado', 'desc' => 'Respetamos los plazos. Si no cumplimos, te informamos de inmediato.'],
                    ];
                @endphp
                @foreach ($beneficios as $i => $b)
                    <div class="benefit-item animate-fade-right delay-{{ min($i + 1, 4) }}">
                        <div class="benefit-item__icon"><i class="bi {{ $b['icon'] }}"></i></div>
                        <div>
                            <h6>{{ $b['titulo'] }}</h6>
                            <p>{{ $b['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- CÓMO FUNCIONA --}}
<section class="how-section py-5" id="como-funciona" aria-label="Cómo funciona">
    <div class="container py-5">
        <div class="section-header animate-fade-up">
            <h2>Así funciona el seguimiento</h2>
            <div class="accent-line"></div>
            <p>Desde que dejas tu vehículo hasta que lo recibes, estás informado en cada paso.</p>
        </div>
        <div class="row justify-content-center">
            @php
                $pasos = [
                    ['num' => '1', 'titulo' => 'Agenda tu cita', 'desc' => 'Programa el servicio desde el portal o llámanos. Te asignaremos un horario.'],
                    ['num' => '2', 'titulo' => 'Recibimos tu vehículo', 'desc' => 'Lo registramos y realizamos un diagnóstico inicial. Te enviamos el presupuesto.'],
                    ['num' => '3', 'titulo' => 'Sigue el progreso', 'desc' => 'Ingresa al portal y ve el avance en tiempo real: diagnóstico, reparación, pruebas.'],
                    ['num' => '4', 'titulo' => 'Recibe notificaciones', 'desc' => 'Te avisamos cuando el trabajo esté listo. Generamos tu comprobante digital.'],
                ];
            @endphp
            @foreach ($pasos as $i => $p)
                <div class="col-md-6 col-lg-3 position-relative">
                    <div class="step-card animate-fade-up delay-{{ min($i + 1, 4) }}">
                        @if ($i < count($pasos) - 1)
                            <div class="step-connector"></div>
                        @endif
                        <div class="step-card__number">{{ $p['num'] }}</div>
                        <h6>{{ $p['titulo'] }}</h6>
                        <p>{{ $p['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- EQUIPO --}}
<section class="py-5" id="equipo" aria-label="Equipo">
    <div class="container py-5">
        <div class="section-header animate-fade-up">
            <h2>Conoce a nuestro equipo</h2>
            <div class="accent-line"></div>
            <p>Profesionales apasionados por la mecánica, comprometidos con tu satisfacción.</p>
        </div>
        <div class="row justify-content-center g-4">
            @php
                $equipo = [
                    ['nombre' => 'Carlos Méndez', 'rol' => 'Director técnico', 'red' => '#'],
                    ['nombre' => 'Ana López', 'rol' => 'Mecánica especialista', 'red' => '#'],
                    ['nombre' => 'Roberto Vargas', 'rol' => 'Diagnóstico computarizado', 'red' => '#'],
                ];
            @endphp
            @foreach ($equipo as $i => $m)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="team-card animate-scale delay-{{ min($i + 1, 4) }}">
                        <div class="team-card__img-wrap">
                            <img src="{{ 'https://ui-avatars.com/api/?name='.urlencode($m['nombre']).'&background=E31E24&color=fff&size=120' }}" alt="{{ $m['nombre'] }}" loading="lazy">
                        </div>
                        <h6>{{ $m['nombre'] }}</h6>
                        <div class="text-muted">{{ $m['rol'] }}</div>
                        <div class="team-card__social">
                            <a href="#" aria-label="LinkedIn {{ $m['nombre'] }}"><i class="bi bi-linkedin"></i></a>
                            <a href="#" aria-label="Email {{ $m['nombre'] }}"><i class="bi bi-envelope"></i></a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- TESTIMONIOS --}}
<section class="testimonials-section py-5" id="testimonios" aria-label="Testimonios">
    <div class="container py-5">
        <div class="section-header animate-fade-up">
            <h2 class="text-white">Lo que dicen nuestros clientes</h2>
            <div class="accent-line"></div>
            <p>Más de 10 años cuidando los vehículos de nuestra comunidad.</p>
        </div>
        <div class="row g-4">
            @php
                $testimonios = [
                    ['texto' => 'Dejé mi auto para una reparación completa y pude ver el avance desde casa. El trabajo quedó impecable y en el tiempo prometido.', 'nombre' => 'María G.', 'rol' => 'Cliente frecuente'],
                    ['texto' => 'El diagnóstico computarizado salvó mi motor. Me explicaron cada detalle antes de hacer cualquier cosa. Total confianza.', 'nombre' => 'Pedro R.', 'rol' => 'Cliente desde 2022'],
                    ['texto' => 'Cambio de frenos y suspensión en un día. El sistema de seguimiento me mantuvo tranquilo todo el proceso.', 'nombre' => 'Lucía M.', 'rol' => 'Cliente frecuente'],
                ];
            @endphp
            @foreach ($testimonios as $i => $t)
                <div class="col-md-4">
                    <div class="testimonial-card animate-fade-up delay-{{ min($i + 1, 4) }} {{ $i === 0 ? 'testimonial-card--featured' : '' }}">
                        <div class="stars">
                            @for ($j = 0; $j < 5; $j++)<i class="bi bi-star-fill"></i>@endfor
                        </div>
                        <blockquote>"{{ $t['texto'] }}"</blockquote>
                        <cite>
                            <img src="{{ 'https://ui-avatars.com/api/?name='.urlencode($t['nombre']).'&background=fff&color=E31E24&size=42' }}" alt="{{ $t['nombre'] }}" loading="lazy">
                            <div>
                                <span>{{ $t['nombre'] }}</span>
                                <small>{{ $t['rol'] }}</small>
                            </div>
                        </cite>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- PREGUNTAS FRECUENTES --}}
<section class="py-5" id="faq" aria-label="Preguntas frecuentes">
    <div class="container py-5">
        <div class="section-header animate-fade-up">
            <h2>Preguntas frecuentes</h2>
            <div class="accent-line"></div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion faq-accordion" id="faqAccordion">
                    @php
                        $faqs = [
                            ['q' => '¿Cuánto tiempo toma una reparación?', 'r' => 'Depende del tipo de servicio. El diagnóstico inicial toma de 30 a 60 minutos. Las reparaciones menores se completan el mismo día. Te informaremos el plazo estimado antes de comenzar.'],
                            ['q' => '¿Puedo seguir el progreso de mi vehículo?', 'r' => 'Sí. Como cliente registrado puedes ingresar al portal y ver el estado actual, el porcentaje de avance, las notas del mecánico y las evidencias fotográficas en tiempo real.'],
                            ['q' => '¿Ofrecen garantía?', 'r' => 'Todos nuestros servicios tienen garantía. La cobertura varía según el tipo de reparación. Te entregamos los detalles por escrito al finalizar el trabajo.'],
                            ['q' => '¿Cómo agendo una cita?', 'r' => 'Puedes agendar desde el portal del cliente haciendo clic en "Agendar cita" o llamándonos directamente. Si eres nuevo, crea tu cuenta y agenda en minutos.'],
                            ['q' => '¿Aceptan pagos con tarjeta?', 'r' => 'Sí. Aceptamos tarjetas de crédito, débito, transferencias bancarias y pagos mediante código QR. También puedes pagar en efectivo en nuestra sucursal.'],
                        ];
                    @endphp
                    @foreach ($faqs as $i => $faq)
                        <div class="accordion-item animate-fade-up delay-{{ min($i + 1, 4) }}">
                            <h3 class="accordion-header">
                                <button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $i }}" aria-expanded="{{ $i === 0 ? 'true' : 'false' }}">
                                    {{ $faq['q'] }}
                                </button>
                            </h3>
                            <div id="faq{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">{{ $faq['r'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="cta-section py-5" aria-label="Llamado a la acción">
    <div class="container py-5 text-center position-relative">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h2 class="animate-fade-up">¿Listo para cuidar tu vehículo?</h2>
                <p class="lead opacity-75 mb-4 animate-fade-up delay-1">Agenda tu cita hoy y recibe un diagnóstico profesional sin compromiso.</p>
                <div class="d-flex flex-wrap justify-content-center gap-3 animate-fade-up delay-2">
                    <a href="{{ route('login') }}" class="btn btn-tp btn-lg px-4">
                        <i class="bi bi-calendar-check me-2"></i>Agendar cita
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-tp-light btn-lg px-4">
                        <i class="bi bi-chat-dots me-2"></i>Contactar ahora
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- CONTACTO --}}
<section class="contact-section py-5" id="contacto" aria-label="Contacto">
    <div class="container py-5">
        <div class="row g-5">
            <div class="col-lg-5">
                <div class="section-header text-start mb-4 animate-fade-left">
                    <h2>Contáctanos</h2>
                    <div class="accent-line accent-line--left"></div>
                    <p>Estamos listos para ayudarte. Respuesta rápida, sin letras chicas.</p>
                </div>
                @php
                    $contactos = [
                        ['icon' => 'bi-geo-alt', 'titulo' => 'Dirección', 'desc' => 'Av. Cristo Redentor #1250, Santa Cruz, Bolivia'],
                        ['icon' => 'bi-telephone', 'titulo' => 'Teléfono', 'desc' => '+591 3 345 6789'],
                        ['icon' => 'bi-envelope', 'titulo' => 'Email', 'desc' => 'info@tallerpro.com'],
                        ['icon' => 'bi-clock', 'titulo' => 'Horarios', 'desc' => 'Lun — Vie: 7:00 – 19:00 | Sáb: 7:00 – 14:00'],
                    ];
                @endphp
                @foreach ($contactos as $i => $c)
                    <div class="contact-info-item animate-fade-left delay-{{ min($i + 1, 4) }}">
                        <div class="contact-info-item__icon"><i class="bi {{ $c['icon'] }}"></i></div>
                        <div>
                            <h6>{{ $c['titulo'] }}</h6>
                            <p>{{ $c['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="col-lg-7">
                <div class="animate-fade-right">
                    <form class="contact-form row g-3" onsubmit="event.preventDefault(); alert('Gracias por contactarnos. Te responderemos a la brevedad.');">
                        @csrf
                        <div class="col-md-6">
                            <label for="nombre" class="form-label small fw-semibold">Nombre</label>
                            <input type="text" class="form-control" id="nombre" required placeholder="Tu nombre">
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label small fw-semibold">Email</label>
                            <input type="email" class="form-control" id="email" required placeholder="correo@ejemplo.com">
                        </div>
                        <div class="col-12">
                            <label for="mensaje" class="form-label small fw-semibold">Mensaje</label>
                            <textarea class="form-control" id="mensaje" rows="4" required placeholder="¿En qué podemos ayudarte?"></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-tp"><i class="bi bi-send me-2"></i>Enviar mensaje</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- MAPA --}}
<section class="map-section" aria-label="Ubicación">
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3799.999!2d-63.170!3d-17.780!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTfCsDQ2JzQ4LjAiUyA2M8KwMTAnMTIuMCJX!5e0!3m2!1ses!2sbo!4v1" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Ubicación de Taller Pro"></iframe>
</section>

{{-- FOOTER --}}
<footer class="landing-footer py-5" aria-label="Pie de página">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="footer-brand">
                    <img src="{{ asset('img/logo-modo-oscuro.png') }}" alt="Taller Pro" loading="lazy">
                    <p class="small opacity-75">Taller automotriz profesional con seguimiento en tiempo real. Más de 10 años cuidando tu vehículo en Santa Cruz, Bolivia.</p>
                </div>
                <div class="d-flex gap-2 mt-3">
                    <a href="#" class="social-link" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="social-link" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="social-link" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                    <a href="#" class="social-link" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
                </div>
            </div>
            <div class="col-6 col-lg-2">
                <h6>Servicios</h6>
                <ul class="list-unstyled">
                    <li><a href="#servicios">Mecánica general</a></li>
                    <li><a href="#servicios">Diagnóstico</a></li>
                    <li><a href="#servicios">Frenos y suspensión</a></li>
                    <li><a href="#servicios">Electricidad</a></li>
                    <li><a href="#servicios">Mantenimiento</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <h6>Cliente</h6>
                <ul class="list-unstyled">
                    <li><a href="{{ route('login') }}">Iniciar sesión</a></li>
                    <li><a href="{{ route('login') }}">Agendar cita</a></li>
                    <li><a href="{{ route('login') }}">Consultar vehículo</a></li>
                    <li><a href="#faq">Preguntas frecuentes</a></li>
                </ul>
            </div>
            <div class="col-lg-4">
                <h6>Acceso del personal</h6>
                <ul class="list-unstyled">
                    <li>
                        <a href="{{ route('login') }}" class="opacity-50">
                            <i class="bi bi-lock me-1"></i>Área administrativa
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('login') }}" class="opacity-50">
                            <i class="bi bi-lock me-1"></i>Portal del mecánico
                        </a>
                    </li>
                </ul>
                <h6 class="mt-4">Sucursales</h6>
                <ul class="list-unstyled small opacity-75">
                    <li class="mb-1"><i class="bi bi-geo-alt me-1"></i>Sucursal Principal — Santa Cruz</li>
                    <li class="mb-1"><i class="bi bi-geo-alt me-1"></i>Sucursal Norte — Montero</li>
                    <li class="mb-1"><i class="bi bi-geo-alt me-1"></i>Sucursal Sur — La Guardia</li>
                </ul>
            </div>
        </div>
        <hr>
        <div class="row align-items-center">
            <div class="col-md-6 small">&copy; {{ date('Y') }} Taller Pro. Todos los derechos reservados.</div>
            <div class="col-md-6 text-md-end small mt-2 mt-md-0">
                Hecho con <i class="bi bi-heart-fill" style="color:var(--tp-red);"></i> en Santa Cruz, Bolivia
            </div>
        </div>
    </div>
</footer>

{{-- BACK TO TOP --}}
<button class="back-to-top" aria-label="Volver arriba">
    <i class="bi bi-chevron-up"></i>
</button>

</body>
</html>
