import './bootstrap';

window.cropperComponent = (imageUrl) => ({
    imageUrl,
    __cropperLoaded: false,
    async init() {
        if (this.__cropperLoaded) return;
        this.__cropperLoaded = true;

        const mod = await import('./imageCropper');
        if (typeof mod.createCropper !== 'function') return;

        const data = mod.createCropper(imageUrl);
        Object.assign(this, data);

        if (typeof data.init === 'function') {
            data.init.call(this);
        }
    },
});

const media = window.matchMedia('(prefers-color-scheme: dark)');

function normalizeMode(mode) {
    if (!mode) return 'system';
    if (mode === 'light') return 'normal';
    if (mode !== 'dark' && mode !== 'normal' && mode !== 'system') return 'system';
    return mode;
}

function isDark(mode) {
    return mode === 'dark' || (mode === 'system' && media.matches);
}

function applyTheme(mode, persist = false) {
    const current = normalizeMode(mode);
    const dark = isDark(current);

    document.documentElement.classList.toggle('dark', dark);
    if (persist) localStorage.setItem('theme', current);

    document.querySelectorAll('[data-theme]').forEach((btn) => {
        const t = btn.dataset.theme;

        let hide = false;

        if (current === 'system') {
            // system mode → hide the effective theme option
            hide = (dark && t === 'dark') || (!dark && t === 'normal');
        } else {
            // explicit mode → hide the active one
            hide = t === current;
        }

        // Use attribute, not class (so .btn can't override it)
        btn.toggleAttribute('hidden', hide);
    });
}

// init
applyTheme(localStorage.getItem('theme'));

// clicks (Livewire-safe)
document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-theme]');
    if (!btn) return;
    applyTheme(btn.dataset.theme, true);
});

// OS changes while in system mode
media.addEventListener('change', () => {
    if (normalizeMode(localStorage.getItem('theme')) === 'system') {
        applyTheme('system');
    }
});
