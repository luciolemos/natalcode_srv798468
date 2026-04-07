(function () {
  const normalizeMediaItem = (item) => {
    if (typeof item === 'string') {
      const source = item.trim();
      if (!source) {
        return null;
      }

      return {
        src: source,
        srcset: '',
        avifSrcset: '',
        webpSrcset: '',
        sizes: '100vw',
      };
    }

    if (!item || typeof item !== 'object') {
      return null;
    }

    const source = typeof item.src === 'string' ? item.src.trim() : '';
    if (!source) {
      return null;
    }

    return {
      src: source,
      srcset: typeof item.srcset === 'string' ? item.srcset.trim() : '',
      avifSrcset: typeof item.avifSrcset === 'string' ? item.avifSrcset.trim() : '',
      webpSrcset: typeof item.webpSrcset === 'string' ? item.webpSrcset.trim() : '',
      sizes: typeof item.sizes === 'string' && item.sizes.trim() ? item.sizes.trim() : '100vw',
    };
  };

  const parseMediaItems = (rawValue) => {
    if (!rawValue) {
      return [];
    }

    try {
      const parsed = JSON.parse(rawValue);
      if (!Array.isArray(parsed)) {
        return [];
      }

      return parsed.map(normalizeMediaItem).filter((item) => item !== null);
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

    return (media) => {
      if (!media || !media.src) {
        return Promise.resolve(null);
      }

      const cacheKey = [media.src, media.srcset || '', media.sizes || ''].join('|');
      const cachedLoad = cache.get(cacheKey);
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

        if (media.srcset) {
          image.srcset = media.srcset;
        }

        if (media.sizes) {
          image.sizes = media.sizes;
        }

        image.decoding = 'async';
        image.src = media.src;

        if (image.complete) {
          finalize();
        }
      });

      cache.set(cacheKey, loadPromise);
      return loadPromise;
    };
  })();

  const initHeroCarousel = () => {
    const heroes = document.querySelectorAll('.nc-home-hero[data-hero-media], .nc-home-hero[data-hero-images]');

    heroes.forEach((hero) => {
      const mediaItems = parseMediaItems(
        hero.getAttribute('data-hero-media') || hero.getAttribute('data-hero-images')
      );
      if (mediaItems.length < 2) {
        return;
      }

      const picture = hero.querySelector('[data-hero-picture]');
      const avifSource = hero.querySelector('[data-hero-source-avif]');
      const webpSource = hero.querySelector('[data-hero-source-webp]');
      const imageElement = hero.querySelector('[data-hero-image]');

      if (!picture || !imageElement) {
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

      const setOrRemoveAttr = (element, attributeName, value) => {
        if (!element) {
          return;
        }

        if (!value) {
          element.removeAttribute(attributeName);
          return;
        }

        element.setAttribute(attributeName, value);
      };

      const applyMediaMarkup = (media) => {
        if (!media) {
          return;
        }

        setOrRemoveAttr(avifSource, 'srcset', media.avifSrcset);
        setOrRemoveAttr(webpSource, 'srcset', media.webpSrcset);
        setOrRemoveAttr(imageElement, 'srcset', media.srcset);
        setOrRemoveAttr(imageElement, 'sizes', media.sizes || '100vw');
        imageElement.src = media.src;
      };

      const applyImage = async () => {
        const media = mediaItems[currentIndex];
        if (!media || !media.src) {
          return;
        }

        const currentRequestId = renderRequestId + 1;
        renderRequestId = currentRequestId;

        const decodedImage = await preloadImage(media);
        if (!decodedImage || currentRequestId !== renderRequestId) {
          return;
        }

        applyMediaMarkup(media);
      };

      const preloadNext = () => {
        const nextIndex = (currentIndex + 1) % mediaItems.length;
        void preloadImage(mediaItems[nextIndex]);
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
          currentIndex = (currentIndex + 1) % mediaItems.length;
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
