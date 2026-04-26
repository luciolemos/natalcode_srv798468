const { defineConfig, devices } = require('@playwright/test');

module.exports = defineConfig({
  testDir: './tests/visual',
  timeout: 30_000,
  expect: {
    timeout: 10_000,
    toHaveScreenshot: {
      maxDiffPixelRatio: 0.02,
    },
  },
  fullyParallel: true,
  reporter: [['list']],
  use: {
    baseURL: 'http://localhost:8080',
    trace: 'on-first-retry',
    screenshot: 'only-on-failure',
    video: 'off',
    deviceScaleFactor: 1,
    launchOptions: {
      args: [
        '--disable-lcd-text',
        '--disable-font-subpixel-positioning',
        '--font-render-hinting=none',
      ],
    },
  },
  projects: [
    {
      name: 'mobile',
      expect: {
        toHaveScreenshot: {
          maxDiffPixelRatio: 0.08,
        },
      },
      use: {
        browserName: 'chromium',
        viewport: { width: 390, height: 844 },
        isMobile: true,
        hasTouch: true,
      },
    },
    {
      name: 'tablet',
      expect: {
        toHaveScreenshot: {
          maxDiffPixelRatio: 0.08,
        },
      },
      use: {
        browserName: 'chromium',
        viewport: { width: 820, height: 1180 },
        isMobile: false,
        hasTouch: true,
      },
    },
    {
      name: 'desktop',
      use: {
        browserName: 'chromium',
        viewport: { width: 1440, height: 900 },
      },
    },
  ],
  webServer: {
    command: 'composer start',
    url: 'http://localhost:8080',
    reuseExistingServer: true,
    env: {
      ...process.env,
      APP_DEFAULT_THEME: 'blue',
      APP_DEFAULT_MODE: 'light',
      APP_DEFAULT_DARK_INTENSITY: 'neutral',
      APP_ASSET_VERSION: '1',
      APP_WHATSAPP_NUMBER: '5584996360721',
      APP_WHATSAPP_MESSAGE: 'Oi! Quero conversar sobre o projeto de uma landing page com a NatalCode.',
      APP_SOCIAL_INSTAGRAM_URL: 'https://www.instagram.com/natalcodern/',
      APP_SOCIAL_INSTAGRAM_LABEL: 'Instagram',
      APP_SOCIAL_FACEBOOK_URL: 'https://www.facebook.com/natalcodern/',
      APP_SOCIAL_FACEBOOK_LABEL: 'Facebook',
      APP_SOCIAL_GITHUB_URL: 'https://github.com/natalcode',
      APP_SOCIAL_GITHUB_LABEL: 'GitHub',
    },
    timeout: 120_000,
  },
});
