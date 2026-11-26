/*
 * Analytics com consentimento (LGPD).
 * - Nada do Google é carregado antes de o visitante clicar em "Aceitar".
 * - A escolha fica salva em localStorage ("natalcode_analytics_consent": granted | denied).
 * - Qualquer elemento com [data-analytics-consent-open] reabre o banner (ex.: link no rodapé).
 * - Eventos de conversão enviados após consentimento:
 *     whatsapp_click  -> clique em links wa.me / api.whatsapp.com
 *     generate_lead   -> mensagem de /contato aceita pelo servidor (aviso de sucesso
 *                        com [data-analytics-lead], exibido uma única vez após o envio)
 *     contact_click   -> clique em links mailto: / tel:
 */
(() => {
  const STORAGE_KEY = 'natalcode_analytics_consent';
  const script = document.currentScript;
  const gtmId = (script && script.dataset.gtmId) || '';
  const ga4Id = (script && script.dataset.ga4Id) || '';

  if (!gtmId && !ga4Id) {
    return;
  }

  let loaded = false;

  const readConsent = () => {
    try {
      return localStorage.getItem(STORAGE_KEY) || '';
    } catch (error) {
      return '';
    }
  };

  const saveConsent = (value) => {
    try {
      localStorage.setItem(STORAGE_KEY, value);
    } catch (error) {
      // Navegação privada sem storage: a escolha vale só para esta página.
    }
  };

  window.dataLayer = window.dataLayer || [];
  const gtag = function () {
    window.dataLayer.push(arguments);
  };

  const injectScript = (src) => {
    const tag = document.createElement('script');
    tag.async = true;
    tag.src = src;
    document.head.appendChild(tag);
  };

  const loadAnalytics = () => {
    if (loaded) {
      return;
    }
    loaded = true;

    if (gtmId) {
      window.dataLayer.push({ 'gtm.start': Date.now(), event: 'gtm.js' });
      injectScript('https://www.googletagmanager.com/gtm.js?id=' + encodeURIComponent(gtmId));
      return;
    }

    injectScript('https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(ga4Id));
    gtag('js', new Date());
    gtag('config', ga4Id, { anonymize_ip: true });
  };

  const track = (eventName, params) => {
    if (!loaded) {
      return;
    }

    if (gtmId) {
      window.dataLayer.push(Object.assign({ event: eventName }, params || {}));
      return;
    }

    gtag('event', eventName, params || {});
  };

  const bindConversionTracking = () => {
    document.addEventListener('click', (event) => {
      const link = event.target instanceof Element ? event.target.closest('a[href]') : null;

      if (!link) {
        return;
      }

      const href = link.getAttribute('href') || '';

      if (/^https:\/\/(wa\.me|api\.whatsapp\.com)\//i.test(href)) {
        track('whatsapp_click', { page_path: window.location.pathname });
      } else if (/^(mailto|tel):/i.test(href)) {
        track('contact_click', { method: href.split(':')[0], page_path: window.location.pathname });
      }
    });
  };

  // Não usa o evento "submit": o reCAPTCHA cancela e reenvia o formulário (contaria em dobro)
  // e o envio pode ser recusado pelo servidor. O aviso de sucesso só aparece para envio aceito.
  let leadTracked = false;
  const trackConfirmedLead = () => {
    const marker = document.querySelector('[data-analytics-lead]');

    if (!loaded || leadTracked || !marker) {
      return;
    }

    leadTracked = true;
    track('generate_lead', { form: marker.getAttribute('data-analytics-lead') || 'contato', page_path: window.location.pathname });
  };

  const init = () => {
    const banner = document.querySelector('[data-analytics-consent]');

    const showBanner = () => {
      if (banner) {
        banner.hidden = false;
      }
    };

    const hideBanner = () => {
      if (banner) {
        banner.hidden = true;
      }
    };

    document.addEventListener('click', (event) => {
      const target = event.target instanceof Element ? event.target : null;

      if (!target) {
        return;
      }

      const choiceButton = target.closest('[data-analytics-consent-choice]');

      if (choiceButton) {
        const choice = choiceButton.getAttribute('data-analytics-consent-choice') === 'granted' ? 'granted' : 'denied';
        saveConsent(choice);
        hideBanner();

        if (choice === 'granted') {
          loadAnalytics();
          trackConfirmedLead();
        } else if (loaded) {
          // Já carregado nesta página: recarrega para descarregar os scripts do Google.
          window.location.reload();
        }

        return;
      }

      if (target.closest('[data-analytics-consent-open]')) {
        event.preventDefault();
        showBanner();
      }
    });

    bindConversionTracking();

    const consent = readConsent();

    if (consent === 'granted') {
      loadAnalytics();
      trackConfirmedLead();
    } else if (consent !== 'denied') {
      showBanner();
    }
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
