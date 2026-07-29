import './bootstrap';
import * as bootstrap from 'bootstrap';

window.bootstrap = bootstrap;

// ─── NAVBAR DINÁMICA: cambia al hacer scroll, resalta sección activa ───
const nav = document.getElementById('nav');
const topBtn = document.getElementById('topBtn');
const navToggle = document.getElementById('navToggle');
const navMobile = document.getElementById('navMobile');
const navLinks = document.querySelectorAll('[data-link]');

let lastScroll = 0;
let ticking = false;

function onScroll() {
  if (!ticking) {
    window.requestAnimationFrame(() => {
      const y = window.scrollY;
      // Navbar cambia cuando se desplaza > 60px
      if (nav) nav.classList.toggle('scrolled', y > 60);
      // Top button
      if (topBtn) topBtn.classList.toggle('visible', y > 500);
      // Highlight sección activa
      highlightActiveSection();
      lastScroll = y;
      ticking = false;
    });
    ticking = true;
  }
}

function highlightActiveSection() {
  const sections = ['hero', 'servicios', 'como-funciona', 'sucursales', 'contacto'];
  const y = window.scrollY + 120;
  let current = 'hero';
  for (const id of sections) {
    const el = document.getElementById(id);
    if (el && el.offsetTop <= y) current = id;
  }
  document.querySelectorAll('[data-link]').forEach((link) => {
    link.classList.toggle('active', link.dataset.link === current);
  });
}

document.addEventListener('scroll', onScroll, { passive: true });

// Top button
if (topBtn) {
  topBtn.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
}

// Mobile toggle
if (navToggle && navMobile) {
  navToggle.addEventListener('click', () => {
    navMobile.classList.toggle('open');
  });
}

// Smooth scroll + cerrar menú móvil
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener('click', (e) => {
    const href = anchor.getAttribute('href');
    if (href === '#' || href.length < 2) return;
    const target = document.querySelector(href);
    if (target) {
      e.preventDefault();
      const top = target.getBoundingClientRect().top + window.scrollY - 60;
      window.scrollTo({ top, behavior: 'smooth' });
      if (navMobile) navMobile.classList.remove('open');
    }
  });
});

// ─── SERVICE CARDS: hover desktop, click mobile ───
const services = document.querySelectorAll('.service');
let activeService = null;

services.forEach((card) => {
  card.addEventListener('click', (e) => {
    if (e.target.closest('.btn')) return;
    if (window.innerWidth <= 767) {
      if (activeService && activeService !== card) {
        activeService.classList.remove('expanded');
      }
      card.classList.toggle('expanded');
      activeService = card.classList.contains('expanded') ? card : null;
    }
  });

  card.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      card.classList.toggle('expanded');
    }
    if (e.key === 'Escape') {
      card.classList.remove('expanded');
    }
  });
});

// Cerrar al hacer clic fuera (móvil)
document.addEventListener('click', (e) => {
  if (!e.target.closest('.service')) {
    if (activeService) {
      activeService.classList.remove('expanded');
      activeService = null;
    }
  }
});

// ─── FAQ ACCORDION ───
document.querySelectorAll('.faq__q').forEach((btn) => {
  btn.addEventListener('click', () => {
    const idx = btn.dataset.faq;
    const target = document.getElementById('faq-' + idx);
    if (!target) return;
    const isOpen = target.classList.contains('open');
    // Cerrar todos
    document.querySelectorAll('.faq__a.open').forEach((el) => el.classList.remove('open'));
    document.querySelectorAll('.faq__q.open').forEach((el) => el.classList.remove('open'));
    document.querySelectorAll('.faq__q').forEach((el) => el.setAttribute('aria-expanded', 'false'));
    if (!isOpen) {
      target.classList.add('open');
      btn.classList.add('open');
      btn.setAttribute('aria-expanded', 'true');
    }
  });
});

// Inicializar estado de navbar (en caso de cargar con scroll)
onScroll();

// ─── SCROLL REVEAL — animaciones al entrar en viewport ───
// Cualquier elemento con [data-reveal] aparece con animación al entrar en pantalla.
// Variantes: data-reveal="up|down|left|right|scale|fade" (default: up)
// Delays: data-reveal-delay="1..8"
const revealEls = document.querySelectorAll('[data-reveal]');

if (revealEls.length && 'IntersectionObserver' in window) {
  const revealObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          revealObserver.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
  );
  revealEls.forEach((el) => revealObserver.observe(el));
} else {
  // Fallback: mostrar todo si no hay soporte
  revealEls.forEach((el) => el.classList.add('is-visible'));
}
