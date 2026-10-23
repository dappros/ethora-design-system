import { chromium } from 'playwright';

const OUT = process.argv[2];
// BASE=https://ethora.com to shoot the live site; ONLY=faq,short-answer to re-shoot a subset
const base = process.env.BASE || 'http://ethora.local';
const only = process.env.ONLY ? process.env.ONLY.split(',') : null;
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
  { url: '/healthcare/',                  name: 'cards-carousel',        sel: '.cc-section',        i: 0 },
  { url: '/healthcare/',                  name: 'comparison',            sel: '.cmp-section',       i: 0 },
  { url: '/healthcare/',                  name: 'feature-spotlight',     sel: '.fs-section',        i: 0 },
  { url: '/self-hosted-chat-server-aws/', name: 'bento-deployment',      sel: 'section:has(.shs-bento-grid)', i: 0 },
  { url: '/self-hosted-chat-server-aws/', name: 'pricing-cards',         sel: '.ppc',               i: 0 },
  { url: '/self-hosted-chat-server-aws/', name: 'testimonials-carousel', sel: '.tcar',              i: 0 },
  { url: '/self-hosted-chat-server-aws/', name: 'case-studies',          sel: '.case-studies',      i: 0 },
  { url: '/pricing/',                     name: 'cta-dark',              sel: '.cta-dark',          i: 0 },
  { url: '/deadsimplechat-alternative/',  name: 'deployment',            sel: '.dep-section',       i: 0 },
  { url: '/deadsimplechat-alternative/',  name: 'compliance-cards',      sel: '.cmpl-section',      i: 0 },
  { url: '/deadsimplechat-alternative/',  name: 'stats',                 sel: '.sb-section',        i: 0 },
  { url: '/crisp-alternative/',           name: 'short-answer',          sel: '.shs-sa',            i: 0 },
  { url: '/crisp-alternative/',           name: 'migration-steps',       sel: '.shs-ms',            i: 0 },
  { url: '/ai-sdk/mcp-server/',           name: 'faq',                   sel: 'section.faq',        i: 0 },
  { url: '/sendbird-alternative/',        name: 'video-embed',           sel: '.video-embed-section', i: 0 },
  { url: '/',                             name: 'hero-chat-demo',        sel: 'section:has(#demo)',     i: 0 },
  { url: '/',                             name: 'onboarding-call',       sel: '.obc',               i: 0 },
  { url: '/glossary/a2a-protocol/',       name: 'glossary-single',       sel: '.gterm',             i: 0 },
  { url: '/case-study-fan-community-chat-app/', name: 'case-study-prose', sel: '.cs-prose',         i: 0 },
];

const browser = await chromium.launch({ channel: 'chrome' });
const page = await browser.newPage({ viewport: { width: 1280, height: 900 }, deviceScaleFactor: 2 });
// hide the fixed header and the site chat widget so they don't overlap element screenshots
const hideHeader = () => page.addStyleTag({ content: '.header, #chat-widget{display:none !important;}' }).catch(() => {});

let last = '';
for (const s of shots) {
  if (only && !only.includes(s.name)) continue;
  try {
    if (s.url !== last) {
      // the live site never goes network-idle (analytics, embeds), so fall back to 'load'
      await page.goto(base + s.url, { waitUntil: 'networkidle', timeout: 20000 })
        .catch(() => page.goto(base + s.url, { waitUntil: 'load', timeout: 45000 }));
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
    // hide floating widgets (site chat bubble, cookie bar) and swap YouTube iframes, which
    // render black in headless Chrome, for their poster image
    await el.evaluate((target) => {
      for (const n of document.querySelectorAll('body *')) {
        if (getComputedStyle(n).position === 'fixed' && !n.contains(target) && !target.contains(n)) n.style.visibility = 'hidden';
      }
      for (const f of target.querySelectorAll('iframe[src*="youtube"]')) {
        const id = (f.src.match(/embed\/([\w-]+)/) || [])[1];
        if (!id) continue;
        const img = document.createElement('img');
        img.src = `https://i.ytimg.com/vi/${id}/maxresdefault.jpg`;
        img.style.cssText = 'position:absolute;inset:0;width:100%;height:100%;object-fit:cover';
        f.replaceWith(img);
      }
    });
    await page.waitForTimeout(1200);
    await el.screenshot({ path: `${OUT}/${s.name}.png` });
    console.log(`OK   ${s.name}.png`);
  } catch (e) {
    console.log(`FAIL ${s.name} — ${e.message.split('\n')[0]}`);
  }
}
await browser.close();
