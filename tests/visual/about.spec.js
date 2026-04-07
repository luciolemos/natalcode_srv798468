const { test, expect } = require('@playwright/test');

async function openAboutReady(page)
{
    await page.goto('/quem-somos');
    await page.waitForLoadState('domcontentloaded');
    await page.locator('.nc-footer').waitFor();
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
