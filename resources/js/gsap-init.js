import { gsap } from 'gsap';

window.gsap = gsap;

const prefersReducedMotion = () =>
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;

export function initGsapAnimations(root = document) {
    if (prefersReducedMotion()) return;

    const scope = root instanceof Element ? root : document;

    // 1. Hero Title & Seal Entrance (Login / Landing)
    const heroElements = scope.querySelectorAll('[data-gsap="hero-reveal"]');
    if (heroElements.length) {
        gsap.fromTo(
            heroElements,
            { opacity: 0, y: 24, scale: 0.98 },
            {
                opacity: 1,
                y: 0,
                scale: 1,
                duration: 0.6,
                stagger: 0.08,
                ease: 'power2.out',
                clearProps: 'transform,opacity',
            }
        );
    }

    // 2. Smooth Scroll Navigation for Anchor Links
    const smoothLinks = scope.querySelectorAll('a[href^="#"]');
    smoothLinks.forEach((link) => {
        if (link.dataset.gsapBound) return;
        link.dataset.gsapBound = 'true';
        link.addEventListener('click', (e) => {
            const targetId = link.getAttribute('href');
            if (targetId && targetId !== '#' && document.querySelector(targetId)) {
                e.preventDefault();
                const target = document.querySelector(targetId);
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
}
