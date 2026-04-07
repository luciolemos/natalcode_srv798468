(function () {
  const parseImages = (rawValue) => {
    if (!rawValue) {
      return [];
    }

    try {
      const parsed = JSON.parse(rawValue);
      if (!Array.isArray(parsed)) {
        return [];
      }

      return parsed
        .map((item) => (typeof item === 'string' ? item.trim() : ''))
        .filter((item) => item !== '');
    } catch (error) {
      return [];
    }
  };

  const normalizeInterval = (rawValue) => {
    const parsed = Number.parseInt(rawValue || '5500', 10);

    if (!Number.isFinite(parsed)) {
      return 5500;
    }

    return Math.min(15000, Math.max(2500, parsed));
  };

  const initHeroCarousel = () => {
    const heroes = document.querySelectorAll('.nc-home-hero[data-hero-images]');

    heroes.forEach((hero) => {
      const images = parseImages(hero.getAttribute('data-hero-images'));
      if (images.length < 2) {
        return;
      }

      const intervalMs = normalizeInterval(hero.getAttribute('data-hero-interval'));
      let currentIndex = 0;
      let autoplayTimer = null;

      const applyImage = () => {
        const imagePath = images[currentIndex];
        if (!imagePath) {
          return;
        }

        hero.style.setProperty('--nc-hero-bg-image', "url('" + imagePath + "')");
      };

      const preloadNext = () => {
        const nextIndex = (currentIndex + 1) % images.length;
        const nextImagePath = images[nextIndex];

        if (!nextImagePath) {
          return;
        }

        const preloadedImage = new Image();
        preloadedImage.src = nextImagePath;
      };

      const stopAutoplay = () => {
        if (autoplayTimer !== null) {
          window.clearInterval(autoplayTimer);
          autoplayTimer = null;
        }
      };

      const startAutoplay = () => {
        if (autoplayTimer !== null) {
          return;
        }

        autoplayTimer = window.setInterval(() => {
          currentIndex = (currentIndex + 1) % images.length;
          applyImage();
          preloadNext();
        }, intervalMs);
      };

      applyImage();
      preloadNext();
      startAutoplay();

      hero.addEventListener('mouseenter', stopAutoplay);
      hero.addEventListener('mouseleave', startAutoplay);
      hero.addEventListener('focusin', stopAutoplay);
      hero.addEventListener('focusout', startAutoplay);

      document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
          stopAutoplay();
          return;
        }

        startAutoplay();
      });
    });
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initHeroCarousel);
  } else {
    initHeroCarousel();
  }
})();
