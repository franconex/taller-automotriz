document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.querySelector('.menu-toggle');
    const menu = document.querySelector('.nav-menu');
    const navLinks = document.querySelectorAll('.nav-menu a[href^="#"]');
    const navbar = document.querySelector('.navbar');

    const updateNavbar = () => navbar?.classList.toggle('scrolled', window.scrollY > 30);
    updateNavbar();
    window.addEventListener('scroll', updateNavbar, { passive: true });

    toggle?.addEventListener('click', () => {
        const opened = menu.classList.toggle('open');
        toggle.setAttribute('aria-expanded', opened);
        document.body.classList.toggle('menu-open', opened);
    });
    navLinks.forEach(link => link.addEventListener('click', () => {
        menu.classList.remove('open');
        toggle?.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('menu-open');
    }));

    const staggerGroups = [
        { selector: '.services-grid .service-card', base: 0, step: 80 },
        { selector: '.trust-bar-grid .trust-item', base: 0, step: 80 },
        { selector: '.contact-grid .contact-card', base: 0, step: 80 },
    ];

    const revealObserver = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                revealObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });

    document.querySelectorAll('.reveal').forEach(item => {
        let delay = 0;
        for (const group of staggerGroups) {
            const parent = item.closest(group.selector.split(' ')[0]);
            if (parent) {
                const siblings = [...parent.querySelectorAll(group.selector.split(' ').slice(1).join(' '))];
                const index = siblings.indexOf(item);
                if (index !== -1) {
                    delay = group.base + index * group.step;
                    break;
                }
            }
        }
        if (delay > 0) item.style.transitionDelay = delay + 'ms';
        revealObserver.observe(item);
    });

    const sections = document.querySelectorAll('main section[id]');
    const sectionObserver = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            navLinks.forEach(link => link.classList.toggle('active', link.getAttribute('href') === `#${entry.target.id}`));
        });
    }, { rootMargin: '-35% 0px -55% 0px' });
    sections.forEach(section => sectionObserver.observe(section));

    const serviceSelect = document.getElementById('servicio');
    document.querySelectorAll('[data-service]').forEach(link => link.addEventListener('click', () => {
        if (serviceSelect) serviceSelect.value = link.dataset.service;
    }));

    const dateInput = document.getElementById('fecha');
    if (dateInput) dateInput.min = new Date().toISOString().split('T')[0];

    const form = document.getElementById('cita-form');
    const feedback = document.getElementById('form-feedback');
    form?.addEventListener('submit', event => {
        event.preventDefault();
        const required = [...form.querySelectorAll('[required]')];
        required.forEach(field => field.classList.remove('invalid'));
        const invalid = required.filter(field => !field.checkValidity());
        const phone = document.getElementById('telefono');
        if (phone && !/^\d{7,10}$/.test(phone.value.trim())) invalid.push(phone);
        if (invalid.length) {
            [...new Set(invalid)].forEach(field => field.classList.add('invalid'));
            feedback.textContent = 'Revisa los campos marcados antes de continuar.';
            invalid[0].focus();
            return;
        }
        feedback.textContent = '';
        const message = [
            'Hola, quiero solicitar una cita en Taller Pro.',
            '',
            `Nombre: ${document.getElementById('nombre').value.trim()}`,
            `Teléfono: ${phone.value.trim()}`,
            `Vehículo: ${document.getElementById('vehiculo').value.trim()}`,
            `Servicio: ${serviceSelect.value}`,
            `Fecha preferida: ${dateInput.value}`,
            `Detalle: ${document.getElementById('mensaje').value.trim() || 'Sin detalle adicional'}`
        ].join('\n');
        window.open(`https://wa.me/59162134776?text=${encodeURIComponent(message)}`, '_blank', 'noopener');
    });
});
