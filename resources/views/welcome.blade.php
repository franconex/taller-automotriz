<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Taller Pro — Taller automotriz de confianza. Diagnóstico computarizado, mantenimiento preventivo, frenos, suspensión y más. Seguimiento en línea de tu vehículo.">
    <meta name="theme-color" content="#1A1A1A">
    <title>Taller Pro — Tu taller automotriz de confianza</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/landing.css', 'resources/js/landing.js'])
</head>
<body>

{{-- NAVBAR --}}
<nav class="nav" id="nav" aria-label="Navegación principal">
    <div class="nav__inner container">
        <a class="nav__logo" href="#hero">
            <img src="{{ asset('img/logo.png') }}" alt="Taller Pro">
        </a>
        <ul class="nav__links" id="navLinks">
            <li><a href="#hero" data-link="hero">Inicio</a></li>
            <li><a href="#servicios" data-link="servicios">Servicios</a></li>
            <li><a href="#como-funciona" data-link="como-funciona">Proceso</a></li>
            <li><a href="#sucursales" data-link="sucursales">Sucursales</a></li>
            <li><a href="#contacto" data-link="contacto">Contacto</a></li>
        </ul>
        <div class="nav__actions">
            <a href="{{ route('login') }}" class="nav__login">Ingresar</a>
            <a href="{{ route('login') }}" class="nav__cta">Agendar cita</a>
        </div>
        <button class="nav__toggle" id="navToggle" type="button" aria-label="Abrir menú">
            <span></span><span></span><span></span>
        </button>
    </div>
    <div class="nav__mobile" id="navMobile">
        <a href="#hero" data-link="hero">Inicio</a>
        <a href="#servicios" data-link="servicios">Servicios</a>
        <a href="#como-funciona" data-link="como-funciona">Proceso</a>
        <a href="#sucursales" data-link="sucursales">Sucursales</a>
        <a href="#contacto" data-link="contacto">Contacto</a>
        <a href="{{ route('login') }}">Ingresar</a>
        <a href="{{ route('login') }}" class="nav__mobile-cta">Agendar cita</a>
    </div>
</nav>

{{-- HERO --}}
<section class="hero" id="hero">
    <div class="hero__image">
        <img src="{{ asset('img/portada-talle.png') }}" alt="Taller automotriz profesional" loading="eager">
        <div class="hero__overlay"></div>
    </div>
    <div class="hero__content container">
        <div class="hero__inner">
            <div class="hero__eyebrow">
                <span class="hero__eyebrow-line"></span>
                <span>Santa Cruz &middot; Bolivia</span>
            </div>
            <h1 class="hero__title">
                Mecánica que<br>entiende a tu vehículo
            </h1>
            <p class="hero__lead">
                Diagnóstico, mantenimiento y reparación. Con seguimiento en tiempo real desde tu celular, sin sorpresas ni demoras.
            </p>
            <div class="hero__actions">
                <a href="{{ route('login') }}" class="btn btn--primary">Agendar cita</a>
                <a href="{{ route('login') }}" class="btn btn--ghost">Ingresar como cliente</a>
            </div>
        </div>
    </div>
    <div class="hero__strip">
        <div class="container hero__strip-inner">
            <div class="hero__strip-item">
                <span class="hero__strip-num">10+</span>
                <span class="hero__strip-label">años de experiencia</span>
            </div>
            <div class="hero__strip-item">
                <span class="hero__strip-num">5.000+</span>
                <span class="hero__strip-label">vehículos atendidos</span>
            </div>
            <div class="hero__strip-item">
                <span class="hero__strip-num">3</span>
                <span class="hero__strip-label">sucursales</span>
            </div>
            <div class="hero__strip-item">
                <span class="hero__strip-num">100%</span>
                <span class="hero__strip-label">con garantía</span>
            </div>
        </div>
    </div>
</section>

{{-- FRANJA DE CONFIANZA --}}
<section class="trust" aria-label="Beneficios y confianza">
    <div class="container trust__inner">
        <ul class="trust__list">
            <li class="trust__item" data-reveal="up" data-reveal-delay="1">
                <span class="trust__icon" aria-hidden="true">@include('icons.clock')</span>
                <span class="trust__text">Puntualidad real</span>
            </li>
            <li class="trust__item" data-reveal="up" data-reveal-delay="2">
                <span class="trust__icon" aria-hidden="true">@include('icons.shield')</span>
                <span class="trust__text">Garantía de servicio</span>
            </li>
            <li class="trust__item" data-reveal="up" data-reveal-delay="3">
                <span class="trust__icon" aria-hidden="true">@include('icons.message')</span>
                <span class="trust__text">Avances en línea</span>
            </li>
            <li class="trust__item" data-reveal="up" data-reveal-delay="4">
                <span class="trust__icon" aria-hidden="true">@include('icons.check')</span>
                <span class="trust__text">Presupuesto transparente</span>
            </li>
        </ul>
    </div>
</section>

{{-- BENEFICIOS --}}
<section class="section section--white" id="beneficios">
    <div class="container">
        <div class="benefits">
            <div class="benefits__media" data-reveal="left">
                <img src="{{ asset('img/taller-equipo.png') }}" alt="Equipo de Taller Pro" loading="lazy">
            </div>
            <div class="benefits__content" data-reveal="right">
                <p class="section__label">Por qué elegirnos</p>
                <h2 class="section__title">Una atención<br>que se nota</h2>
                <div class="accent-line accent-line--left"></div>
                @php
                    $beneficios = [
                        ['icon' => 'shield', 'titulo' => 'Garantía real', 'desc' => 'Respaldo por escrito en cada reparación. Si algo no queda bien, lo arreglamos sin costo.'],
                        ['icon' => 'phone', 'titulo' => 'Seguimiento en vivo', 'desc' => 'Consulta el estado de tu vehículo desde el portal del cliente, cuando quieras.'],
                        ['icon' => 'cash', 'titulo' => 'Presupuesto previo', 'desc' => 'Te informamos antes de cualquier reparación. Tú decides si seguimos.'],
                        ['icon' => 'clock', 'titulo' => 'Tiempos respetados', 'desc' => 'Cumplimos los plazos. Si algo cambia, te avisamos de inmediato.'],
                    ];
                @endphp
                <div class="benefits__list">
                    @foreach ($beneficios as $i => $b)
                        <div class="benefit" data-reveal="up" data-reveal-delay="{{ $i + 1 }}">
                            <div class="benefit__icon">@include('icons.'.$b['icon'])</div>
                            <div>
                                <h6>{{ $b['titulo'] }}</h6>
                                <p>{{ $b['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- SERVICIOS --}}
<section class="section section--light" id="servicios">
    <div class="container">
        <div class="section__head" data-reveal="up">
            <div>
                <p class="section__label">Servicios</p>
                <h2 class="section__title">Lo que<br>hacemos</h2>
            </div>
            <p class="section__sub">Cada servicio se realiza con tecnología de punta, técnicos certificados y repuestos de calidad. Pasa el cursor sobre cada tarjeta para ver los detalles.</p>
        </div>

        @php
            $servicios = [
                [
                    'titulo' => 'Mecánica general',
                    'icono' => 'engine',
                    'desc_corta' => 'Reparación y mantenimiento de motor, transmisión, sistema de escape y más.',
                    'imagen' => 'servicio-mecanica-general.png',
                    'desc_larga' => 'Diagnosticamos y reparamos problemas del motor, transmisión manual y automática, sistema de escape, dirección y suspensión. Utilizamos herramientas especializadas para garantizar un trabajo duradero.',
                    'beneficios' => ['Diagnóstico preciso con escáner', 'Repuestos originales o de calidad garantizada', 'Servicio rápido y organizado'],
                    'tiempo' => '60 — 180 min',
                ],
                [
                    'titulo' => 'Diagnóstico computarizado',
                    'icono' => 'diag',
                    'desc_corta' => 'Escaneo electrónico completo para identificar fallas con precisión.',
                    'imagen' => 'servicio-diagnostico-computarizado.png',
                    'desc_larga' => 'Conectamos tu vehículo a un escáner de última generación que lee códigos de falla en todos los sistemas electrónicos: motor, transmisión, frenos ABS, bolsas de aire y más.',
                    'beneficios' => ['Identificación precisa de fallas', 'Ahorro en reparaciones innecesarias', 'Reporte detallado por escrito'],
                    'tiempo' => '30 — 45 min',
                ],
                [
                    'titulo' => 'Cambio de aceite',
                    'icono' => 'oil',
                    'desc_corta' => 'Aceites de alta calidad para proteger y prolongar la vida del motor.',
                    'imagen' => 'servicio-cambio-aceite.png',
                    'desc_larga' => 'Realizamos el cambio de aceite y filtro con productos de las mejores marcas. Asesoramos sobre la viscosidad adecuada según el fabricante.',
                    'beneficios' => ['Aceites Castrol, Mobil y Shell', 'Cambio de filtro incluido', 'Revisión rápida de 10 puntos'],
                    'tiempo' => '20 — 30 min',
                ],
                [
                    'titulo' => 'Frenos y suspensión',
                    'icono' => 'brake',
                    'desc_corta' => 'Revisión y reparación de frenos, amortiguadores y dirección.',
                    'imagen' => 'servicio-frenos-suspension.png',
                    'desc_larga' => 'Inspeccionamos todo el sistema de frenos (pastillas, discos, tambores, líquido) y la suspensión (amortiguadores, resortes, rótulas, terminales).',
                    'beneficios' => ['Medición de espesor de discos', 'Líquido de frenos purgado', 'Alineación y balanceo incluido'],
                    'tiempo' => '60 — 120 min',
                ],
                [
                    'titulo' => 'Electricidad automotriz',
                    'icono' => 'elec',
                    'desc_corta' => 'Sistema eléctrico, batería, alternador, sensores y módulos.',
                    'imagen' => 'servicio-electricidad-automotriz.png',
                    'desc_larga' => 'Reparamos y mantenemos sistemas eléctricos y electrónicos: batería, alternador, motor de arranque, sensores, módulos de control, sistema de iluminación y accesorios.',
                    'beneficios' => ['Diagnóstico con multímetro y osciloscopio', 'Reparación de módulos electrónicos', 'Instalación de accesorios eléctricos'],
                    'tiempo' => '45 — 90 min',
                ],
                [
                    'titulo' => 'Mantenimiento preventivo',
                    'icono' => 'maintenance',
                    'desc_corta' => 'Programa tu mantenimiento según el kilometraje y recomendaciones del fabricante.',
                    'imagen' => 'servicio-mantenimiento-preventivo.png',
                    'desc_larga' => 'Ofrecemos paquetes de mantenimiento programado por kilometraje: cambio de aceite, filtros, bujías, correas, líquidos y revisión general. Prolongamos la vida útil de tu vehículo.',
                    'beneficios' => ['Checklist de 30 puntos', 'Plan personalizado por vehículo', 'Recordatorio automático de servicio'],
                    'tiempo' => '120 — 180 min',
                ],
            ];
        @endphp

        <div class="services" id="servicesGrid">
            @foreach ($servicios as $s)
                <article class="service" tabindex="0" aria-label="{{ $s['titulo'] }}">
                    <div class="service__front">
                        <div class="service__img">
                            <img src="{{ asset('img/'.$s['imagen']) }}" alt="{{ $s['titulo'] }}" loading="lazy">
                        </div>
                        <span class="service__hint">Ver detalles</span>
                        <div class="service__info">
                            <div class="service__icon">@include('icons.'.$s['icono'])</div>
                            <h3 class="service__name">{{ $s['titulo'] }}</h3>
                            <p class="service__desc">{{ $s['desc_corta'] }}</p>
                        </div>
                    </div>
                    <div class="service__detail">
                        <div class="service__detail-body">
                            <div class="service__detail-head">
                                <div class="service__icon">@include('icons.'.$s['icono'])</div>
                                <h3>{{ $s['titulo'] }}</h3>
                            </div>
                            <p class="service__detail-text">{{ $s['desc_larga'] }}</p>
                            <ul class="service__benefits">
                                @foreach ($s['beneficios'] as $b)
                                    <li><span class="service__check">+</span> {{ $b }}</li>
                                @endforeach
                            </ul>
                            <div class="service__time">
                                <span class="service__time-icon">@include('icons.clock')</span>
                                Tiempo estimado: <strong>{{ $s['tiempo'] }}</strong>
                            </div>
                            <a href="{{ route('login') }}" class="btn btn--primary btn--sm">Agendar este servicio</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- PASO A PASO --}}
<section class="section section--white" id="como-funciona">
    <div class="container">
        <div class="paso-head" data-reveal="up">
            <div class="paso-head__text">
                <p class="paso-head__eyebrow">PASO A PASO</p>
                <h2 class="paso-head__title">
                    Del problema a la solución,<br>
                    <span class="paso-head__title-accent">sin vueltas.</span>
                </h2>
            </div>
            <a href="{{ route('login') }}" class="btn btn--black">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="square">
                    <rect x="3" y="5" width="18" height="16"/>
                    <line x1="3" y1="9" x2="21" y2="9"/>
                    <line x1="8" y1="3" x2="8" y2="7"/>
                    <line x1="16" y1="3" x2="16" y2="7"/>
                </svg>
                Reservar ahora
            </a>
        </div>

        @php
            $pasos = [
                ['num' => '01', 'titulo' => 'Agenda tu cita', 'desc' => 'Elige el día, registra tu vehículo y cuéntanos qué sucede.'],
                ['num' => '02', 'titulo' => 'Recibe diagnóstico', 'desc' => 'Asignamos un mecánico y te enviamos el presupuesto.'],
                ['num' => '03', 'titulo' => 'Sigue el avance', 'desc' => 'Mira el progreso, repuestos y tiempo estimado desde tu panel.'],
                ['num' => '04', 'titulo' => 'Retira tranquilo', 'desc' => 'Pagas en recepción y te entregamos el vehículo revisado.'],
            ];
        @endphp

        <ol class="timeline">
            <span class="timeline__line" aria-hidden="true"></span>
            @foreach ($pasos as $i => $p)
                <li class="timeline__step {{ $i === 0 ? 'is-active' : '' }}">
                    <span class="timeline__num">{{ $p['num'] }}</span>
                    <h3 class="timeline__title">{{ $p['titulo'] }}</h3>
                    <p class="timeline__desc">{{ $p['desc'] }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>

{{-- SUCURSALES --}}
<section class="section section--light" id="sucursales">
    <div class="container">
        <div class="section__head" data-reveal="up">
            <div>
                <p class="section__label">Ubicaciones</p>
                <h2 class="section__title">Nuestras<br>sucursales</h2>
            </div>
            <p class="section__sub">Estamos cerca de ti con dos sucursales en Santa Cruz. Elige la que te queda más cómoda o agenda directamente desde aquí.</p>
        </div>

        @php
            try {
                $sucursales = \App\Models\Sucursal::where('estado', true)->orderBy('nombre')->get();
            } catch (\Exception $e) {
                $sucursales = collect([]);
            }
        @endphp

        @if ($sucursales->count() > 0)
            <div class="sucursales">
                @foreach ($sucursales as $i => $sucursal)
                    @php
                        $mapsUrl = 'https://www.google.com/maps/dir/?api=1&destination='.$sucursal->latitud.','.$sucursal->longitud;
                    @endphp
                    <div class="sucursal" data-reveal="up" data-reveal-delay="{{ $i + 1 }}">
                        <div class="sucursal__head">
                            <div class="sucursal__num">Sucursal {{ str_pad($i+1, 2, '0', STR_PAD_LEFT) }}</div>
                            <h3 class="sucursal__name">{{ $sucursal->nombre }}</h3>
                            <span class="sucursal__status">
                                <span class="sucursal__dot"></span>Abierto
                            </span>
                        </div>
                        <div class="sucursal__body">
                            <div class="sucursal__row">
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M8 1C5.2 1 3 3.2 3 6c0 3.8 5 9 5 9s5-5.2 5-9c0-2.8-2.2-5-5-5z" stroke="currentColor" stroke-width="1.2"/><circle cx="8" cy="6" r="2" fill="currentColor"/></svg>
                                <span>{{ $sucursal->direccion }}</span>
                            </div>
                            <div class="sucursal__row">
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M14.5 11.5l-3-1.5-2 2-4-4 2-2-1.5-3h-4C1.5 9 7 14.5 14.5 11.5z" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round"/></svg>
                                <span>{{ $sucursal->telefono }}</span>
                            </div>
                            <div class="sucursal__row">
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.2"/><path d="M8 4.5V8l2.5 1.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
                                <span>
                                    @php $h = $sucursal->horario_atencion; @endphp
                                    @if (is_array($h))
                                        Lun-Vie {{ $h['weekday']['open'] ?? '--' }}-{{ $h['weekday']['close'] ?? '--' }},
                                        Sáb {{ $h['saturday']['open'] ?? '--' }}-{{ $h['saturday']['close'] ?? '--' }}
                                    @else
                                        {{ $h }}
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="sucursal__actions">
                            <a href="{{ $mapsUrl }}" target="_blank" rel="noopener" class="btn btn--outline btn--sm">Cómo llegar</a>
                            <a href="{{ route('login') }}" class="btn btn--primary btn--sm">Agendar en esta sucursal</a>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="sucursal__map">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3799.999!2d-63.170!3d-17.780!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTfCsDQ2JzQ4LjAiUyA2M8KwMTAnMTIuMCJX!5e0!3m2!1ses!2sbo!4v1" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Ubicación de Taller Pro"></iframe>
            </div>
        @endif
    </div>
</section>

{{-- CTA --}}
<section class="section cta" id="cta">
    <div class="cta__bg" aria-hidden="true"></div>
    <div class="cta__overlay" aria-hidden="true"></div>
    <div class="container cta__container">
        <div class="cta__inner">
            <p class="cta__eyebrow">¿LISTO PARA EMPEZAR?</p>
            <h2 class="cta__title">
                Tu auto merece un servicio<br>
                <span class="cta__title-dark">a la altura.</span>
            </h2>
            <p class="cta__text">Agenda en menos de dos minutos. Nosotros nos encargamos del resto.</p>
            <div class="cta__actions">
                <a href="{{ route('login') }}" class="btn btn--white">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="square" stroke-linejoin="miter">
                        <rect x="3" y="5" width="18" height="16"/>
                        <line x1="3" y1="9" x2="21" y2="9"/>
                        <line x1="8" y1="3" x2="8" y2="7"/>
                        <line x1="16" y1="3" x2="16" y2="7"/>
                    </svg>
                    Reservar una cita
                </a>
                <a href="#sucursales" class="btn btn--ghost cta__btn-ghost">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="square" stroke-linejoin="miter">
                        <path d="M12 22s8-7 8-13a8 8 0 10-16 0c0 6 8 13 8 13z"/>
                        <circle cx="12" cy="9" r="3"/>
                    </svg>
                    Ver ubicaciones
                </a>
            </div>
            <a href="tel:62134776" class="cta__phone">
                ¿Prefieres llamar? <span>62134776</span>
            </a>
        </div>
    </div>
</section>

{{-- TESTIMONIOS --}}
<section class="section section--dark" id="testimonios">
    <div class="container">
        <div class="section__head section__head--dark" data-reveal="up">
            <div>
                <p class="section__label">Clientes</p>
                <h2 class="section__title section__title--light">Lo que dicen</h2>
            </div>
        </div>
        @php
            $testimonios = [
                ['texto' => 'Dejé mi auto para una reparación completa y pude ver el avance desde casa. El trabajo quedó impecable y en el tiempo prometido.', 'nombre' => 'María G.', 'rol' => 'Cliente frecuente'],
                ['texto' => 'El diagnóstico computarizado salvó mi motor. Me explicaron cada detalle antes de hacer cualquier cosa. Total confianza.', 'nombre' => 'Pedro R.', 'rol' => 'Cliente desde 2022'],
                ['texto' => 'Cambio de frenos y suspensión en un día. El sistema de seguimiento me mantuvo tranquilo todo el proceso.', 'nombre' => 'Lucía M.', 'rol' => 'Cliente frecuente'],
            ];
        @endphp
        <div class="testimonials" data-reveal="up">
            @foreach ($testimonios as $t)
                <div class="testimonial">
                    <div class="testimonial__stars">
                        @for ($j = 0; $j < 5; $j++)<svg width="14" height="14" viewBox="0 0 14 14"><path d="M7 0l2.2 4.4 4.8.7-3.5 3.4.8 4.7L7 11l-4.3 2.2.8-4.7L0 5.1l4.8-.7L7 0z" fill="#fbbf24"/></svg>@endfor
                    </div>
                    <blockquote>"{{ $t['texto'] }}"</blockquote>
                    <cite>
                        <img src="{{ 'https://ui-avatars.com/api/?name='.urlencode($t['nombre']).'&background=fff&color=1A1A1A&size=42' }}" alt="{{ $t['nombre'] }}" loading="lazy">
                        <div>
                            <span>{{ $t['nombre'] }}</span>
                            <small>{{ $t['rol'] }}</small>
                        </div>
                    </cite>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- COMPROMISO (fondo rojo con imagen) --}}
<section class="section compromiso" id="info">
    <div class="compromiso__bg" aria-hidden="true"></div>
    <div class="compromiso__overlay" aria-hidden="true"></div>
    <div class="container compromiso__container">
        <div class="compromiso__head" data-reveal="up">
            <p class="section__label section__label--light">Por qué elegirnos</p>
            <h2 class="compromiso__title">Tu auto merece<br>un servicio real</h2>
            <p class="compromiso__sub">No vendemos humo. Te explicamos qué tiene tu vehículo, cuánto va a costar y cuándo va a estar listo.</p>
        </div>
        <ol class="compromiso__grid">
            <li class="compromiso__item" data-reveal="up" data-reveal-delay="1">
                <span class="compromiso__num">01</span>
                <h3 class="compromiso__name">Diagnóstico transparente</h3>
                <p class="compromiso__text">Te explicamos qué tiene tu vehículo. Sin inventos, sin letra chica.</p>
            </li>
            <li class="compromiso__item" data-reveal="up" data-reveal-delay="2">
                <span class="compromiso__num">02</span>
                <h3 class="compromiso__name">Trabajo a tiempo</h3>
                <p class="compromiso__text">Asignamos un plazo y lo cumplimos. Si cambia, te avisamos antes.</p>
            </li>
            <li class="compromiso__item" data-reveal="up" data-reveal-delay="3">
                <span class="compromiso__num">03</span>
                <h3 class="compromiso__name">Seguimiento en línea</h3>
                <p class="compromiso__text">Ves el avance en tiempo real desde el portal del cliente.</p>
            </li>
            <li class="compromiso__item" data-reveal="up" data-reveal-delay="4">
                <span class="compromiso__num">04</span>
                <h3 class="compromiso__name">Garantía por escrito</h3>
                <p class="compromiso__text">Si algo falla dentro del plazo cubierto, lo reparamos sin costo.</p>
            </li>
        </ol>
    </div>
</section>

{{-- FAQ --}}
<section class="section section--light" id="faq">
    <div class="container">
        <div class="section__head" data-reveal="up">
            <div>
                <p class="section__label">FAQ</p>
                <h2 class="section__title">Preguntas<br>frecuentes</h2>
            </div>
            <p class="section__sub">Las respuestas a las preguntas más comunes de nuestros clientes.</p>
        </div>
        <div class="faq">
            @php
                $faqs = [
                    ['q' => '¿Cuánto tiempo toma una reparación?', 'r' => 'Depende del tipo de servicio. El diagnóstico inicial toma de 30 a 60 minutos. Las reparaciones menores se completan el mismo día. Te informamos el plazo estimado antes de comenzar.'],
                    ['q' => '¿Puedo seguir el progreso de mi vehículo?', 'r' => 'Sí. Como cliente registrado puedes ingresar al portal y ver el estado actual, el porcentaje de avance, las notas del mecánico y las evidencias fotográficas en tiempo real.'],
                    ['q' => '¿Ofrecen garantía?', 'r' => 'Todos nuestros servicios tienen garantía. La cobertura varía según el tipo de reparación. Te entregamos los detalles por escrito al finalizar el trabajo.'],
                    ['q' => '¿Cómo agendo una cita?', 'r' => 'Puedes agendar desde el portal del cliente haciendo clic en "Agendar cita" o llamándonos directamente a cualquiera de nuestras sucursales.'],
                    ['q' => '¿Aceptan pagos con tarjeta?', 'r' => 'Sí. Aceptamos tarjetas de crédito, débito, transferencias bancarias y pagos mediante código QR. También puedes pagar en efectivo en cualquiera de nuestras sucursales.'],
                ];
            @endphp
            @foreach ($faqs as $i => $faq)
                <div class="faq__item">
                    <button class="faq__q" data-faq="{{ $i }}" aria-expanded="false">
                        <span>{{ $faq['q'] }}</span>
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="square"/></svg>
                    </button>
                    <div class="faq__a" id="faq-{{ $i }}">
                        <p>{{ $faq['r'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CONTACTO --}}
<section class="section section--white" id="contacto">
    <div class="container">
        <div class="contact">
            <div class="contact__info" data-reveal="left">
                <p class="section__label">Contacto</p>
                <h2 class="section__title section__title--left">Escríbenos</h2>
                <div class="accent-line accent-line--left"></div>
                <p class="contact__lead">¿Tienes dudas o necesitas atención inmediata? Estamos disponibles.</p>
                @php
                    $contactos = [
                        ['icon' => '<svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M8 1C5.2 1 3 3.2 3 6c0 3.8 5 9 5 9s5-5.2 5-9c0-2.8-2.2-5-5-5z" stroke="currentColor" stroke-width="1.2"/><circle cx="8" cy="6" r="2" fill="currentColor"/></svg>', 'titulo' => 'Dirección', 'desc' => 'Av. Cristo Redentor #1250, Santa Cruz, Bolivia'],
                        ['icon' => '<svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M14.5 11.5l-3-1.5-2 2-4-4 2-2-1.5-3h-4C1.5 9 7 14.5 14.5 11.5z" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round"/></svg>', 'titulo' => 'Teléfono', 'desc' => '+591 3 345 6789'],
                        ['icon' => '<svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M1 3l7 5 7-5M1 3v10h14V3" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round"/></svg>', 'titulo' => 'Email', 'desc' => 'info@tallerpro.com'],
                        ['icon' => '<svg width="16" height="16" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.2"/><path d="M8 4.5V8l2.5 1.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>', 'titulo' => 'Horarios', 'desc' => 'Lun — Vie: 7:00 – 19:00 | Sáb: 7:00 – 14:00'],
                    ];
                @endphp
                <div class="contact__items">
                    @foreach ($contactos as $c)
                        <div class="contact__item">
                            <div class="contact__icon">{!! $c['icon'] !!}</div>
                            <div>
                                <h6>{{ $c['titulo'] }}</h6>
                                <p>{{ $c['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="contact__form-wrap" data-reveal="right">
                <form class="contact__form" onsubmit="event.preventDefault(); alert('Gracias por contactarnos. Te responderemos a la brevedad.');">
                    @csrf
                    <h3 class="contact__form-title">Envíanos un mensaje</h3>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="contact__label">Nombre completo</label>
                            <input type="text" class="contact__input" placeholder="Tu nombre" required>
                        </div>
                        <div class="col-md-6">
                            <label class="contact__label">Email</label>
                            <input type="email" class="contact__input" placeholder="correo@ejemplo.com" required>
                        </div>
                        <div class="col-12">
                            <label class="contact__label">Asunto</label>
                            <input type="text" class="contact__input" placeholder="¿Sobre qué nos escribes?">
                        </div>
                        <div class="col-12">
                            <label class="contact__label">Mensaje</label>
                            <textarea class="contact__input contact__input--area" rows="5" placeholder="Cuéntanos en qué podemos ayudarte" required></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn--primary">Enviar mensaje</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

{{-- FOOTER --}}
<footer class="footer">
    <div class="container">
        <div class="footer__grid">
            <div class="footer__brand">
                <img src="{{ asset('img/logo-modo-oscuro.png') }}" alt="Taller Pro" loading="lazy">
                <p>Taller automotriz profesional con seguimiento en tiempo real. Más de 10 años cuidando tu vehículo en Santa Cruz, Bolivia.</p>
            </div>
            <div class="footer__col">
                <h6>Navegación</h6>
                <ul>
                    <li><a href="#servicios">Servicios</a></li>
                    <li><a href="#como-funciona">Proceso</a></li>
                    <li><a href="#sucursales">Sucursales</a></li>
                    <li><a href="#contacto">Contacto</a></li>
                </ul>
            </div>
            <div class="footer__col">
                <h6>Cliente</h6>
                <ul>
                    <li><a href="{{ route('login') }}">Iniciar sesión</a></li>
                    <li><a href="{{ route('login') }}">Agendar cita</a></li>
                    <li><a href="#faq">Preguntas frecuentes</a></li>
                </ul>
            </div>
            <div class="footer__col">
                <h6>Acceso</h6>
                <ul>
                    <li><a href="{{ route('login') }}">Área administrativa</a></li>
                    <li><a href="{{ route('login') }}">Portal del mecánico</a></li>
                </ul>
                @if (isset($sucursales) && $sucursales->count() > 0)
                    <h6>Sucursales</h6>
                    <ul>
                        @foreach ($sucursales as $sucursal)
                            <li><a href="#sucursales">{{ $sucursal->nombre }}</a></li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
        <div class="footer__bottom">
            <p>&copy; {{ date('Y') }} Taller Pro. Todos los derechos reservados.</p>
            <p>Hecho en Santa Cruz, Bolivia</p>
        </div>
    </div>
</footer>

{{-- BACK TO TOP --}}
<button class="top" id="topBtn" aria-label="Volver arriba">
    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M4 10l4-4 4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="square"/></svg>
</button>

</body>
</html>
