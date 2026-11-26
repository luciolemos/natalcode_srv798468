/*
 * Google Analytics 4 com consentimento (LGPD).
 * - Nada do Google é carregado antes de o visitante clicar em "Aceitar".
 * - Escolha salva em localStorage ("natalcode_analytics_consent": granted | denied).
 * - [data-consent-open] reabre o banner (link no rodapé).
 * - Eventos: whatsapp_click (links wa.me), contact_click (mailto/tel) e
 *   generate_lead (elemento [data-analytics-lead], exibido só após envio aceito pelo servidor).
 */
const STORAGE_KEY = 'natalcode_analytics_consent';

export function initAnalytics() {
    const measurementId = document.body.dataset.ga4Id || '';

    if (!measurementId) {
        return;
    }

    const banner = document.querySelector('[data-consent]');
    let loaded = false;
    let leadTracked = false;

    window.dataLayer = window.dataLayer || [];
    const gtag = function () {
        window.dataLayer.push(arguments);
    };

    const readConsent = () => {
        try {
            return localStorage.getItem(STORAGE_KEY) || '';
        } catch {
            return '';
        }
    };

    const saveConsent = (value) => {
        try {
            localStorage.setItem(STORAGE_KEY, value);
        } catch {
            // Sem storage (navegação privada restrita): vale só para esta página.
        }
    };

    const track = (name, params = {}) => {
        if (loaded) {
            gtag('event', name, { page_path: window.location.pathname, ...params });
        }
    };

    const trackConfirmedLead = () => {
        const marker = document.querySelector('[data-analytics-lead]');

        if (loaded && marker && !leadTracked) {
            leadTracked = true;
            track('generate_lead', { form: marker.dataset.analyticsLead || 'contato' });
        }
    };

    const load = () => {
        if (loaded) {
            return;
        }

        loaded = true;
        const script = document.createElement('script');
        script.async = true;
        script.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(measurementId);
        document.head.appendChild(script);
        gtag('js', new Date());
        gtag('config', measurementId, { anonymize_ip: true });
        trackConfirmedLead();
    };

    const showBanner = (show) => {
        if (banner) {
            banner.hidden = !show;
        }
    };

    document.addEventListener('click', (event) => {
        const target = event.target instanceof Element ? event.target : null;

        if (!target) {
            return;
        }

        const choice = target.closest('[data-consent-choice]');

        if (choice) {
            const value = choice.dataset.consentChoice === 'granted' ? 'granted' : 'denied';
            saveConsent(value);
            showBanner(false);

            if (value === 'granted') {
                load();
            } else if (loaded) {
                window.location.reload();
            }

            return;
        }

        if (target.closest('[data-consent-open]')) {
            showBanner(true);
            return;
        }

        const link = target.closest('a[href]');
        const href = link?.getAttribute('href') || '';

        if (/^https:\/\/(wa\.me|api\.whatsapp\.com)\//i.test(href)) {
            track('whatsapp_click');
        } else if (/^(mailto|tel):/i.test(href)) {
            track('contact_click', { method: href.split(':')[0] });
        }
    });

    const consent = readConsent();

    if (consent === 'granted') {
        load();
    } else if (consent !== 'denied') {
        showBanner(true);
    }
}
