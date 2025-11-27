import { PlaywrightTestConfig } from '@playwright/test';

const config: PlaywrightTestConfig = {
  testDir: './tests',
  timeout: 30 * 1000,
  expect: { timeout: 5000 },
  fullyParallel: true,
  retries: 0,
  use: {
    headless: true,
    viewport: { width: 1200, height: 900 },
    ignoreHTTPSErrors: true,
    video: 'off',
  },
};

export default config;
