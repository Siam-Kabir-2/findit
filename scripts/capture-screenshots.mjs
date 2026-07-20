import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const ROOT = path.resolve(__dirname, '..');
const OUT = path.join(ROOT, 'docs/presentation/screenshots');
const BASE = process.env.FINDIT_URL || 'http://127.0.0.1:8000';

const USER = {
  email: 'john.smith@university.edu',
  password: 'pass_john123',
};
const ADMIN = {
  email: 'admin.one@university.edu',
  password: 'admin_pass_secure_001',
};

fs.mkdirSync(OUT, { recursive: true });

async function settle(page, ms = 600) {
  await page.waitForLoadState('networkidle').catch(() => {});
  // Reveal animations start hidden — force visible for screenshots.
  await page.addStyleTag({
    content: '.reveal{opacity:1!important;transform:none!important;transition:none!important;}',
  }).catch(() => {});
  await page.evaluate(() => {
    document.querySelectorAll('.reveal').forEach((el) => el.classList.add('visible'));
  }).catch(() => {});
  await page.waitForTimeout(ms);
}

async function saveShot(page, name, fullPage) {
  const tmpDir = path.join(ROOT, 'storage/app/screenshot-tmp');
  fs.mkdirSync(tmpDir, { recursive: true });
  const tmp = path.join(tmpDir, name);
  const finalPath = path.join(OUT, name);

  await page.screenshot({ path: tmp, fullPage });

  for (let i = 0; i < 6; i += 1) {
    try {
      fs.copyFileSync(tmp, finalPath);
      fs.unlinkSync(tmp);
      console.log('saved', name);
      return;
    } catch {
      await new Promise((r) => setTimeout(r, 250 * (i + 1)));
    }
  }

  // Last resort: keep temp file name if destination stays locked
  fs.renameSync(tmp, finalPath);
  console.log('saved', name);
}

async function full(page, name) {
  await saveShot(page, name, true);
}

async function view(page, name) {
  await saveShot(page, name, false);
}

async function loginUser(page) {
  await page.goto(`${BASE}/login`, { waitUntil: 'domcontentloaded' });
  await page.fill('input[name="email"]', USER.email);
  await page.fill('input[name="password"]', USER.password);
  await Promise.all([
    page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
    page.click('button[type="submit"]'),
  ]);
  await settle(page, 400);
}

async function logoutUser(page) {
  const logout = page.locator('form[action*="logout"] button, form[action*="logout"] input[type="submit"]').first();
  if (await logout.count()) {
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }).catch(() => {}),
      logout.click(),
    ]);
  } else {
    await page.goto(`${BASE}/`, { waitUntil: 'domcontentloaded' });
  }
  await settle(page, 300);
}

async function loginAdmin(page) {
  await page.goto(`${BASE}/admin/login`, { waitUntil: 'domcontentloaded' });
  await page.fill('input[name="email"]', ADMIN.email);
  await page.fill('input[name="password"]', ADMIN.password);
  await Promise.all([
    page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
    page.click('button[type="submit"]'),
  ]);
  await settle(page, 500);
}

async function main() {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({
    viewport: { width: 1440, height: 900 },
    deviceScaleFactor: 1,
  });
  const page = await context.newPage();

  // Public pages
  await page.goto(`${BASE}/`, { waitUntil: 'domcontentloaded' });
  await settle(page, 900);
  await view(page, '20-home-viewport.png');
  await full(page, '01-home.png');

  await page.goto(`${BASE}/items`, { waitUntil: 'domcontentloaded' });
  await settle(page, 800);
  await view(page, '19-browse-viewport.png');
  await full(page, '02-browse-items.png');

  await page.goto(`${BASE}/items?type=LOST`, { waitUntil: 'domcontentloaded' });
  await settle(page, 600);
  await full(page, '21-browse-lost.png');

  await page.goto(`${BASE}/items?type=FOUND`, { waitUntil: 'domcontentloaded' });
  await settle(page, 600);
  await full(page, '22-browse-found.png');

  // Prefer a claimable item John does not own (often item 2 / iPhone)
  let detailUrl = `${BASE}/items/1`;
  try {
    const res = await page.request.get(`${BASE}/items`);
    const html = await res.text();
    const match = html.match(/\/items\/(\d+)/);
    if (match) detailUrl = `${BASE}/items/${match[1]}`;
  } catch {
    // keep default
  }

  await page.goto(detailUrl, { waitUntil: 'domcontentloaded' });
  await settle(page, 1200);
  await full(page, '03-item-detail.png');

  await page.goto(`${BASE}/login`, { waitUntil: 'domcontentloaded' });
  await settle(page, 500);
  await full(page, '04-user-login.png');

  await page.goto(`${BASE}/register`, { waitUntil: 'domcontentloaded' });
  await settle(page, 500);
  await full(page, '05-register.png');

  // Authenticated user pages
  await loginUser(page);

  await page.goto(`${BASE}/dashboard`, { waitUntil: 'domcontentloaded' });
  await settle(page, 700);
  await full(page, '06-user-dashboard.png');

  await page.goto(`${BASE}/items/create`, { waitUntil: 'domcontentloaded' });
  await settle(page, 1000);
  await full(page, '07-report-item.png');

  await page.goto(`${BASE}/my-items`, { waitUntil: 'domcontentloaded' });
  await settle(page, 700);
  await full(page, '08-my-items.png');

  await page.goto(`${BASE}/my-claims`, { waitUntil: 'domcontentloaded' });
  await settle(page, 700);
  await full(page, '09-my-claims.png');

  // Claim form on an item John does not own
  await page.goto(`${BASE}/items/2`, { waitUntil: 'domcontentloaded' });
  await settle(page, 1000);
  await full(page, '23-claim-form.png');

  const message = page.locator('#claim_message');
  if (await message.count()) {
    await message.fill('This looks like my item — I can verify ownership.');
    await page.locator('#proof_description').fill('Unique scratch near the camera and my Apple ID receipt.');
    await settle(page, 400);
    await full(page, '24-claim-form-filled.png');
  } else {
    // Fallback: copy empty claim shot if form unavailable
    fs.copyFileSync(path.join(OUT, '23-claim-form.png'), path.join(OUT, '24-claim-form-filled.png'));
    console.log('saved 24-claim-form-filled.png (fallback copy)');
  }

  await logoutUser(page);

  // Admin pages
  await page.goto(`${BASE}/admin/login`, { waitUntil: 'domcontentloaded' });
  await settle(page, 500);
  await full(page, '10-admin-login.png');

  await loginAdmin(page);

  await page.goto(`${BASE}/admin/dashboard`, { waitUntil: 'domcontentloaded' });
  await settle(page, 800);
  await full(page, '11-admin-dashboard.png');

  await page.goto(`${BASE}/admin/claims`, { waitUntil: 'domcontentloaded' });
  await settle(page, 700);
  await full(page, '12-admin-claims.png');

  await page.goto(`${BASE}/admin/claims?status=PENDING`, { waitUntil: 'domcontentloaded' });
  await settle(page, 700);
  await full(page, '13-admin-claims-pending.png');
  await full(page, '25-admin-claims-pending.png');

  await page.goto(`${BASE}/admin/items`, { waitUntil: 'domcontentloaded' });
  await settle(page, 700);
  await full(page, '14-admin-items.png');

  await page.goto(`${BASE}/admin/users`, { waitUntil: 'domcontentloaded' });
  await settle(page, 700);
  await full(page, '15-admin-users.png');

  await page.goto(`${BASE}/admin/categories`, { waitUntil: 'domcontentloaded' });
  await settle(page, 700);
  await full(page, '16-admin-categories.png');

  await page.goto(`${BASE}/admin/locations`, { waitUntil: 'domcontentloaded' });
  await settle(page, 1400);
  await full(page, '17-admin-locations.png');

  await page.goto(`${BASE}/admin/audit`, { waitUntil: 'domcontentloaded' });
  await settle(page, 800);
  await full(page, '18-admin-audit.png');

  await browser.close();
  console.log('Done. Screenshots in', OUT);
}

main().catch((err) => {
  console.error(err);
  process.exit(1);
});
