import './bootstrap';
import * as bootstrap from 'bootstrap';
import 'bootstrap-icons/font/bootstrap-icons.css';

window.bootstrap = bootstrap;

// ─── Navbar scroll effect ───
const navbar = document.querySelector('.landing-navbar');
let ticking = false;

if (navbar) {
  document.addEventListener('scroll', () => {
    if (!ticking) {
      window.requestAnimationFrame(() => {
        navbar.classList.toggle('scrolled', window.scrollY > 60);
        ticking = false;
      });
      ticking = true;
    }
  });
}

// ─── Back to top button ───
const backToTop = document.querySelector('.back-to-top');

if (backToTop) {
  document.addEventListener('scroll', () => {
    backToTop.classList.toggle('visible', window.scrollY > 500);
  });

  backToTop.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
}

// ─── Counter animation ───
const counters = document.querySelectorAll('.stats-bar__number');

if (counters.length) {
  const counterObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const el = entry.target;
          const target = parseInt(el.dataset.target, 10);
          if (!target || el.dataset.counted) return;
          el.dataset.counted = 'true';

          const duration = 2000;
          const start = performance.now();

          const update = (now) => {
            const elapsed = now - start;
            const progress = Math.min(elapsed / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            const current = Math.round(target * eased);
            el.textContent = current.toLocaleString('es-BO');

            if (progress < 1) {
              requestAnimationFrame(update);
            } else {
              el.textContent = target.toLocaleString('es-BO');
            }
          };

          requestAnimationFrame(update);
        }
      });
    },
    { threshold: 0.5 }
  );

  counters.forEach((el) => counterObserver.observe(el));
}

// ─── Fade-up / scroll animations ───
const animObserver = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
      }
    });
  },
  { threshold: 0.1, rootMargin: '0px 0px -50px 0px' }
);

document.querySelectorAll('.animate-fade-up, .animate-fade-left, .animate-fade-right, .animate-scale')
  .forEach((el) => animObserver.observe(el));

// ─── Smooth anchor scroll (offset for fixed navbar) ───
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener('click', (e) => {
    const target = document.querySelector(anchor.getAttribute('href'));
    if (target) {
      e.preventDefault();
      const offset = 80;
      const top = target.getBoundingClientRect().top + window.scrollY - offset;
      window.scrollTo({ top, behavior: 'smooth' });
    }
  });
});
