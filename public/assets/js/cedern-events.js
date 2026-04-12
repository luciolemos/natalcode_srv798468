(() => {
  const endpoint = '/events';

  const sendEvent = (event) => {
    if (!event || typeof event !== 'object') {
      return;
    }

    const payload = JSON.stringify(event);

    if (navigator.sendBeacon) {
      const blob = new Blob([payload], { type: 'application/json' });
      navigator.sendBeacon(endpoint, blob);
      return;
    }

    fetch(endpoint, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: payload,
      keepalive: true,
    }).catch(() => {});
  };

  const trackPageView = () => {
    sendEvent({
      type: 'page_view',
      title: document.title,
      path: window.location.pathname,
      referrer: document.referrer || '',
    });
  };

  const bindClickTracking = () => {
    document.addEventListener('click', (event) => {
      const target = event.target instanceof Element
        ? event.target.closest('[data-track-event]')
        : null;

      if (!target) {
        return;
      }

      const eventName = target.getAttribute('data-track-event') || 'click';
      const label = target.getAttribute('data-track-label') || target.textContent?.trim() || '';
      const href = target.getAttribute('href') || '';

      sendEvent({
        type: 'click',
        event: eventName,
        label,
        href,
        path: window.location.pathname,
      });
    });
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      trackPageView();
      bindClickTracking();
    });
  } else {
    trackPageView();
    bindClickTracking();
  }
})();
