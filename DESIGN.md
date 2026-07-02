# Ethora Theme — Design System

Human-readable guide to the design rules. **The machine source of truth is
[`css/tokens.css`](css/tokens.css)** — one `:root`, loaded globally before every
other stylesheet. Edit a value there and it cascades across the whole site.

> **Rule #1 — use tokens, not literals.** When building or editing a page/section,
> reference `var(--token)` for every colour, size, spacing, radius, weight and
> width. Do **not** hardcode hex/px values — that's the ad-hoc, off-brand drift we're
> avoiding. If a value you need doesn't exist as a token, add it to `tokens.css`
> first, then use it.

The broader methodology, brand assets and component prompts live in the
`ethora-design` skill (`~/.claude/skills/ethora-design/`, see `readme.md` /
`SKILL.md`). This file is the in-repo summary.

---

## Brand essentials
- **Brand blue** `#0052CD` (`--primary`) on near-white canvas `--background`.
  Deep brand `#002398` (`--primary-dark`) for dark/CTA panels.
- Blue + grey does ~90% of the work. One bold accent per screen; keep the rest quiet.
- **Font (RULE): Open Sans only** — the single typeface across the whole site. Every
  font token (`--font-body/display/serif/sans/mono`) points to Open Sans, so any
  `var(--font-*)` renders Open Sans. **Do not introduce other typefaces** (no Newsreader,
  IBM Plex, etc.). Headings = heavier weight, not a different family.

---

## Colour
| Token | Value | Use |
|---|---|---|
| `--primary` | #0052CD | brand accent, links, buttons, eyebrows |
| `--primary-dark` | #002398 | dark / CTA panels, gradients |
| `--primary-hover` | #003FA6 | filled-button hover |
| `--ink` | #0E1A33 | headings / strongest text |
| `--text-body` | #46506A | default paragraph |
| `--text-body-soft` | #5A6478 | secondary text in cards |
| `--text-caption` | #6B7280 | captions / fine print (AA on white) |
| `--text-on-dark` | #CDD8EE | body text on dark/brand panels |
| `--accent-on-dark` | #8FB4FF | eyebrow/accent on dark panels |
| `--background` | #F6F8FC | page canvas |
| `--surface` / `--surface-alt` | #FFF / #F4F7FD | cards / alternating sections |
| `--tint` / `--tint-strong` | #EAF1FF / #F2F7FF | icon tiles / highlighted rows & table column |
| `--border` / `--hairline` / `--border-hair` | #E4E9F2 / #EAEEF5 / #EEF1F6 | card borders / dividers |
| `--green` / `--success-strong` | #22C55E / #0B6E3E | positive; "win" text on tint |
| `--red` `--orange` `--purple` `--yellow` | — | semantic |

Brand ramp `--brand-50 … --brand-950` is available for fine control.

**Brand gradient (RULE).** The only blue-fill background for a full-bleed section is the token
**`--gradient-brand`** = `linear-gradient(135deg, var(--brand-500) 0%, var(--brand-800) 100%)`.
Used by the statement band (`section-split-card` `dark`), cards carousel (`.cc-section`), stats
band (`.sb-section`) and feature-spotlight flagship. Never hand-write this gradient inline —
reference the token. (This is the *blue* section fill; near-black is never allowed, and dark/CTA
panels use the separate `.shs-dark` image treatment.)

## Typography
- **Family:** Open Sans only (`--font-*` all map to it). Differentiate with weight/size, not family.
- **Sizes (fixed):** `--fs-xs 12` · `--fs-sm 14` · `--fs-base 15` · `--fs-md 16` ·
  `--fs-lg 17` · `--fs-xl 18` · `--fs-2xl 20` · `--fs-3xl 24`.
- **Sizes (fluid headings):** `--fs-h3` · `--fs-h3-lg` · `--fs-h2` (standard section)
  · `--fs-h1` · `--fs-display` (hero) · `--fs-cta`.
- **Weights:** `--fw-regular 400` · `--fw-medium 500` · `--fw-semibold 600` · `--fw-bold 700`.
- **Line height:** `--lh-tight 1.08` · `--lh-heading 1.1` · `--lh-snug 1.2` ·
  `--lh-base 1.5` · `--lh-relaxed 1.65` · `--lh-loose 1.7`.
- **Tracking:** `--tracking-tight -.02em` (big serif) · `--tracking-wide .12em` /
  `--tracking-wider .14em` (eyebrows, uppercase).

## Spacing — strict 16px scale (RULE)
**Every** margin / padding / gap comes from a token on the **16px scale** — never
hand-type px (no `12px` / `22px` / `30px`). Steps:
`--space-16` · `--space-32` · `--space-48` · `--space-64` · `--space-80` · `--space-96`.
The **only** sub-16 value is `--space-8` — allowed strictly for inline micro-gaps
(icon ↔ label, chip padding), nothing else.
- **Section rhythm:** `--section-y` (clamp 48–96) · `--section-y-sm` (48–80).
- **Horizontal gutter = 24px (RULE):** `--section-x` is a fixed **24px**, identical to the
  header (`.container` padding `1rem 24px`), so section content lines up with the header on
  every width. Section wraps use `--content-max` (= `--container-xl − 2×--section-x` = **1152**)
  centred, which makes their edges match the 1200 header container exactly.

## Radius
`--radius-xs 8` · `-sm 10` · `-md 12` · `-lg 14` · `-xl 16` · `-2xl 18` (cards) ·
`-3xl 24` (big cards/modals) · `--radius-btn 12` (CTA buttons) · `--radius-pill 999`
(avatars, chips, toggles).

## Buttons (RULE)
The two CTAs — **Get started** and **Book a Call** — are the brand standard everywhere
(home, footer, all pages). Always:
- **Radius `--radius-btn` (12px)**, font Open Sans 600, padding ~`14px 28px`.
- **Primary (Get started):** `background: var(--primary)`, white text, no border.
  Hover → `var(--primary-dark)` + slight lift.
- **Outline (Book a Call):** transparent, `2px solid var(--primary)`, `var(--primary)` text.
  Hover → `var(--primary-light)`.
- On dark panels use the white / translucent variants, same radius.
Never invent pill/other-radius CTAs. Reuse `.btn .btn-primary` / `.btn-outline`, or the
token-based button classes (`.shs-btn`, `.ppc-btn`, `.cta-dark-btn`) which follow this rule.

**Slider / nav (switch) buttons (RULE).** Prev/next buttons for any carousel or slider are the
brand `.slider-btn` standard (as in the *Our Case Studies* section): **40px (2.5rem) square,
radius `--radius-btn` (12px), `1px solid var(--primary)` border, transparent background,
`var(--primary)` chevron icon, hover → `var(--primary-light)`**, centred. Reuse the `.slider-btn`
class — never invent another nav-button style.

**Toggle switch (RULE).** Any binary/segmented toggle (Monthly/Yearly, tabs) is the brand
`.ppc-toggle` / `.ppc-tg` standard (as in the pricing cards): a **`--radius-pill` track** on
`--white` with a `--border`, segments in Open Sans 600, and the **active segment filled `--ink`
+ white text** (an inline accent like a "15% OFF" tag uses `--primary`). Reuse this markup — never
build a bespoke switch/toggle.

## Container widths — MAX 1200, ALL SECTIONS SAME WIDTH (HARD RULE)
**This is critical and non-negotiable:**
1. **Content NEVER exceeds 1200px.** The outer bound is `--container-xl` (**1200**) — the
   header container width. Nothing on any page goes wider.
2. **Every full-width section has the SAME content width and the SAME left/right edges.**
   The standard section wrap is:
   ```css
   .section-wrap { max-width: var(--content-max); margin: 0 auto; } /* 1152, inside 24px gutter */
   ```
   with the section providing the `--section-x` (24px) horizontal padding. This makes all
   sections line up with each other **and** with the header on every screen width. Never give
   one section a different max-width or gutter — they must read as one continuous column.
3. Narrower **centred** sub-columns may use `--container-lg 1080` · `--container-md 880` ·
   `--container-sm 680` · `--measure 760` — but only centred (e.g. a centred hero/intro),
   never as a different left-edge for a normal left-aligned section.

Do not hardcode widths (no `max-width: 1240px`/`1140px`) — only the tokens above.

## Header (RULE)
One header across all pages: the brand **light-blue gradient** bar
(`linear-gradient(90deg, #fff, #d7e4f7, #fff)`), sticky, with the Ethora logo + mega-menu.
No plain-white header variant. Header CTAs: **Try Free** (borderless text, `.header-cta-text`)
+ **Book a Call** (primary button).
- **First-section top offset (RULE):** the header is fixed (~`--header-h` = 92px). A page's
  first/hero section must clear it — use `padding-top: var(--hero-pt)`
  (= `--header-h` + a 24–64px gap). Never let hero content sit tight under the header.

## Shadows & z-index
`--shadow` · `--shadow-lg` · `--shadow-card` (floating card) · `--shadow-hover`
(lift) · `--shadow-lift` (hero/media image) · `--shadow-panel` (dropdown/mega).
Layers: `--z-header 100` · `--z-mega 90` · `--z-overlay 9998` · `--z-modal 9999`.

---

## Quality floor (every page/section)
Target **Accessibility ≥ 95, SEO 100, CLS 0**. Full detail in the skill's
`readme.md → QUALITY FLOOR`.
- **A11y:** contrast ≥ 4.5:1 (muted text `--text-caption`+, never lighter on white);
  in-text links underlined (`p a, li a`), not colour-only; heading order never skips
  (`h2 → h3`, not `h4`); one `<main>` landmark; icon-only buttons get `aria-label` +
  `type="button"`; every `<img>` has `alt`.
- **Performance:** right-size images (≤ 2× display width — an oversized PNG is the LCP
  killer); prefer WebP (via a server image plugin — `sips` can't write it); **every
  `<img>` has explicit `width`/`height`**; lazy-load heavy 3rd-party embeds (HubSpot)
  on first interaction.
- **Responsive:** no horizontal scroll on mobile — scope `overflow-x` fixes to a
  mobile media query; **`overflow` on `<html>`/`<body>` breaks `position: sticky`**.
  Stack 2-col rows ≤ 900px.
- **Dark panels — ALWAYS the brand treatment.** Any dark block (Book-a-Call / CTA, feature
  panels like the Node.js-SDK card, etc.) must use the brand `.shs-dark` colour: brand-deep
  `--primary-dark` (#002398) tinted ~85% over `images/start-free.png` — **never a near-black
  fill** (`rgba(8,18,40,…)` is wrong). For Book-a-Call / CTA blocks use the reusable
  **`template-parts/section-cta-dark.php`** partial, which encapsulates exactly this.
- **Verify with Lighthouse**, but measure *speed* on the prod https URL — local dev
  tanks Performance/Best-Practices and inflates LCP; SEO/A11y/CLS are trustworthy
  locally.

---

## Reusable building blocks

> **Full catalog with screenshots, props and copy-paste usage:** the project skill
> **`.claude/skills/ethora-theme/`** (`SKILL.md` + `references/BLOCKS.md`). Reuse a
> block instead of inventing a layout; that skill also restates these hard rules so a
> new developer can't drift off-brand.

- **Tokens:** `css/tokens.css` (this system).
- **Hero:** `template-parts/section-hero.php` — page opener on the brand gradient: eyebrow +
  h1 + lead + CTA buttons + trust row, product visual (inline SVG or image), optional
  compliance strip; clears the fixed header via `--hero-pt`.
- **Split card:** `template-parts/section-split-card.php` — brand-gradient card with a
  heading + paragraphs beside an image (`reverse` flips sides). Optional image.
  `'dark' => true` → **full-bleed brand dark-blue band** (edge-to-edge `.shs-dark`, white text).
- **Key features:** `template-parts/section-key-features.php` — auto-cycling accordion
  (one item open, progress loader drives the switch) + product image; `reverse` and any
  number of items; multiple instances per page.
- **Feature cards:** `template-parts/section-feature-cards.php` — grid of brand-gradient
  cards with a coloured circular icon + heading + text + optional "Learn more" link.
- **Scroll-telling:** `template-parts/section-why.php` — pinned title + changing image (left),
  text track that slides per step as you scroll (right); mobile accordion. Each step has an image.
- **Cards carousel:** `template-parts/section-cards-carousel.php` — draggable horizontal slider of
  dark cards (next peeks), prev/next buttons + swipe; each card = heading + labelled text blocks.
- **Comparison:** `template-parts/section-comparison.php` — capability vs a negative column (red ✕) vs a highlighted recommended column (green ✓, RECOMMENDED badge); responsive (cards on mobile).
- **Link cards:** `template-parts/section-link-cards.php` — grid of cards (icon + heading +
- **Feature spotlight:** `template-parts/section-feature-spotlight.php` — flagship blue card (chat mockup + chips) + numbered white cards; responsive.
  text + "Read more →") that fill brand blue on hover; for related links / SDKs / industries.
- **Modal:** `template-parts/section-book-call-modal.php` — "Book a Call" modal with
  configurable left panel (eyebrow/title/text/bullets/image). Opened by any
  `.book-demo-button` (incl. the header CTA). **Site-wide default** is rendered on
  `wp_footer` (`functions.php`) with generic copy; a page **overrides** it by calling the
  partial in its own body (renders first → wins via the render-once `$GLOBALS` guard) —
  e.g. `page-healthcare.php` shows healthcare copy. Exactly one `#demo-modal` per page.
- **Dark CTA / Book-a-Call:** `template-parts/section-cta-dark.php` — brand `.shs-dark`
  panel (eyebrow/heading/text/buttons). **Use this for every dark CTA block** so the colour
  stays on-brand (#002398 over start-free.png), never near-black.
- **Pricing cards:** `template-parts/section-pricing-cards.php` — reusable Variant-A 3-card
  block with Monthly/Yearly toggle (params: eyebrow/heading/subheading/show_header/bg).
  `template-parts/section-pricing.php` wraps it for the compact in-page section; the full
  `/pricing/` page (`page-pricing.php`) wraps it with hero + Why + CTA + FAQ.
- **Sections:** `template-parts/section-*.php` (choose-app, use-kit, quick-start,
  pricing, testimonials, case-studies, …) — pass data via `get_template_part` args.
- **Reference implementation:** `page-self-hosted-server.php` is fully tokenised —
  copy its patterns when building new marketing pages.

## How to add a page (checklist)
1. Build with `var(--token)` values only (colour / type / spacing / radius / width).
2. Reuse existing `template-parts/` blocks where possible.
3. Hit the quality floor above; run Lighthouse.
4. If you need a new value, add the token to `css/tokens.css` first.
