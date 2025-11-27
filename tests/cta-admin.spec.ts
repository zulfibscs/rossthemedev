import { test, expect } from '@playwright/test';
import type { Page } from '@playwright/test';

// These env vars can be used to override the defaults in CI/local
const ADMIN_URL = process.env.ADMIN_URL || 'http://theme.dev/wp-admin';
const ADMIN_USER = process.env.ADMIN_USER || 'admin';
const ADMIN_PASS = process.env.ADMIN_PASS || 'password';
const FOOTER_PAGE = `${ADMIN_URL}/admin.php?page=ross-theme-footer`;

async function loginIfNeeded(page: Page) {
  await page.goto(ADMIN_URL);
  // if logged in, WP admin will redirect to /wp-admin/ and not show the login form
  if (page.url().includes('wp-login.php')) {
    await page.fill('input#user_login', ADMIN_USER);
    await page.fill('input#user_pass', ADMIN_PASS);
    await page.click('input#wp-submit');
    // safe wait for admin menu to appear
    await page.waitForSelector('#adminmenu', { timeout: 10000 });
  } else {
    // If redirected to admin area still check we have the admin menu
    await page.waitForSelector('#adminmenu', { timeout: 10000 });
  }
}

// Helper: Switch to a registered tab
async function openFooterTab(page: Page, tabName = 'tab-cta') {
  // Click the Footer Options admin menu if not already
  if (!page.url().includes('page=ross-theme-footer')) {
    await page.goto(FOOTER_PAGE);
  }
  // Click CTA main tab if needed
  await page.waitForSelector('.ross-tab-btn[data-tab="cta"]');
  await page.click('.ross-tab-btn[data-tab="cta"]');
  await page.waitForSelector(`#${tabName}.active`, { timeout: 5000 });
}

// Map each CTA subtab to at least one representative field that lives in there
const ctaSubtabSelectors: Record<string, { show: string[]; hide: string[] }>= {
  'ross_footer_cta_visibility': {
    show: ['input[name="ross_theme_footer_options[enable_footer_cta]"]'],
    hide: ['input[name="ross_theme_footer_options[cta_title]"]']
  },
  'ross_footer_cta_content': {
    show: ['input[name="ross_theme_footer_options[cta_title]"]', 'input[name="ross_theme_footer_options[cta_button_text]"]'],
    hide: ['input[name="ross_theme_footer_options[cta_alignment]"]']
  },
  'ross_footer_cta_layout': {
    show: ['select[name="ross_theme_footer_options[cta_alignment]"]', 'select[name="ross_theme_footer_options[cta_layout_direction]"]'],
    hide: ['input[name="ross_theme_footer_options[cta_bg_color]"]']
  },
  'ross_footer_cta_styling': {
    show: ['input[name="ross_theme_footer_options[cta_bg_color]"]', 'select[name="ross_theme_footer_options[cta_bg_type]"]'],
    hide: ['input[name="ross_theme_footer_options[cta_padding_top]"]']
  },
  'ross_footer_cta_spacing': {
    show: ['input[name="ross_theme_footer_options[cta_padding_top]"]', 'input[name="ross_theme_footer_options[cta_margin_top]"]'],
    hide: ['select[name="ross_theme_footer_options[cta_animation]"]']
  },
  'ross_footer_cta_animation': {
    show: ['select[name="ross_theme_footer_options[cta_animation]"]', 'input[name="ross_theme_footer_options[cta_anim_delay]"]'],
    hide: ['input[name="ross_theme_footer_options[enable_custom_cta]"]']
  },
  'ross_footer_cta_advanced': {
    show: ['input[name="ross_theme_footer_options[enable_custom_cta]"]', 'textarea[name="ross_theme_footer_options[custom_cta_css]"]'],
    hide: ['input[name="ross_theme_footer_options[cta_title]"]']
  }
};

// Test: Each CTA subtab shows only its fields
// Quick check that wrappers are present and the default Visibility wrapper is visible
test('CTA wrapper sections exist and default to Visibility visible', async ({ page }: { page: Page }) => {
  await loginIfNeeded(page);
  await openFooterTab(page);
  // The default section visibility is ross_footer_cta_visibility; check its wrapper
  await page.waitForSelector('.ross-cta-section-wrapper[data-cta-section="ross_footer_cta_visibility"]');
  const wrapper = page.locator('.ross-cta-section-wrapper[data-cta-section="ross_footer_cta_visibility"]');
  await expect(wrapper).toBeVisible();
});

for (const sectionId of Object.keys(ctaSubtabSelectors)) {
  test(`CTA subtab ${sectionId} shows correct fields and hides unrelated fields`, async ({ page }: { page: Page }) => {
    await loginIfNeeded(page);
    await openFooterTab(page);

    // Ensure CTA tab content area is present
    await page.waitForSelector('.ross-cta-admin-form');

    // Click that subtab button
    const btnSelector = `.ross-cta-tab-btn[data-section="${sectionId}"]`;
    await page.click(btnSelector);

    // Small wait so DOM updates and wrapper toggling completes
    await page.waitForTimeout(100);

    // Verify fields that should be visible
    const shows = ctaSubtabSelectors[sectionId].show;
    for (const s of shows) {
      const ok = await page.isVisible(s).catch(()=>false);
      expect(ok, `Expected ${s} to be visible in ${sectionId}`).toBeTruthy();
    }

    // Verify fields that should be hidden
    const hides = ctaSubtabSelectors[sectionId].hide;
    for (const h of hides) {
      const isVisible = await page.isVisible(h).catch(()=>false);
      expect(isVisible, `Expected ${h} to be hidden in ${sectionId}`).toBeFalsy();
    }
  });
}

// Extra test: navigation between subtabs should preserve show/hide states
test('Switching CTA subtab toggles visibility correctly', async ({ page }: { page: Page }) => {
  await loginIfNeeded(page);
  await openFooterTab(page);

  await page.click('.ross-cta-tab-btn[data-section="ross_footer_cta_visibility"]');
  await page.waitForTimeout(50);
  await expect(page.locator('input[name="ross_theme_footer_options[enable_footer_cta]"]')).toBeVisible();

  await page.click('.ross-cta-tab-btn[data-section="ross_footer_cta_styling"]');
  await page.waitForTimeout(50);
  await expect(page.locator('input[name="ross_theme_footer_options[cta_bg_color]"]')).toBeVisible();
  await expect(page.locator('input[name="ross_theme_footer_options[enable_footer_cta]"]')).toBeHidden();
});

// Copyright admin tests
test('Copyright admin: default fields visible, preview updates, and custom footer toggles', async ({ page }: { page: Page }) => {
  await loginIfNeeded(page);
  await openFooterTab(page);
  // Switch to copyright tab
  await page.click('.ross-tab-btn[data-tab="copyright"]');
  await page.waitForSelector('#ross-copyright-preview');

  // Default: custom disabled -> ensure default fields visible and preview uses placeholders
  const enableCustom = await page.isVisible('input[name="ross_theme_footer_options[enable_custom_footer]"]');
  expect(enableCustom).toBeTruthy();

  // Set copyright text to use placeholders
  await page.fill('textarea[name="ross_theme_footer_options[copyright_text]"]', '© {year} {site_name} — test');
  // Wait for the preview to update
  await page.waitForTimeout(150);
  const previewHtml = await page.locator('#ross-copyright-preview').innerHTML();
  const currentYear = new Date().getFullYear().toString();
  expect(previewHtml).toContain(currentYear);
  expect(previewHtml).toContain(await page.evaluate(()=> document.title));

  // Change font size and assert inline style updated in preview
  await page.fill('input[name="ross_theme_footer_options[copyright_font_size]"]', '22');
  await page.waitForTimeout(150);
  const previewStyle = await page.locator('#ross-copyright-preview .copyright-live').getAttribute('style');
  expect(previewStyle).toContain('font-size: 22px');

  // Enable custom footer -> default fields should hide and custom html field should appear
  await page.check('input[name="ross_theme_footer_options[enable_custom_footer]"]');
  await page.waitForTimeout(150);
  // Expect default copyright text field to be hidden
  expect(await page.isVisible('textarea[name="ross_theme_footer_options[copyright_text]"]')).toBeFalsy();
  // custom html field should be visible
  expect(await page.isVisible('textarea[name="ross_theme_footer_options[custom_footer_html]"]')).toBeTruthy();

  // Put custom HTML and expect the preview to render it
  await page.fill('textarea[name="ross_theme_footer_options[custom_footer_html]"]', '<strong>My Custom Footer</strong>');
  await page.waitForTimeout(150);
  const customPreviewHtml = await page.locator('#ross-copyright-preview').innerHTML();
  expect(customPreviewHtml).toContain('My Custom Footer');
});
