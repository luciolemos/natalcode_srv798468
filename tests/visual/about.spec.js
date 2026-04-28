const { test, expect } = require('@playwright/test');

function getMaxDiffRatio(testInfo)
{
    if (testInfo.project.name === 'desktop') {
        return 0.04;
    }
    if (testInfo.project.name === 'mobile') {
        return 0.12;
    }
    return 0.08;
}

async function stabilizeForScreenshot(page)
{
    await page.waitForLoadState('networkidle');
    await page.waitForFunction(() => document.fonts && document.fonts.status === 'loaded');
    await page.waitForFunction(() => Array.from(document.images).every((img) => img.complete && img.naturalWidth > 0));
    await page.evaluate(() => {
        const videos = Array.from(document.querySelectorAll('video'));
        videos.forEach((video) => {
            try {
                video.pause();
                video.currentTime = 0;
            } catch (error) {
                // Ignora restricoes ocasionais do navegador em controles de midia durante testes.
            }
        });
    });
    await page.waitForTimeout(120);
    await page.evaluate(async() => {
        window.scrollTo(0, document.documentElement.scrollHeight);
        await new Promise((resolve) => window.setTimeout(resolve, 120));
        window.scrollTo(0, 0);
    });
    await page.waitForTimeout(120);
    let stableSamples = 0;
    let lastHeight = await page.evaluate(() => document.documentElement.scrollHeight);
    for (let index = 0; index < 12; index += 1) {
        await page.waitForTimeout(100);
        const currentHeight = await page.evaluate(() => document.documentElement.scrollHeight);
        if (Math.abs(currentHeight - lastHeight) <= 2) {
            stableSamples += 1;
            if (stableSamples >= 3) {
                break;
            }
        } else {
            stableSamples = 0;
        }
        lastHeight = currentHeight;
    }
    await page.addStyleTag({
        content: 'html, body { transition: none !important; }',
    });
    await page.evaluate(() => new Promise((resolve) => {
        requestAnimationFrame(() => requestAnimationFrame(resolve));
    }));
    await page.waitForTimeout(100);
}

async function openAboutReady(page)
{
    await page.goto('/quem-somos');
    await page.waitForLoadState('domcontentloaded');
    await page.locator('.nc-footer').waitFor();
    await stabilizeForScreenshot(page);
}

test.describe('About visual regression', () => {
    test('about top fold', async({ page }, testInfo) => {
        await openAboutReady(page);

        await expect(page).toHaveScreenshot('about-top.png', {
            fullPage: false,
            animations: 'disabled',
            maxDiffPixelRatio: getMaxDiffRatio(testInfo),
        });
    });

    test('about full page', async({ page }, testInfo) => {
        await openAboutReady(page);

        await expect(page).toHaveScreenshot('about-full.png', {
            fullPage: true,
            animations: 'disabled',
            maxDiffPixelRatio: getMaxDiffRatio(testInfo),
        });
    });
});
