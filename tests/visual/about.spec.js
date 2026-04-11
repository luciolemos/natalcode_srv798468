const { test, expect } = require('@playwright/test');

async function stabilizeForScreenshot(page)
{
    await page.waitForLoadState('networkidle');
    await page.addStyleTag({
        content: `
            * { caret-color: transparent !important; }
            html { -webkit-font-smoothing: antialiased; }
        `,
    });
    await page.waitForFunction(() => document.fonts && document.fonts.status === 'loaded');
}

async function openAboutReady(page)
{
    await page.goto('/quem-somos');
    await page.waitForLoadState('domcontentloaded');
    await page.locator('.nc-footer').waitFor();
    await stabilizeForScreenshot(page);
}

test.describe('About visual regression', () => {
    test('about top fold', async({ page }) => {
        await openAboutReady(page);

        await expect(page).toHaveScreenshot('about-top.png', {
            fullPage: false,
            animations: 'disabled',
        });
    });

    test('about full page', async({ page }) => {
        await openAboutReady(page);

        await expect(page).toHaveScreenshot('about-full.png', {
            fullPage: true,
            animations: 'disabled',
        });
    });
});
