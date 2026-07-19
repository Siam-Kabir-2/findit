import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (reduceMotion) {
        document.querySelectorAll('.reveal').forEach((el) => el.classList.add('visible'));
    } else {
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        observer.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.12 }
        );
        document.querySelectorAll('.reveal').forEach((el) => observer.observe(el));
    }

    const toggle = document.querySelector('[data-nav-toggle]');
    const mobileNav = document.querySelector('[data-mobile-nav]');
    if (toggle && mobileNav) {
        toggle.addEventListener('click', () => {
            const open = mobileNav.classList.toggle('open');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            toggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
        });
    }

    document.querySelectorAll('[data-alert-dismiss]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const alert = btn.closest('[data-alert]');
            if (! alert) {
                return;
            }
            alert.style.transition = 'opacity 180ms ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 200);
        });
    });
});
