# Ross Theme Admin E2E Tests (Playwright)

This directory adds automated Playwright tests that verify the Footer CTA admin subtab behavior: that each CTA subtab shows the expected fields and hides unrelated fields.

Prerequisites:
- Node.js (>=18 recommended)
- npm or yarn
- A WordPress instance with the Ross Theme installed at `http://theme.dev` (or custom URL) and an administrator user.

Files added:
- `package.json` — devDependencies and scripts to run Playwright tests
- `playwright.config.ts` — Playwright test runner configuration
- `tests/cta-admin.spec.ts` — Playwright test that logs into WP admin, navigates to Footer > CTA, and asserts visibility per subtab

Environment variables (optional):
- `ADMIN_URL` — base WP admin URL (default: `http://theme.dev/wp-admin`)
- `ADMIN_USER` — admin username (default `admin`)
- `ADMIN_PASS` — admin user password (default `password`)

Install and run:

```powershell
# From the theme directory
cd c:\xampp\htdocs\theme.dev\wp-content\themes\rosstheme
npm install
npx playwright install
npm run test:e2e
```

Helpful commands:
- Run tests in headed mode for debugging: `npm run test:e2e:headed`
- Run in debug mode with Playwright inspector: `npm run test:e2e:debug` (requires headful browser)

CI (GitHub Actions): `/.github/workflows/playwright.yml` (if created) can be used to run the test suite on push or PR by setting ADMIN_URL/ADMIN_USER/ADMIN_PASS secrets.

If you need help adapting the tests for a different admin URL, update the environment variables in your CI or local environment accordingly.
