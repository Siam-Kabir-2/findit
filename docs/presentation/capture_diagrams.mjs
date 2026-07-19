import { chromium } from 'playwright';
import path from 'path';
import { fileURLToPath } from 'url';
import fs from 'fs';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const outDir = path.join(__dirname, 'screenshots');
const html = path.join(__dirname, 'diagrams.html');

async function crop(page, selector, name) {
  const el = page.locator(selector);
  await el.waitFor({ state: 'visible', timeout: 60000 });
  await page.waitForTimeout(1500);
  await el.screenshot({ path: path.join(outDir, `${name}.png`) });
  console.log('saved', name);
}

async function main() {
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage({ viewport: { width: 1600, height: 1200 }, deviceScaleFactor: 1.5 });
  await page.goto('file:///' + html.replace(/\\/g, '/'), { waitUntil: 'networkidle' });
  await page.waitForTimeout(2500);
  await crop(page, '#er', '27-er-diagram');
  await crop(page, '#schema', '28-schema-diagram');
  await crop(page, '#arch', '26-architecture');
  await crop(page, '#workflow', '29-workflow');
  await browser.close();
  console.log('diagrams done');
}

main().catch((e) => { console.error(e); process.exit(1); });
