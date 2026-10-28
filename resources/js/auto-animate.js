import autoAnimate from '@formkit/auto-animate';

const DEFAULT_OPTIONS = {
    duration: 250,
    easing: 'ease-in-out',
    disrespectUserMotionPreference: false,
};

const initialized = new WeakSet();

function parseOptions(el) {
    const raw = el.getAttribute('data-auto-animate');
    if (!raw || raw === '' || raw === 'true') return DEFAULT_OPTIONS;
    try {
        const parsed = JSON.parse(raw);
        if (parsed && typeof parsed === 'object') {
            return { ...DEFAULT_OPTIONS, ...parsed };
        }
    } catch {
        // ignore malformed JSON — fall back to defaults
    }
    return DEFAULT_OPTIONS;
}

export function initAutoAnimate(root = document) {
    const scope = root instanceof Element ? root : document;
    const elements = scope.querySelectorAll
        ? scope.querySelectorAll('[data-auto-animate]')
        : [];

    elements.forEach((el) => {
        if (initialized.has(el)) return;
        const options = parseOptions(el);
        autoAnimate(el, options);
        initialized.add(el);
    });

    // Also handle root itself if it carries the attribute (e.g. Livewire morph target)
    if (root instanceof Element && root.hasAttribute('data-auto-animate') && !initialized.has(root)) {
        autoAnimate(root, parseOptions(root));
        initialized.add(root);
    }
}

export { autoAnimate, DEFAULT_OPTIONS };
