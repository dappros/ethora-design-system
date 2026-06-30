import { chromium } from 'playwright';

const OUT = process.argv[2];
const base = 'http://ethora.local';
const shots = [
  { url: '/self-hosted-chat-server-aws/', name: 'hero',                  sel: '.ehero',             i: 0 },
  { url: '/self-hosted-chat-server-aws/', name: 'split-card',            sel: '.shs-split-section', i: 0 },
  { url: '/self-hosted-chat-server-aws/', name: 'split-card-reverse',    sel: '.shs-split-section', i: 1 },
  { url: '/healthcare/',                  name: 'split-card-dark',        sel: '.shs-split-section.is-dark', i: 0 },
  { url: '/self-hosted-chat-server-aws/', name: 'why',                   sel: '.shs-why .why-pin',  i: 0 },
  { url: '/self-hosted-chat-server-aws/', name: 'key-features',          sel: '.shs-kf-section',    i: 0 },
  { url: '/self-hosted-chat-server-aws/', name: 'key-features-reverse',  sel: '.shs-kf-section',    i: 1 },
  { url: '/self-hosted-chat-server-aws/', name: 'feature-cards',         sel: '.shs-fc-section',    i: 0 },
  { url: '/self-hosted-chat-server-aws/', name: 'link-cards',            sel: '.shs-lc-section',    i: 0 },
  { url: '/self-hosted-chat-server-aws/', name: 'bento-deployment',      sel: 'section:has(.shs-bento-grid)', i: 0 },
  { url: '/self-hosted-chat-server-aws/', name: 'pricing-cards',         sel: '.ppc',               i: 0 },
  { url: '/self-hosted-chat-server-aws/', name: 'testimonials-carousel', sel: '.tcar',              i: 0 },
  { url: '/self-hosted-chat-server-aws/', name: 'case-studies',          sel: '.case-studies',      i: 0 },
  { url: '/pricing/',                     name: 'cta-dark',              sel: '.cta-dark',          i: 0 },
];

const browser = await chromium.launch({ channel: 'chrome' });
const page = await browser.newPage({ viewport: { width: 1280, height: 900 }, deviceScaleFactor: 2 });
// hide the fixed header so it doesn't overlap the top of element screenshots
const hideHeader = () => page.addStyleTag({ content: '.header{display:none !important;}' }).catch(() => {});

let last = '';
for (const s of shots) {
  try {
    if (s.url !== last) {
      await page.goto(base + s.url, { waitUntil: 'networkidle', timeout: 30000 });
      // trigger lazy-loaded images
      await page.evaluate(async () => {
        for (let y = 0; y < document.body.scrollHeight; y += 600) { window.scrollTo(0, y); await new Promise(r => setTimeout(r, 60)); }
        window.scrollTo(0, 0);
      });
      await page.waitForTimeout(800);
      await hideHeader();
      last = s.url;
    }
    const el = page.locator(s.sel).nth(s.i);
    const n = await page.locator(s.sel).count();
    if (n <= s.i) { console.log(`SKIP ${s.name} — selector ${s.sel}[${s.i}] not found (${n})`); continue; }
    await el.scrollIntoViewIfNeeded();
    await page.waitForTimeout(400);
    await el.screenshot({ path: `${OUT}/${s.name}.png` });
    console.log(`OK   ${s.name}.png`);
  } catch (e) {
    console.log(`FAIL ${s.name} — ${e.message.split('\n')[0]}`);
  }
}
await browser.close();
