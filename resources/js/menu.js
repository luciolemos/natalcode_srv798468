// Menu do celular: abre/fecha e mantém aria-expanded em dia.
export function initMenu() {
    const toggle = document.querySelector('[data-menu-toggle]');
    const menu = document.querySelector('[data-menu]');

    if (!toggle || !menu) {
        return;
    }

    const setOpen = (open) => {
        menu.classList.toggle('hidden', !open);
        toggle.setAttribute('aria-expanded', String(open));
        toggle.querySelector('[data-menu-icon-open]')?.classList.toggle('hidden', open);
        toggle.querySelector('[data-menu-icon-close]')?.classList.toggle('hidden', !open);
        toggle.querySelector('.sr-only').textContent = open ? 'Fechar menu' : 'Abrir menu';
    };

    toggle.addEventListener('click', () => setOpen(menu.classList.contains('hidden')));
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !menu.classList.contains('hidden')) {
            setOpen(false);
            toggle.focus();
        }
    });
    window.matchMedia('(min-width: 768px)').addEventListener('change', (event) => {
        if (event.matches) {
            setOpen(false);
        }
    });
}
