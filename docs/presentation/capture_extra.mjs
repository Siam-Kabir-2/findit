import { chromium } from 'playwright';
import path from 'path';
import { fileURLToPath } from 'url';
import fs from 'fs';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const outDir = path.join(__dirname, 'screenshots');
fs.mkdirSync(outDir, { recursive: true });
const BASE = 'http://127.0.0.1:8000';

async function shot(page, name, fullPage = true) {
  await page.waitForTimeout(400);
  await page.screenshot({ path: path.join(outDir, `${name}.png`), fullPage, animations: 'disabled' });
  console.log('saved', name);
}

async function main() {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({ viewport: { width: 1440, height: 900 }, deviceScaleFactor: 1.2 });
  const page = await context.newPage();

  // Filtered browse
  await page.goto(BASE + '/items?type=LOST', { waitUntil: 'networkidle' });
  await shot(page, '21-browse-lost', false);
  await page.goto(BASE + '/items?type=FOUND', { waitUntil: 'networkidle' });
  await shot(page, '22-browse-found', false);

  // Login as John
  await page.goto(BASE + '/login', { waitUntil: 'networkidle' });
  await page.fill('input[name="email"]', 'john.smith@university.edu');
  await page.fill('input[name="password"]', 'pass_john123');
  await Promise.all([
    page.waitForNavigation({ waitUntil: 'networkidle' }).catch(() => {}),
    page.click('button[type="submit"]'),
  ]);
  await page.waitForTimeout(600);

  // Find an item detail with claim form (not own item ideally)
  await page.goto(BASE + '/items', { waitUntil: 'networkidle' });
  const links = page.locator('a[href*="/items/"]');
  const count = await links.count();
  for (let i = 0; i < Math.min(count, 8); i++) {
    const href = await links.nth(i).getAttribute('href');
    if (!href || !/\/items\/\d+/.test(href)) continue;
    await page.goto(BASE + href.replace(BASE, ''), { waitUntil: 'networkidle' });
    const form = page.locator('textarea[name="claim_message"], #claim_message');
    if (await form.count()) {
      await shot(page, '23-claim-form');
      // Fill but don't necessarily submit if validation might fail
      await form.fill('This looks like my item. I can describe unique marks.');
      const proof = page.locator('textarea[name="proof_description"], #proof_description');
      if (await proof.count()) await proof.fill('Blue case, scratched corner, sticker on back.');
      await shot(page, '24-claim-form-filled');
      break;
    }
  }

  await shot(page, '09-my-claims'); // refresh later
  await page.goto(BASE + '/my-claims', { waitUntil: 'networkidle' });
  await shot(page, '09-my-claims');

  // Admin claims with pending filter + items
  await page.goto(BASE + '/admin/login', { waitUntil: 'networkidle' });
  await page.fill('input[name="email"]', 'admin.one@university.edu');
  await page.fill('input[name="password"]', 'admin_pass_secure_001');
  await Promise.all([
    page.waitForNavigation({ waitUntil: 'networkidle' }).catch(() => {}),
    page.click('button[type="submit"]'),
  ]);
  await page.waitForTimeout(600);
  await page.goto(BASE + '/admin/claims?status=PENDING', { waitUntil: 'networkidle' });
  await shot(page, '25-admin-claims-pending');
  await page.goto(BASE + '/admin/items', { waitUntil: 'networkidle' });
  await shot(page, '14-admin-items');

  // GitHub repo
  try {
    await page.goto('https://github.com/Siam-Kabir-2/findit', { waitUntil: 'domcontentloaded', timeout: 45000 });
    await page.waitForTimeout(2000);
    await shot(page, '30-github-repo', false);
  } catch (e) {
    console.log('github skip', e.message);
  }

  await browser.close();
  console.log('done');
}

main().catch((e) => { console.error(e); process.exit(1); });
