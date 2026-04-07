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

  const preloadImage = (() => {
    const cache = new Map();

    return (imagePath) => {
      if (!imagePath) {
        return Promise.resolve(null);
      }

      const cachedLoad = cache.get(imagePath);
      if (cachedLoad) {
        return cachedLoad;
      }

      const loadPromise = new Promise((resolve) => {
        const image = new Image();
        let settled = false;

        const settle = (value) => {
          if (settled) {
            return;
          }

          settled = true;
          resolve(value);
        };

        const finalize = () => {
          if (typeof image.decode === 'function') {
            image
              .decode()
              .catch(() => undefined)
              .finally(() => {
                settle(image);
              });
            return;
          }

          settle(image);
        };

        image.addEventListener('load', finalize, { once: true });
        image.addEventListener(
          'error',
          () => {
            settle(null);
          },
          { once: true }
        );

        image.decoding = 'async';
        image.src = imagePath;

        if (image.complete) {
          finalize();
        }
      });

      cache.set(imagePath, loadPromise);
      return loadPromise;
    };
  })();

  const initHeroCarousel = () => {
    const heroes = document.querySelectorAll('.nc-home-hero[data-hero-images]');

    heroes.forEach((hero) => {
      const images = parseImages(hero.getAttribute('data-hero-images'));
      if (images.length < 2) {
        return;
      }

      const intervalMs = normalizeInterval(hero.getAttribute('data-hero-interval'));
      const reducedMotionQuery =
        typeof window.matchMedia === 'function'
          ? window.matchMedia('(prefers-reduced-motion: reduce)')
          : null;
      let currentIndex = 0;
      let autoplayTimer = null;
      let renderRequestId = 0;
      let isInViewport = true;
      let isUserInteracting = false;
      let prefersReducedMotion = reducedMotionQuery ? reducedMotionQuery.matches : false;

      const applyImage = async () => {
        const imagePath = images[currentIndex];
        if (!imagePath) {
          return;
        }

        const currentRequestId = renderRequestId + 1;
        renderRequestId = currentRequestId;

        const decodedImage = await preloadImage(imagePath);
        if (!decodedImage || currentRequestId !== renderRequestId) {
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

        void preloadImage(nextImagePath);
      };

      const stopAutoplay = () => {
        if (autoplayTimer !== null) {
          window.clearInterval(autoplayTimer);
          autoplayTimer = null;
        }
      };

      const shouldAutoplay = () =>
        !prefersReducedMotion && !document.hidden && isInViewport && !isUserInteracting;

      const startAutoplay = () => {
        if (autoplayTimer !== null || !shouldAutoplay()) {
          return;
        }

        autoplayTimer = window.setInterval(() => {
          currentIndex = (currentIndex + 1) % images.length;
          void applyImage();
          preloadNext();
        }, intervalMs);
      };

      const syncAutoplay = () => {
        if (shouldAutoplay()) {
          startAutoplay();
          return;
        }

        stopAutoplay();
      };

      void applyImage();
      preloadNext();
      syncAutoplay();

      hero.addEventListener('mouseenter', () => {
        isUserInteracting = true;
        syncAutoplay();
      });
      hero.addEventListener('mouseleave', () => {
        isUserInteracting = false;
        syncAutoplay();
      });
      hero.addEventListener('focusin', () => {
        isUserInteracting = true;
        syncAutoplay();
      });
      hero.addEventListener('focusout', (event) => {
        const nextFocusedNode = event.relatedTarget;

        if (nextFocusedNode instanceof Node && hero.contains(nextFocusedNode)) {
          return;
        }

        isUserInteracting = false;
        syncAutoplay();
      });

      document.addEventListener('visibilitychange', () => {
        syncAutoplay();
      });

      if (reducedMotionQuery) {
        const onReducedMotionChange = (event) => {
          prefersReducedMotion = event.matches;
          syncAutoplay();
        };

        if (typeof reducedMotionQuery.addEventListener === 'function') {
          reducedMotionQuery.addEventListener('change', onReducedMotionChange);
        } else if (typeof reducedMotionQuery.addListener === 'function') {
          reducedMotionQuery.addListener(onReducedMotionChange);
        }
      }

      if (typeof window.IntersectionObserver === 'function') {
        const observer = new IntersectionObserver(
          (entries) => {
            entries.forEach((entry) => {
              if (entry.target !== hero) {
                return;
              }

              isInViewport = entry.isIntersecting && entry.intersectionRatio >= 0.15;
              syncAutoplay();
            });
          },
          {
            threshold: [0, 0.15, 0.5],
          }
        );

        observer.observe(hero);
      }
    });
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initHeroCarousel);
  } else {
    initHeroCarousel();
  }
})();
