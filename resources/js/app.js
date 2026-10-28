import SignaturePad from 'signature_pad';
import { initAutoAnimate } from './auto-animate.js';
import { initGsapAnimations } from './gsap-init.js';

window.SignaturePad = SignaturePad;

// Init animations on first load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        initAutoAnimate();
        initGsapAnimations();
    });
} else {
    initAutoAnimate();
    initGsapAnimations();
}

// Re-init after Livewire navigations / morph updates
document.addEventListener('livewire:init', () => {
    // Livewire 4 hook API — re-scan after DOM morph
    if (window.Livewire && typeof window.Livewire.hook === 'function') {
        window.Livewire.hook('morph.updated', ({ el }) => {
            // el is the morphed element; re-scan its subtree + itself
            initAutoAnimate(el);
            initGsapAnimations(el);
        });
    }
});

document.addEventListener('livewire:navigated', () => {
    initAutoAnimate();
    initGsapAnimations();
});

