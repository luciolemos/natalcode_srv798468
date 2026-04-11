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
        kicker: '',
        badge: '',
        title: '',
        tagline: '',
        lead: '',
        imageAlt: '',
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
      kicker: typeof item.kicker === 'string' ? item.kicker.trim() : '',
      badge: typeof item.badge === 'string' ? item.badge.trim() : '',
      title: typeof item.title === 'string' ? item.title.trim() : '',
      tagline: typeof item.tagline === 'string' ? item.tagline.trim() : '',
      lead: typeof item.lead === 'string' ? item.lead.trim() : '',
      imageAlt: typeof item.imageAlt === 'string' ? item.imageAlt.trim() : '',
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
      const controls = hero.querySelector('[data-hero-controls]');
      const previousButton = hero.querySelector('[data-hero-prev]');
      const nextButton = hero.querySelector('[data-hero-next]');
      const toggleButton = hero.querySelector('[data-hero-toggle]');
      const statusElement = hero.querySelector('[data-hero-status]');
      const dotButtons = Array.from(hero.querySelectorAll('[data-hero-dot]'));
      const copyStackElement = hero.querySelector('[data-hero-copy-stack]');
      const kickerElement = hero.querySelector('[data-hero-copy-kicker]');
      const badgeElement = hero.querySelector('[data-hero-copy-badge]');
      const titleElement = hero.querySelector('[data-hero-copy-title]');
      const taglineElement = hero.querySelector('[data-hero-copy-tagline]');
      const leadElement = hero.querySelector('[data-hero-copy-lead]');
      const imageAltElement = hero.querySelector('[data-hero-copy-image-alt]');

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
      let isUserPaused = false;
      let prefersReducedMotion = reducedMotionQuery ? reducedMotionQuery.matches : false;
      let copyTransitionTimer = null;

      const readTextContent = (element) => {
        if (!element || typeof element.textContent !== 'string') {
          return '';
        }

        return element.textContent.trim();
      };

      const fallbackCopy = {
        kicker: readTextContent(kickerElement),
        badge: readTextContent(badgeElement),
        title: readTextContent(titleElement),
        tagline: readTextContent(taglineElement),
        lead: readTextContent(leadElement),
        imageAlt: readTextContent(imageAltElement),
      };

      const resolveCopyValue = (value, fallbackValue = '') => {
        if (typeof value === 'string' && value.trim()) {
          return value.trim();
        }

        return fallbackValue;
      };

      const setCopyText = (element, value, hideWhenEmpty = false) => {
        if (!element) {
          return;
        }

        const hasValue = typeof value === 'string' && value.trim() !== '';
        element.textContent = hasValue ? value : '';

        if (!hideWhenEmpty) {
          return;
        }

        if (hasValue) {
          element.removeAttribute('hidden');
          return;
        }

        element.setAttribute('hidden', '');
      };

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

      const animateCopySwap = () => {
        if (!copyStackElement || prefersReducedMotion) {
          return;
        }

        copyStackElement.classList.remove('is-transitioning');
        void copyStackElement.offsetWidth;
        copyStackElement.classList.add('is-transitioning');

        if (copyTransitionTimer !== null) {
          window.clearTimeout(copyTransitionTimer);
        }

        copyTransitionTimer = window.setTimeout(() => {
          copyStackElement.classList.remove('is-transitioning');
          copyTransitionTimer = null;
        }, 320);
      };

      const applyCopyMarkup = (media, options = {}) => {
        if (!media) {
          return;
        }

        const kicker = resolveCopyValue(media.kicker, fallbackCopy.kicker);
        const badge = resolveCopyValue(media.badge, fallbackCopy.badge);
        const title = resolveCopyValue(media.title, fallbackCopy.title);
        const tagline = resolveCopyValue(media.tagline, fallbackCopy.tagline);
        const lead = resolveCopyValue(media.lead, fallbackCopy.lead);
        const imageAlt = resolveCopyValue(media.imageAlt, fallbackCopy.imageAlt);

        setCopyText(kickerElement, kicker);
        setCopyText(badgeElement, badge, true);
        setCopyText(titleElement, title);
        setCopyText(taglineElement, tagline, true);
        setCopyText(leadElement, lead);
        setCopyText(imageAltElement, imageAlt);

        if (options.animate !== false) {
          animateCopySwap();
        }
      };

      const updateDots = () => {
        if (!dotButtons.length) {
          return;
        }

        dotButtons.forEach((dotButton, index) => {
          const isActive = index === currentIndex;
          dotButton.classList.toggle('is-active', isActive);

          if (isActive) {
            dotButton.setAttribute('aria-current', 'true');
            return;
          }

          dotButton.removeAttribute('aria-current');
        });
      };

      const updateStatus = (isUserInitiated) => {
        if (!statusElement) {
          return;
        }

        const activeMedia = mediaItems[currentIndex];
        const activeTitle =
          activeMedia && typeof activeMedia.title === 'string' && activeMedia.title.trim()
            ? ': ' + activeMedia.title.trim()
            : '';

        statusElement.setAttribute('aria-live', isUserInitiated ? 'polite' : 'off');
        statusElement.textContent = 'Imagem ' + (currentIndex + 1) + ' de ' + mediaItems.length + activeTitle;
      };

      const updateToggleButton = () => {
        if (!toggleButton) {
          return;
        }

        const playLabel = toggleButton.getAttribute('data-label-play') || 'Reproduzir';
        const pauseLabel = toggleButton.getAttribute('data-label-pause') || 'Pausar';
        const isAutoplayDisabled = prefersReducedMotion || isUserPaused;

        toggleButton.textContent = isAutoplayDisabled ? playLabel : pauseLabel;
        toggleButton.setAttribute('aria-pressed', isAutoplayDisabled ? 'true' : 'false');
        toggleButton.disabled = !!prefersReducedMotion;
      };

      const applyImage = async (options = {}) => {
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
        applyCopyMarkup(media, { animate: options.animateCopy !== false });
        updateDots();
        updateStatus(!!options.userInitiated);
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
        !prefersReducedMotion && !isUserPaused && !document.hidden && isInViewport && !isUserInteracting;

      const startAutoplay = () => {
        if (autoplayTimer !== null || !shouldAutoplay()) {
          return;
        }

        autoplayTimer = window.setInterval(() => {
          currentIndex = (currentIndex + 1) % mediaItems.length;
          void applyImage({ userInitiated: false });
          preloadNext();
        }, intervalMs);
      };

      const goToSlide = (targetIndex, isUserInitiated) => {
        const wrappedIndex = ((targetIndex % mediaItems.length) + mediaItems.length) % mediaItems.length;
        currentIndex = wrappedIndex;
        void applyImage({ userInitiated: !!isUserInitiated });
        preloadNext();
        syncAutoplay();
      };

      const goToNext = (isUserInitiated) => {
        goToSlide(currentIndex + 1, isUserInitiated);
      };

      const goToPrevious = (isUserInitiated) => {
        goToSlide(currentIndex - 1, isUserInitiated);
      };

      const syncAutoplay = () => {
        if (shouldAutoplay()) {
          startAutoplay();
          return;
        }

        stopAutoplay();
      };

      updateDots();
      updateStatus(false);
      updateToggleButton();
      void applyImage({ userInitiated: false, animateCopy: false });
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

      if (previousButton) {
        previousButton.addEventListener('click', () => {
          goToPrevious(true);
        });
      }

      if (nextButton) {
        nextButton.addEventListener('click', () => {
          goToNext(true);
        });
      }

      if (toggleButton) {
        toggleButton.addEventListener('click', () => {
          if (prefersReducedMotion) {
            return;
          }

          isUserPaused = !isUserPaused;
          updateToggleButton();
          syncAutoplay();
          updateStatus(true);
        });
      }

      if (dotButtons.length) {
        dotButtons.forEach((dotButton, index) => {
          dotButton.addEventListener('click', () => {
            goToSlide(index, true);
          });
        });
      }

      if (controls) {
        controls.addEventListener('keydown', (event) => {
          if (event.key === 'ArrowLeft') {
            event.preventDefault();
            goToPrevious(true);
            return;
          }

          if (event.key === 'ArrowRight') {
            event.preventDefault();
            goToNext(true);
          }
        });
      }

      document.addEventListener('visibilitychange', () => {
        syncAutoplay();
      });

      if (reducedMotionQuery) {
        const onReducedMotionChange = (event) => {
          prefersReducedMotion = event.matches;
          updateToggleButton();
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
