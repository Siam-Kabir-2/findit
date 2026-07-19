import { chromium } from 'playwright';
import path from 'path';
import { fileURLToPath } from 'url';
import fs from 'fs';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const outDir = path.join(__dirname, 'screenshots');
fs.mkdirSync(outDir, { recursive: true });

const BASE = 'http://127.0.0.1:8000';

async function shot(page, name, fullPage = true) {
  await page.waitForTimeout(500);
  await page.screenshot({
    path: path.join(outDir, `${name}.png`),
    fullPage,
    animations: 'disabled',
  });
  console.log('saved', name);
}

async function main() {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({
    viewport: { width: 1440, height: 900 },
    deviceScaleFactor: 1.25,
  });
  const page = await context.newPage();

  // Public pages
  await page.goto(BASE + '/', { waitUntil: 'networkidle' });
  await shot(page, '01-home');

  await page.goto(BASE + '/items', { waitUntil: 'networkidle' });
  await shot(page, '02-browse-items');

  // Try open first item detail if any
  const firstItem = page.locator('a[href*="/items/"]').first();
  if (await firstItem.count()) {
    await firstItem.click();
    await page.waitForLoadState('networkidle');
    await shot(page, '03-item-detail');
  }

  await page.goto(BASE + '/login', { waitUntil: 'networkidle' });
  await shot(page, '04-user-login');

  await page.goto(BASE + '/register', { waitUntil: 'networkidle' });
  await shot(page, '05-register');

  // User login
  await page.goto(BASE + '/login', { waitUntil: 'networkidle' });
  await page.fill('input[name="email"], input[type="email"]', 'john.smith@university.edu');
  await page.fill('input[name="password"], input[type="password"]', 'pass_john123');
  await Promise.all([
    page.waitForNavigation({ waitUntil: 'networkidle' }).catch(() => {}),
    page.click('button[type="submit"], input[type="submit"]'),
  ]);
  await page.waitForTimeout(800);
  await shot(page, '06-user-dashboard');

  await page.goto(BASE + '/items/create', { waitUntil: 'networkidle' });
  await shot(page, '07-report-item');

  await page.goto(BASE + '/my-items', { waitUntil: 'networkidle' }).catch(() => {});
  await page.waitForTimeout(400);
  if (page.url().includes('my-items') || page.url().includes('items')) {
    await shot(page, '08-my-items');
  }

  await page.goto(BASE + '/my-claims', { waitUntil: 'networkidle' }).catch(() => {});
  await page.waitForTimeout(400);
  await shot(page, '09-my-claims');

  // Logout user if form exists
  const logout = page.locator('form[action*="logout"] button, button:has-text("Logout"), a:has-text("Logout")').first();
  if (await logout.count()) {
    await logout.click().catch(() => {});
    await page.waitForTimeout(400);
  }

  // Admin
  await page.goto(BASE + '/admin/login', { waitUntil: 'networkidle' });
  await shot(page, '10-admin-login');

  await page.fill('input[name="email"], input[type="email"]', 'admin.one@university.edu');
  await page.fill('input[name="password"], input[type="password"]', 'admin_pass_secure_001');
  await Promise.all([
    page.waitForNavigation({ waitUntil: 'networkidle' }).catch(() => {}),
    page.click('button[type="submit"], input[type="submit"]'),
  ]);
  await page.waitForTimeout(800);
  await shot(page, '11-admin-dashboard');

  await page.goto(BASE + '/admin/claims', { waitUntil: 'networkidle' });
  await shot(page, '12-admin-claims');

  await page.goto(BASE + '/admin/claims?status=PENDING', { waitUntil: 'networkidle' });
  await shot(page, '13-admin-claims-pending');

  await page.goto(BASE + '/admin/items', { waitUntil: 'networkidle' });
  await shot(page, '14-admin-items');

  await page.goto(BASE + '/admin/users', { waitUntil: 'networkidle' });
  await shot(page, '15-admin-users');

  await page.goto(BASE + '/admin/categories', { waitUntil: 'networkidle' });
  await shot(page, '16-admin-categories');

  await page.goto(BASE + '/admin/locations', { waitUntil: 'networkidle' });
  await shot(page, '17-admin-locations');

  await page.goto(BASE + '/admin/audit', { waitUntil: 'networkidle' });
  await shot(page, '18-admin-audit');

  // Browse with filters viewport crop hero
  await page.goto(BASE + '/items', { waitUntil: 'networkidle' });
  await shot(page, '19-browse-viewport', false);

  await page.goto(BASE + '/', { waitUntil: 'networkidle' });
  await shot(page, '20-home-viewport', false);

  await browser.close();
  console.log('done');
}

main().catch((e) => {
  console.error(e);
  process.exit(1);
});
