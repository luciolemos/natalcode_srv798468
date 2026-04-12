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

        if (testInfo.project.name === 'mobile') {
            testInfo.skip('Mobile full-page snapshot is flaky due to dynamic page height.');
        }

        await expect(page).toHaveScreenshot('home-full.png', {
            fullPage: true,
            animations: 'disabled',
            maxDiffPixelRatio: getMaxDiffRatio(testInfo),
        });
    });
});
