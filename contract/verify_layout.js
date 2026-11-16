import { chromium } from 'playwright';
import fs from 'fs';

const TARGET_URL = process.env.APP_URL || 'http://127.0.0.1:8000';
const SCREENSHOT_PATH = process.env.SCREENSHOT_PATH || 'artifacts/actual.png';

async function run() {
  const browser = await chromium.launch();
  const page = await browser.newPage({
    viewport: { width: 390, height: 844 },
    deviceScaleFactor: 2,
  });

  const report = {
    url: TARGET_URL,
    passed: false,
    checks: [],
    errors: [],
  };

  try {
    // 1. Page Identity & HTTP Status Check First
    const response = await page.goto(TARGET_URL, { waitUntil: 'networkidle', timeout: 15000 });
    const status = response ? response.status() : 0;
    if (status !== 200) {
      throw new Error(`Expected HTTP 200, got ${status}`);
    }
    report.checks.push('HTTP 200 OK');

    const bodyText = await page.innerText('body');

    // 2. Check Core Text Elements from contract/layout.png
    const requiredStrings = [
      'London',
      '8°',
      'Drizzle',
      'H:8°',
      'L:5°',
      'HOURLY FORECAST',
      'Now',
      '10-DAY FORECAST',
      'Today',
    ];

    for (const text of requiredStrings) {
      if (!bodyText.includes(text)) {
        report.errors.push(`Missing required text: "${text}"`);
      } else {
        report.checks.push(`Found text: "${text}"`);
      }
    }

    // 3. Check Dark Theme Background (must not be default white)
    const bgColor = await page.evaluate(() => {
      return window.getComputedStyle(document.body).backgroundColor;
    });
    if (bgColor === 'rgb(255, 255, 255)' || bgColor === 'rgba(0, 0, 0, 0)') {
      report.errors.push(`Background is still default white/transparent (${bgColor}). Expected dark slate/navy.`);
    } else {
      report.checks.push(`Dark background applied (${bgColor})`);
    }

    // 4. Check Mobile Viewport Overflow
    const hasHorizontalOverflow = await page.evaluate(() => {
      return document.documentElement.scrollWidth > window.innerWidth;
    });
    if (hasHorizontalOverflow) {
      report.errors.push('Horizontal page overflow detected at 390px width.');
    } else {
      report.checks.push('No horizontal overflow at 390px');
    }

    // 5. Capture Screenshot for Vision Checker
    await page.screenshot({ path: SCREENSHOT_PATH, fullPage: true });
    report.checks.push(`Saved screenshot to ${SCREENSHOT_PATH}`);

    report.passed = report.errors.length === 0;
  } catch (err) {
    report.errors.push(err.message);
  } finally {
    await browser.close();
  }

  fs.writeFileSync('artifacts/verify_report.json', JSON.stringify(report, null, 2));
  console.log(JSON.stringify(report, null, 2));
  process.exit(report.passed ? 0 : 1);
}

run();
