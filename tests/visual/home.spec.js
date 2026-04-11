const { test, expect } = require('@playwright/test');

async function stabilizeForScreenshot(page)
{
    await page.waitForLoadState('networkidle');
    await page.waitForFunction(() => document.fonts && document.fonts.status === 'loaded');
    await page.addStyleTag({
        content: `
            * { caret-color: transparent !important; }
            *, *::before, *::after { transition: none !important; }
            html { -webkit-font-smoothing: antialiased; }
        `,
    });
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
    test('home top fold', async({ page }) => {
        await openHomeReady(page);

        await expect(page).toHaveScreenshot('home-top.png', {
            fullPage: false,
            animations: 'disabled',
        });
    });

    test('home full page', async({ page }) => {
        await openHomeReady(page);

        await expect(page).toHaveScreenshot('home-full.png', {
            fullPage: true,
            animations: 'disabled',
        });
    });
});
