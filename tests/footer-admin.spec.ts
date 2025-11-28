import { test, expect } from '@playwright/test';

const ADMIN_URL = process.env.ADMIN_URL || 'http://theme.dev/wp-admin';
const ADMIN_USER = process.env.ADMIN_USER || 'admin';
const ADMIN_PASS = process.env.ADMIN_PASS || 'password';

// Reuse helper login function from other tests if present
async function adminLogin(page) {
  await page.goto(ADMIN_URL);
  await page.fill('input#user_login', ADMIN_USER);
  await page.fill('input#user_pass', ADMIN_PASS);
  await page.click('input#wp-submit');
  await page.waitForNavigation({ waitUntil: 'networkidle' });
}

test.describe('Footer admin options', () => {
  test('preview, apply and restore backups', async ({ page }) => {
    await adminLogin(page);
    await page.goto(`${ADMIN_URL}/admin.php?page=ross-theme-footer`);
    if (!page.url().includes('page=ross-theme-footer')) {
      throw new Error('Not on Footer admin page');
    }

    // Select a template and ensure preview updates
    await page.waitForSelector('input[name="ross_theme_footer_options[footer_template]"]');
    await page.click('input[name="ross_theme_footer_options[footer_template]"][value="template2"]');
    await page.click('#ross-preview-template');
    await expect(page.locator('#ross-template-preview')).toBeVisible();
    await expect(page.locator('#ross-template-preview')).toContainText('E-commerce');

    // Apply template
    await page.click('#ross-apply-template');
    // Confirm dialog appears then confirm
    await page.waitForSelector('#ross-footer-apply-notice, .notice-success, .notice');
    // Wait and ensure backups list contains a new row
    await page.waitForSelector('#ross-footer-backups table');
    const rows = await page.$$eval('#ross-footer-backups table tbody tr', trs => trs.length);
    expect(rows).toBeGreaterThanOrEqual(1);

    // Restore using first restore button in the table
    await page.click('#ross-footer-backups .ross-restore-backup');
    // Confirm restore dialog (page will reload)
    await page.waitForNavigation({ waitUntil: 'networkidle' });

    // Backups table should still exist after reload
    await page.waitForSelector('#ross-footer-backups');
  });
});
