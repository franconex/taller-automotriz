<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Taller Pro: mantenimiento, diagnóstico y reparación automotriz profesional en Santa Cruz de la Sierra.">
    <meta name="theme-color" content="#0b0c0e">
    <title>Taller Pro | Tu vehículo en manos de expertos</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="/css/inicio.css">
</head>
<body class="font-manrope text-[#101114] bg-white antialiased">
<a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-[9999] focus:bg-brand-red focus:text-white focus:px-4 focus:py-2 focus:rounded-lg focus:outline-none">Saltar al contenido principal</a>
<header class="fixed top-0 left-0 w-full h-[70px] bg-white shadow-md z-50 transition-shadow duration-300" id="navbar">
    <nav class="max-w-[1160px] mx-auto px-5 h-full flex items-center justify-between" aria-label="Navegación principal">
        <a href="#inicio" class="flex items-center gap-3 text-[#101114] no-underline" aria-label="Taller Pro, inicio">
            <img src="/img/logo.png" alt="Logo Taller Pro" class="w-12 h-12 object-contain">
            <strong class="text-lg tracking-wide font-extrabold">TALLER <span class="text-brand-red">PRO</span></strong>
        </a>
        <button class="menu-toggle lg:hidden flex-col items-center justify-center bg-none border-0 w-[42px] h-[42px] p-2 cursor-pointer focus-visible:outline-2 focus-visible:outline-brand-red focus-visible:outline-offset-2 rounded" type="button" aria-label="Abrir menú" aria-expanded="false" aria-controls="nav-menu">
            <span class="block h-[2px] w-5 bg-gray-600 rounded transition-transform duration-200 my-[3px]"></span>
            <span class="block h-[2px] w-5 bg-gray-600 rounded transition-opacity duration-200 my-[3px]"></span>
            <span class="block h-[2px] w-5 bg-gray-600 rounded transition-transform duration-200 my-[3px]"></span>
        </button>
        <div class="nav-menu flex items-center gap-5 lg:flex lg:items-center lg:gap-5" id="nav-menu">
            <a href="#inicio" class="text-sm font-bold text-gray-600 no-underline relative after:content-[''] after:absolute after:left-0 after:-bottom-1 after:w-0 after:h-[2px] after:bg-brand-red after:transition-all after:duration-200 hover:after:w-full hover:text-brand-red active:text-brand-red">Inicio</a>
            <a href="#servicios" class="text-sm font-bold text-gray-600 no-underline relative after:content-[''] after:absolute after:left-0 after:-bottom-1 after:w-0 after:h-[2px] after:bg-brand-red after:transition-all after:duration-200 hover:after:w-full hover:text-brand-red">Servicios</a>
            <a href="#nosotros" class="text-sm font-bold text-gray-600 no-underline relative after:content-[''] after:absolute after:left-0 after:-bottom-1 after:w-0 after:h-[2px] after:bg-brand-red after:transition-all after:duration-200 hover:after:w-full hover:text-brand-red">Nosotros</a>
            <a href="#contacto" class="text-sm font-bold text-gray-600 no-underline relative after:content-[''] after:absolute after:left-0 after:-bottom-1 after:w-0 after:h-[2px] after:bg-brand-red after:transition-all after:duration-200 hover:after:w-full hover:text-brand-red">Contacto</a>
            <a href="#ubicacion" class="text-sm font-bold text-gray-600 no-underline relative after:content-[''] after:absolute after:left-0 after:-bottom-1 after:w-0 after:h-[2px] after:bg-brand-red after:transition-all after:duration-200 hover:after:w-full hover:text-brand-red">Ubicación</a>
            <a href="#cita" class="inline-flex items-center gap-2 bg-brand-red text-white px-4 py-2.5 rounded-lg text-xs font-bold no-underline shadow-md shadow-brand-red/20 hover:bg-brand-red-dark transition-all duration-200"><i class="fa-regular fa-calendar-check"></i> Agendar cita</a>
            @if (Route::has('login'))
                @auth <a href="{{ url('/dashboard') }}" class="text-sm font-bold text-gray-600 no-underline hover:text-brand-red transition-colors"><i class="fa-regular fa-user"></i> Mi panel</a>
                @else <a href="{{ route('login') }}" class="text-sm font-bold text-gray-600 no-underline hover:text-brand-red transition-colors"><i class="fa-regular fa-user"></i> Ingresar</a> @endauth
            @endif
        </div>
    </nav>
</header>

<main id="main-content">
    <section class="hero relative min-h-[560px] lg:h-[75vh] max-h-[700px] flex items-center text-white overflow-hidden bg-black pt-[70px]" id="inicio">
        <div class="hero-bg absolute inset-0 bg-cover bg-center" style="background-image:url('/img/portada-talle.png')"></div>
        <div class="max-w-[1160px] mx-auto px-5 relative z-10 w-full">
            <div class="max-w-[640px] reveal">
                <span class="inline-flex items-center gap-2 text-gray-300 text-xs font-bold uppercase tracking-widest mb-4"><i class="fa-solid fa-circle-check text-brand-red"></i> Servicio automotriz de confianza</span>
                <h1 class="text-[clamp(2.6rem,5vw,4.8rem)] leading-[1.04] -tracking-[0.05em] max-w-[680px] font-extrabold">Tu vehículo en manos de <em class="not-italic text-brand-red">expertos</em></h1>
                <p class="text-base text-gray-300 max-w-[560px] mt-5 mb-7">Diagnóstico preciso, atención honesta y soluciones que mantienen tu vehículo seguro en cada recorrido.</p>
                <div class="flex gap-2.5 flex-wrap">
                    <a href="#cita" class="inline-flex items-center gap-2.5 bg-brand-red text-white px-6 py-3.5 rounded-lg font-extrabold no-underline shadow-lg shadow-brand-red/25 hover:bg-brand-red-dark transition-all duration-200 hover:-translate-y-0.5"><i class="fa-regular fa-calendar-check"></i> Agendar una cita</a>
                    <a href="https://wa.me/59162134776?text=Hola%2C%20quiero%20información%20sobre%20los%20servicios%20de%20Taller%20Pro." target="_blank" rel="noopener" class="inline-flex items-center gap-2.5 text-white border border-white/45 bg-white/5 backdrop-blur-sm px-6 py-3.5 rounded-lg font-extrabold no-underline transition-all duration-200 hover:bg-white hover:text-gray-900 hover:-translate-y-0.5"><i class="fa-brands fa-whatsapp"></i> Contactar ahora</a>
                </div>
                <div class="flex gap-7 border-t border-white/15 mt-8 pt-4">
                    <span class="text-gray-400 text-[0.7rem] uppercase tracking-wide"><b class="block text-white text-base">+500</b> vehículos atendidos</span>
                    <span class="text-gray-400 text-[0.7rem] uppercase tracking-wide"><b class="block text-white text-base">6</b> servicios especializados</span>
                    <span class="text-gray-400 text-[0.7rem] uppercase tracking-wide"><b class="block text-white text-base">100%</b> atención personalizada</span>
                </div>
            </div>
        </div>
        <a href="#servicios" class="scroll-down absolute z-10 bottom-5 left-1/2 -translate-x-1/2 w-[38px] h-[38px] border border-white/30 rounded-full grid place-items-center no-underline text-white" aria-label="Ver servicios"><i class="fa-solid fa-chevron-down"></i></a>
    </section>

    <section class="relative z-10 -mt-11 pb-0 reveal">
        <div class="max-w-[1160px] mx-auto px-5">
            <div class="bg-white rounded-[18px] shadow-lg grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 p-5 sm:p-7">
                <div class="flex items-center gap-3.5 p-2 sm:p-3.5 border-0 sm:border-r border-gray-200 lg:border-r">
                    <div class="w-12 h-12 flex-none grid place-items-center bg-red-50 rounded-full text-brand-red text-lg transition-colors duration-200 hover:bg-red-100"><i class="fa-solid fa-shield-halved"></i></div>
                    <div><h4 class="text-sm font-extrabold text-[#101114] mb-0.5">Profesionales calificados</h4><p class="text-xs text-[#66707b] leading-relaxed m-0">Técnicos especializados a tu servicio.</p></div>
                </div>
                <div class="flex items-center gap-3.5 p-2 sm:p-3.5 border-0 sm:border-r border-gray-200 lg:border-r">
                    <div class="w-12 h-12 flex-none grid place-items-center bg-red-50 rounded-full text-brand-red text-lg transition-colors duration-200 hover:bg-red-100"><i class="fa-solid fa-toolbox"></i></div>
                    <div><h4 class="text-sm font-extrabold text-[#101114] mb-0.5">Equipos de última tecnología</h4><p class="text-xs text-[#66707b] leading-relaxed m-0">Diagnósticos precisos y reparaciones eficientes.</p></div>
                </div>
                <div class="flex items-center gap-3.5 p-2 sm:p-3.5 border-0 sm:border-0 lg:border-r border-gray-200">
                    <div class="w-12 h-12 flex-none grid place-items-center bg-red-50 rounded-full text-brand-red text-lg transition-colors duration-200 hover:bg-red-100"><i class="fa-solid fa-certificate"></i></div>
                    <div><h4 class="text-sm font-extrabold text-[#101114] mb-0.5">Garantía en todos nuestros servicios</h4><p class="text-xs text-[#66707b] leading-relaxed m-0">Tu seguridad y satisfacción son nuestra prioridad.</p></div>
                </div>
                <div class="flex items-center gap-3.5 p-2 sm:p-3.5">
                    <div class="w-12 h-12 flex-none grid place-items-center bg-red-50 rounded-full text-brand-red text-lg transition-colors duration-200 hover:bg-red-100"><i class="fa-solid fa-clock"></i></div>
                    <div><h4 class="text-sm font-extrabold text-[#101114] mb-0.5">Atención rápida y confiable</h4><p class="text-xs text-[#66707b] leading-relaxed m-0">Agenda tu cita y olvídate de las filas.</p></div>
                </div>
            </div>
        </div>
    </section>

    @php
        $servicios = [
            ['Mantenimiento preventivo','Revisamos los componentes esenciales para prevenir fallas y prolongar la vida útil de tu vehículo.','servicio-mantenimiento-preventivo.png','fa-shield-halved'],
            ['Diagnóstico computarizado','Detectamos fallas electrónicas y mecánicas con herramientas modernas y resultados precisos.','servicio-diagnostico-computarizado.png','fa-laptop-code'],
            ['Mecánica general','Resolvemos problemas del motor y otros sistemas mecánicos con atención especializada.','servicio-mecanica-general.png','fa-gears'],
            ['Frenos y suspensión','Revisamos los sistemas que garantizan estabilidad, control y seguridad en cada trayecto.','servicio-frenos-suspension.png','fa-car-side'],
            ['Electricidad automotriz','Reparamos baterías, cableado, luces, alternadores y componentes eléctricos.','servicio-electricidad-automotriz.png','fa-bolt'],
            ['Cambio de aceite','Cambiamos aceite y filtros para proteger el motor y conservar su rendimiento.','servicio-cambio-aceite.png','fa-oil-can']
        ];
    @endphp

    <section class="py-16 bg-[#f5f6f8]" id="servicios">
        <div class="max-w-[1160px] mx-auto px-5">
            <header class="text-center max-w-[700px] mx-auto mb-10 reveal">
                <span class="inline-block text-brand-red text-xs font-extrabold tracking-[0.18em] mb-3.5">LO QUE HACEMOS</span>
                <h2 class="text-[clamp(1.8rem,3.5vw,2.8rem)] leading-[1.15] -tracking-[0.04em] font-extrabold">Servicios para cuidar tu vehículo</h2>
                <p class="text-[#66707b] mt-3 text-sm">Equipos modernos, personal capacitado y soluciones claras para que vuelvas a la ruta con tranquilidad.</p>
            </header>
            <div class="services-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($servicios as $servicio)
                <article class="service-card bg-white border border-gray-200 rounded-[18px] overflow-hidden transition-all duration-200 hover:-translate-y-1 hover:shadow-lg hover:border-transparent reveal">
                    <div class="service-image h-48 relative overflow-hidden">
                        <img src="/img/{{ $servicio[2] }}" alt="{{ $servicio[0] }}" loading="lazy" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                        <span class="service-icon"><i class="fa-solid {{ $servicio[3] }}"></i></span>
                    </div>
                    <div class="p-5 sm:p-6">
                        <h3 class="text-lg font-bold mb-1.5">{{ $servicio[0] }}</h3>
                        <p class="text-[#66707b] text-sm leading-relaxed">{{ $servicio[1] }}</p>
                        <a href="#cita" class="inline-flex items-center gap-2 mt-3.5 text-brand-red font-extrabold text-xs no-underline transition-all duration-200 hover:gap-3" data-service="{{ $servicio[0] }}">Solicitar servicio <i class="fa-solid fa-arrow-right text-xs transition-transform duration-200 group-hover:translate-x-1"></i></a>
                    </div>
                </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section py-16" id="nosotros">
        <div class="max-w-[1160px] mx-auto px-5 grid grid-cols-1 lg:grid-cols-[1.05fr_0.95fr] items-center gap-12 lg:gap-16">
            <div class="relative reveal">
                <img src="/img/taller-equipo.png" alt="Mecánicos de Taller Pro trabajando" loading="lazy" class="w-full h-[520px] object-cover rounded-[18px] transition-transform duration-300 hover:scale-[1.02]">
                <div class="absolute left-0 top-6 w-[6px] h-[130px] bg-brand-red rounded-r-lg"></div>
                <div class="absolute -right-5 bottom-7 bg-gray-900 text-white border-l-4 border-brand-red p-4 pl-5.5 shadow-lg flex items-center gap-2.5">
                    <strong class="text-[1.8rem] font-extrabold">+500</strong>
                    <span class="text-[0.7rem] text-gray-300 uppercase tracking-wide">vehículos<br>atendidos</span>
                </div>
            </div>
            <div class="reveal">
                <span class="inline-block text-brand-red text-xs font-extrabold tracking-[0.18em] mb-3.5">QUIÉNES SOMOS</span>
                <h2 class="text-[clamp(1.8rem,3.5vw,2.8rem)] leading-[1.15] -tracking-[0.04em] font-extrabold">Experiencia, confianza y compromiso</h2>
                <p class="text-[#66707b] mt-5 mb-6">Sabemos que dejar tu vehículo en un taller exige confianza. Por eso te explicamos cada diagnóstico con claridad, trabajamos con cuidado y proponemos únicamente lo que tu vehículo necesita.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex gap-3 items-start"><i class="flex-none w-9 h-9 bg-red-50 text-brand-red rounded-lg grid place-items-center transition-colors duration-200 hover:bg-red-100 fa-solid fa-user-gear"></i><span class="flex flex-col"><b class="text-sm">Mecánicos capacitados</b><small class="text-[#66707b] text-xs">Experiencia aplicada a cada reparación.</small></span></div>
                    <div class="flex gap-3 items-start"><i class="flex-none w-9 h-9 bg-red-50 text-brand-red rounded-lg grid place-items-center transition-colors duration-200 hover:bg-red-100 fa-solid fa-microchip"></i><span class="flex flex-col"><b class="text-sm">Diagnóstico profesional</b><small class="text-[#66707b] text-xs">Tecnología para encontrar la causa real.</small></span></div>
                    <div class="flex gap-3 items-start"><i class="flex-none w-9 h-9 bg-red-50 text-brand-red rounded-lg grid place-items-center transition-colors duration-200 hover:bg-red-100 fa-solid fa-comments"></i><span class="flex flex-col"><b class="text-sm">Atención personalizada</b><small class="text-[#66707b] text-xs">Te mantenemos informado en cada etapa.</small></span></div>
                    <div class="flex gap-3 items-start"><i class="flex-none w-9 h-9 bg-red-50 text-brand-red rounded-lg grid place-items-center transition-colors duration-200 hover:bg-red-100 fa-solid fa-shield-halved"></i><span class="flex flex-col"><b class="text-sm">Seguridad primero</b><small class="text-[#66707b] text-xs">Revisiones responsables y detalladas.</small></span></div>
                </div>
                <a href="#contacto" class="inline-flex items-center gap-2 mt-3.5 text-brand-red font-extrabold text-xs no-underline transition-all duration-200 hover:gap-3">Conoce cómo podemos ayudarte <i class="fa-solid fa-arrow-right text-xs"></i></a>
            </div>
        </div>
    </section>

    <section class="cta-strip relative py-14 bg-gradient-to-r from-brand-red-dark to-brand-red text-white overflow-hidden">
        <div class="max-w-[1160px] mx-auto px-5 relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
            <div class="reveal">
                <span class="inline-block text-white text-xs font-extrabold tracking-[0.18em] mb-3.5">NO LO DEJES PARA DESPUÉS</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold">¿Tu vehículo necesita una revisión?</h2>
                <p class="text-red-100 mt-1">Una pequeña señal hoy puede evitar una reparación costosa mañana.</p>
            </div>
            <a href="https://wa.me/59162134776?text=Hola%2C%20quiero%20agendar%20una%20revisión%20para%20mi%20vehículo." target="_blank" rel="noopener" class="inline-flex items-center gap-2.5 bg-white text-gray-900 px-6 py-3.5 rounded-lg font-extrabold no-underline border border-white/20 hover:bg-white/90 transition-all duration-200 shrink-0"><i class="fa-brands fa-whatsapp"></i> Hablar por WhatsApp</a>
        </div>
    </section>

    <section class="section py-16 bg-[#0c0d10] text-white" id="cita">
        <div class="max-w-[1160px] mx-auto px-5 grid grid-cols-1 lg:grid-cols-[0.8fr_1.2fr] gap-12 lg:gap-16 items-center">
            <div class="reveal">
                <span class="inline-block text-brand-red text-xs font-extrabold tracking-[0.18em] mb-3.5">AGENDA TU VISITA</span>
                <h2 class="text-[clamp(1.8rem,3.5vw,2.8rem)] leading-[1.15] -tracking-[0.04em] font-extrabold">Cuéntanos qué necesita tu vehículo</h2>
                <p class="text-gray-400 mt-5 mb-4">Completa el formulario y prepararemos un mensaje de WhatsApp con todos tus datos. Así podremos responderte y confirmar la disponibilidad más rápido.</p>
                <div class="grid gap-2.5 text-gray-300 text-sm">
                    <span><i class="fa-solid fa-check text-brand-red mr-2"></i> Respuesta directa por WhatsApp</span>
                    <span><i class="fa-solid fa-check text-brand-red mr-2"></i> Sin pagos ni compromisos</span>
                    <span><i class="fa-solid fa-check text-brand-red mr-2"></i> Confirmación antes de tu visita</span>
                </div>
                <div class="flex items-center gap-3 border-t border-gray-800 mt-6 pt-5">
                    <i class="w-11 h-11 rounded-full bg-brand-red grid place-items-center text-white"></i>
                    <span class="flex flex-col text-gray-400 text-xs">¿Prefieres llamar?<small class="text-base text-white font-extrabold">+591 62134776</small></span>
                </div>
            </div>
            <div class="form-card bg-white text-[#101114] p-8 rounded-[18px] shadow-2xl reveal">
                <form id="cita-form" class="grid gap-3.5" novalidate>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <label class="grid gap-1.5">Nombre completo *<input type="text" id="nombre" required placeholder="Ej. Juan Pérez" class="w-full border border-gray-300 bg-gray-50 rounded-lg p-3 outline-none transition-all duration-200 focus:border-brand-red focus:ring-2 focus:ring-brand-red/20 focus:bg-white"></label>
                        <label class="grid gap-1.5">Teléfono *<input type="tel" id="telefono" required inputmode="numeric" placeholder="Ej. 70000000" class="w-full border border-gray-300 bg-gray-50 rounded-lg p-3 outline-none transition-all duration-200 focus:border-brand-red focus:ring-2 focus:ring-brand-red/20 focus:bg-white"></label>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <label class="grid gap-1.5">Vehículo *<input type="text" id="vehiculo" required placeholder="Ej. Toyota Corolla 2020" class="w-full border border-gray-300 bg-gray-50 rounded-lg p-3 outline-none transition-all duration-200 focus:border-brand-red focus:ring-2 focus:ring-brand-red/20 focus:bg-white"></label>
                        <label class="grid gap-1.5">Fecha preferida *<input type="date" id="fecha" required class="w-full border border-gray-300 bg-gray-50 rounded-lg p-3 outline-none transition-all duration-200 focus:border-brand-red focus:ring-2 focus:ring-brand-red/20 focus:bg-white"></label>
                    </div>
                    <label class="grid gap-1.5">Servicio solicitado *<select id="servicio" required class="w-full border border-gray-300 bg-gray-50 rounded-lg p-3 outline-none transition-all duration-200 focus:border-brand-red focus:ring-2 focus:ring-brand-red/20 focus:bg-white"><option value="">Selecciona un servicio</option>@foreach($servicios as $servicio)<option value="{{ $servicio[0] }}">{{ $servicio[0] }}</option>@endforeach</select></label>
                    <label class="grid gap-1.5">Cuéntanos brevemente el problema<textarea id="mensaje" rows="4" placeholder="Ej. Escucho un ruido al frenar..." class="w-full border border-gray-300 bg-gray-50 rounded-lg p-3 outline-none transition-all duration-200 focus:border-brand-red focus:ring-2 focus:ring-brand-red/20 focus:bg-white resize-y"></textarea></label>
                    <label class="grid grid-cols-[auto_1fr] items-start gap-2.5 font-medium text-[#66707b] text-xs"><input type="checkbox" id="acepto" required class="w-4 h-4 accent-brand-red mt-0.5"><span>Acepto que Taller Pro utilice estos datos para gestionar mi solicitud.</span></label>
                    <p class="text-brand-red text-xs font-bold min-h-[20px]" id="form-feedback" role="alert"></p>
                    <button type="submit" class="btn btn-red bg-brand-red text-white w-full py-3.5 rounded-lg font-extrabold font-sans text-sm shadow-lg shadow-brand-red/25 hover:bg-brand-red-dark transition-all duration-200 border-0 cursor-pointer inline-flex items-center justify-center gap-2.5"><i class="fa-brands fa-whatsapp"></i> Enviar solicitud por WhatsApp</button>
                </form>
            </div>
        </div>
    </section>

    <section class="section py-16 bg-[#f5f6f8]" id="contacto">
        <div class="max-w-[1160px] mx-auto px-5">
            <header class="text-center max-w-[700px] mx-auto mb-10 reveal">
                <span class="inline-block text-brand-red text-xs font-extrabold tracking-[0.18em] mb-3.5">ESTAMOS PARA AYUDARTE</span>
                <h2 class="text-[clamp(1.8rem,3.5vw,2.8rem)] leading-[1.15] -tracking-[0.04em] font-extrabold">Hablemos de tu vehículo</h2>
                <p class="text-[#66707b] mt-3 text-sm">Elige el canal que te resulte más cómodo. Te atenderemos con gusto.</p>
            </header>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <a href="tel:+59162134776" class="contact-card bg-white p-5 rounded-xl border border-gray-200 flex items-center gap-3.5 no-underline transition-all duration-200 hover:-translate-y-1 hover:shadow-lg hover:border-transparent reveal">
                    <i class="w-[45px] h-[45px] flex-none bg-red-50 text-brand-red rounded-xl grid place-items-center text-base transition-colors duration-200 group-hover:bg-red-100 fa-solid fa-phone"></i>
                    <span class="flex flex-col flex-1"><small class="text-[#66707b] text-[0.62rem] tracking-widest">LLÁMANOS</small><b class="text-sm text-[#101114]">+591 62134776</b></span>
                    <i class="fa-solid fa-arrow-right text-gray-400 text-xs"></i>
                </a>
                <a href="https://wa.me/59162134776" target="_blank" rel="noopener" class="contact-card bg-white p-5 rounded-xl border border-gray-200 flex items-center gap-3.5 no-underline transition-all duration-200 hover:-translate-y-1 hover:shadow-lg hover:border-transparent reveal">
                    <i class="w-[45px] h-[45px] flex-none bg-red-50 text-brand-red rounded-xl grid place-items-center text-base transition-colors duration-200 group-hover:bg-red-100 fa-brands fa-whatsapp"></i>
                    <span class="flex flex-col flex-1"><small class="text-[#66707b] text-[0.62rem] tracking-widest">ESCRÍBENOS</small><b class="text-sm text-[#101114]">WhatsApp directo</b></span>
                    <i class="fa-solid fa-arrow-right text-gray-400 text-xs"></i>
                </a>
                <div class="contact-card bg-white p-5 rounded-xl border border-gray-200 flex items-center gap-3.5 transition-all duration-200 hover:-translate-y-1 hover:shadow-lg hover:border-transparent reveal">
                    <i class="w-[45px] h-[45px] flex-none bg-red-50 text-brand-red rounded-xl grid place-items-center text-base transition-colors duration-200 group-hover:bg-red-100 fa-regular fa-clock"></i>
                    <span class="flex flex-col flex-1"><small class="text-[#66707b] text-[0.62rem] tracking-widest">HORARIO</small><b class="text-sm text-[#101114]">Lun–Sáb · 08:00–18:00</b></span>
                </div>
            </div>
        </div>
    </section>

    <section class="location" id="ubicacion">
        <div class="grid grid-cols-1 lg:grid-cols-[0.7fr_1.3fr]">
            <div class="p-12 lg:p-16 lg:pr-12 self-center reveal max-w-[1160px] lg:ml-auto lg:pl-[max(40px,calc((100vw-1160px)/2))]">
                <span class="inline-block text-brand-red text-xs font-extrabold tracking-[0.18em] mb-3.5">NUESTRA UBICACIÓN</span>
                <h2 class="text-[clamp(1.8rem,3.5vw,2.8rem)] leading-[1.15] -tracking-[0.04em] font-extrabold">Encuéntranos fácilmente</h2>
                <p class="text-[#66707b] mt-4 mb-5 max-w-[480px]">Visítanos en Santa Cruz de la Sierra. Abre la ubicación y permite que Google Maps te muestre la mejor ruta desde donde estés.</p>
                <a href="https://www.google.com/maps?q=-17.847000093051598,-63.16330684196508" target="_blank" rel="noopener" class="inline-flex items-center gap-2.5 bg-brand-red text-white px-6 py-3.5 rounded-lg font-extrabold no-underline shadow-lg shadow-brand-red/25 hover:bg-brand-red-dark transition-all duration-200"><i class="fa-solid fa-route"></i> Cómo llegar</a>
            </div>
            <div class="min-h-[450px] overflow-hidden">
                <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d237.36384465394175!2d-63.16330684196508!3d-17.847000093051598!2m3!1f0!2f0!3f0!3m2!1i1024!1i768!4f13.1!5e0!3m2!1ses-419!2sbo!4v1784400272404!5m2!1ses-419!2sbo" allowfullscreen loading="lazy" referrerpolicy="strict-origin-when-cross-origin" title="Ubicación Taller Pro" class="w-full h-full min-h-[450px] border-0 grayscale-[25%] transition-all duration-300 hover:grayscale-0"></iframe>
            </div>
        </div>
    </section>
</main>

<a href="https://wa.me/59162134776?text=Hola%2C%20quiero%20información%20sobre%20Taller%20Pro." target="_blank" rel="noopener" class="whatsapp-float flex items-center gap-2.5 bg-[#25d366] text-white pl-3 pr-5 py-3 rounded-full shadow-2xl text-sm font-bold no-underline transition-all duration-200 hover:-translate-y-1 hover:shadow-[0_20px_50px_rgba(37,211,102,0.35)]" aria-label="Contactar por WhatsApp"><i class="fa-brands fa-whatsapp w-9 h-9 bg-white text-[#25d366] rounded-full grid place-items-center text-xl"></i><span>¿Necesitas ayuda?</span></a>

<footer class="footer bg-gray-950 text-gray-400 py-14 pb-6">
    <div class="max-w-[1160px] mx-auto px-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[2fr_1fr_1fr] gap-12 lg:gap-16">
        <div class="footer-brand">
            <a href="#inicio" class="flex items-center gap-3 text-white no-underline"><img src="/img/logo.png" alt="Logo Taller Pro" class="w-12 h-12 object-contain"><strong class="text-lg tracking-wide font-extrabold">TALLER <span class="text-brand-red">PRO</span></strong></a>
            <p class="text-xs mt-4 max-w-[400px]">Servicio automotriz profesional, claro y confiable para cuidar lo que te mueve.</p>
        </div>
        <div class="flex flex-col gap-2 text-xs">
            <h3 class="text-white text-sm font-bold mb-2">Explora</h3>
            <a href="#servicios" class="text-gray-400 no-underline hover:text-white transition-colors">Servicios</a>
            <a href="#nosotros" class="text-gray-400 no-underline hover:text-white transition-colors">Nosotros</a>
            <a href="#cita" class="text-gray-400 no-underline hover:text-white transition-colors">Agendar cita</a>
            <a href="#ubicacion" class="text-gray-400 no-underline hover:text-white transition-colors">Ubicación</a>
        </div>
        <div class="flex flex-col gap-2 text-xs">
            <h3 class="text-white text-sm font-bold mb-2">Contacto</h3>
            <a href="tel:+59162134776" class="text-gray-400 no-underline hover:text-white transition-colors">+591 62134776</a>
            <span class="text-gray-400">Lunes a sábado</span>
            <span class="text-gray-400">08:00–18:00</span>
            <span class="text-gray-400">Santa Cruz, Bolivia</span>
        </div>
    </div>
    <div class="max-w-[1160px] mx-auto px-5 border-t border-gray-800 mt-10 pt-5 flex flex-col sm:flex-row justify-between gap-3 text-[0.7rem]">
        <span>© {{ date('Y') }} Taller Pro. Todos los derechos reservados.</span>
        <a href="#inicio" class="text-gray-400 no-underline hover:text-white transition-colors">Volver arriba <i class="fa-solid fa-arrow-up"></i></a>
    </div>
</footer>

<script src="/js/inicio.js"></script>
</body>
</html>