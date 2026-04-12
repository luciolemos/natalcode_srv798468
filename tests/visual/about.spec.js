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
    await page.addStyleTag({
        content: `
            * { caret-color: transparent !important; }
            *, *::before, *::after { transition: none !important; }
            html { -webkit-font-smoothing: antialiased; }
        `,
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
