const { test, expect } = require('@playwright/test');

function getMaxDiffRatio(testInfo)
{
    if (testInfo.project.name === 'desktop') {
        return 0.04;
    }
    return 0.08;
}

async function stabilizeForScreenshot(page)
{
    await page.waitForLoadState('networkidle');
    await page.waitForFunction(() => document.fonts && document.fonts.status === 'loaded');
    await page.waitForFunction(() => Array.from(document.images).every((img) => img.complete && img.naturalWidth > 0));
    await page.evaluate(() => {
        document.querySelectorAll('.nc-home-hero[data-hero-media], .nc-home-hero[data-hero-images]').forEach((hero) => {
            const titleElement = hero.querySelector('[data-hero-copy-title]');
            if (!titleElement) {
                return;
            }

            const rawMedia = hero.getAttribute('data-hero-media') || hero.getAttribute('data-hero-images');
            if (!rawMedia) {
                titleElement.classList.remove('nc-is-typewriting', 'nc-is-typewriting-live');
                return;
            }

            try {
                const parsed = JSON.parse(rawMedia);
                const first = Array.isArray(parsed) ? parsed[0] : null;
                const finalTitle =
                    first && typeof first.title === 'string' && first.title.trim()
                        ? first.title.trim()
                        : titleElement.textContent;

                titleElement.textContent = finalTitle || '';
                titleElement.classList.remove('nc-is-typewriting', 'nc-is-typewriting-live');
            } catch (_error) {
                titleElement.classList.remove('nc-is-typewriting', 'nc-is-typewriting-live');
            }
        });
    });
    await page.addStyleTag({
        content: 'html, body { transition: none !important; }',
    });
    await page.evaluate(() => new Promise((resolve) => {
        requestAnimationFrame(() => requestAnimationFrame(resolve));
    }));
    await page.waitForTimeout(100);
}

async function openHomeReady(page)
{
    await page.goto('/');
    await page.waitForLoadState('domcontentloaded');
    await page.locator('.nc-footer').waitFor();
    await stabilizeForScreenshot(page);
}

test.describe('Home visual regression', () => {
    test('home top fold', async({ page }, testInfo) => {
        await openHomeReady(page);

        await expect(page).toHaveScreenshot('home-top.png', {
            fullPage: false,
            animations: 'disabled',
            maxDiffPixelRatio: getMaxDiffRatio(testInfo),
        });
    });

    test('home full page', async({ page }, testInfo) => {
        await openHomeReady(page);

        await expect(page).toHaveScreenshot('home-full.png', {
            fullPage: true,
            animations: 'disabled',
            maxDiffPixelRatio: getMaxDiffRatio(testInfo),
        });
    });
});
