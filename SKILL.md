---
name: ethora-theme
description: >-
  Use for ANY UI / frontend / layout work in the Ethora WordPress theme
  (ethora-theme) — building new marketing pages, redesigning sections, editing
  page-*.php templates or template-parts, or adding components. Enforces the
  theme's non-negotiable design rules (design tokens only, max 1200px content
  width, Open Sans only, brand blue #0052CD, strict 16px spacing scale) and
  provides a catalog of ready-made, reusable section blocks with screenshots and
  copy-paste usage. Invoke BEFORE writing or editing any markup/CSS in this theme.
---

# Ethora Theme — Design System & Block Library

This is the design contract for the **ethora-theme** WordPress theme. If you
follow it, you cannot drift outside the brand. Two things govern every change:

1. **The rules** — colour, type, spacing, radius, width. Source of truth is
   [`css/tokens.css`](css/tokens.css) (the machine source) and
   [`DESIGN.md`](DESIGN.md) (the human guide). A condensed version is below.
2. **The blocks** — ready-made, reusable sections in
   [`template-parts/`](template-parts/). Reuse these instead of inventing
   new layouts. Full catalog with screenshots + props: **[`references/BLOCKS.md`](references/BLOCKS.md)**.

> The reference implementation is **`page-self-hosted-server.php`** — fully
> tokenised, uses every block below. Copy its patterns when building new pages.

---

## The one rule that matters

**Build only with `var(--token)`.** Never hardcode a hex colour or a px value for
colour / font / spacing / radius / width. If a value you need doesn't exist as a
token, **add it to `css/tokens.css` first**, then use it. This is the "no
ad-hoc drift" rule — it's what keeps every page on-brand.

## Workflow — do this every time

1. **Read** [`css/tokens.css`](css/tokens.css) and skim [`DESIGN.md`](DESIGN.md).
2. **Before building a section, check the catalog** ([`references/BLOCKS.md`](references/BLOCKS.md)).
   If a block fits, reuse it via `get_template_part()` — don't rebuild it.
3. **Build with tokens only.** Match the surrounding code's idiom.
4. **Hit the quality floor** (a11y / performance / responsive — below) and **add the default
   reveal-on-scroll animation** (see *Motion* — every new page/redesign ships it).
5. **New reusable block?** Make it self-contained and add it to the catalog (see
   *Adding a new block*).

---

## Non-negotiable rules (summary)

Full detail and the token table are in [`DESIGN.md`](DESIGN.md). The hard ones:

- **Tokens only.** No literal hex/px for colour, type, spacing, radius, width.
- **Colour:** brand blue `--primary` `#0052CD` + grey does ~90%. Dark/CTA panels use
  `--primary-dark` `#002398`. Semantic `--green`/`--orange`/`--red`/`--purple` only
  for meaning, sparingly. One bold accent per screen; keep the rest quiet.
- **Type:** **Open Sans only** — every `--font-*` maps to it. Differentiate with
  weight/size, never a second typeface. Headings = heavier weight.
- **Spacing:** strict **16px scale** — `--space-16/32/48/64/80/96`. `--space-8` is the
  ONLY sub-16 value (inline icon↔label/chip gaps). Never hand-type `12/22/30px`.
  Horizontal gutter is fixed **24px** (`--section-x`).
- **Section vertical rhythm — collapse the seam between two non-blue sections (HARD).**
  When two consecutive full-width sections are both *light* (i.e. NOT a blue / full-bleed
  brand band — white, `--surface-alt`, or a soft gradient card on white), their y-padding
  must NOT stack. The seam between them is a **single `var(--section-y-sm)` gap**, never
  two paddings. Rule: a light section directly followed by another light section drops its
  `padding-bottom` to `0`; the lower light section owns the gap via `padding-top:
  var(--section-y-sm)`. **A light section keeps its full `padding-bottom` when the section
  below is a blue band** (blue full-bleed bands always keep their own symmetric padding on
  both sides — the colour must breathe), and a light section directly after a blue band
  keeps its normal top padding. Implement it declaratively, not by hand-tuning each
  section: wrap ONLY the light sections in `<div class="shs-sec">` (leave blue bands
  unwrapped so a run never collapses across one), then in the page `<style>`:
  ```css
  .shs .shs-sec:has(+ .shs-sec) > * { padding-bottom: 0 !important; }
  .shs .shs-sec + .shs-sec > *      { padding-top: var(--section-y-sm) !important; }
  ```
  (scope the two selectors to the page's `<main>` class). Reference: `page-self-hosted-server.php`.
  **Exception — consecutive split cards sit tighter.** When two neighbouring light
  sections are both `shs-split-section` (the split-card block), the seam between them
  is a **single `var(--space-32)`**, not `var(--section-y-sm)` — they read as one
  connected series. Mark those wrappers `.shs-sec.is-split` and add a higher-specificity
  override after the two rules above:
  ```css
  .shs .shs-sec.is-split + .shs-sec.is-split > * { padding-top: var(--space-32) !important; }
  ```
  Don't hand-tune the split cards' `pad_top`/`pad_bottom` args to fake this — let the rule
  own it. Reference: `page-healthcare.php`. (If a section ships no native padding of its own
  — e.g. `.faq` — give the last light section before a blue band an explicit
  `padding-bottom: var(--section-y-sm)` so it doesn't butt against the band.)
- **Width (HARD):** content **never exceeds 1200px** (`--container-xl`). Every
  full-width section wraps at **`--content-max` (1152)** centred, with the section
  providing `--section-x` padding — so all sections share the same edges and line up
  with the header. Never give a section a different max-width/gutter. Narrower
  *centred* columns may use `--container-lg/md/sm` or `--measure`.
- **Radius:** `--radius-*` tokens; CTA buttons are `--radius-btn` (12px); cards
  `--radius-2xl` (18) / big cards `--radius-3xl` (24); chips/avatars `--radius-pill`.
- **Buttons (CTA):** the brand CTAs (Get started / Book a Call) — `--radius-btn`, Open Sans
  600; primary = `--primary` bg + white (hover `--primary-dark`); outline = `2px solid
  --primary` + primary text. **Reuse the canonical classes — never restyle a button:**
  `.btn .btn-primary` / `.btn-outline` / `.btn-light` / `.btn-outline-light` (global, `css/index.css`),
  or the token-based partial variants that follow the exact same rule (`.shs-btn*` in
  `section-hero`/`section-cta-dark`, `.ppc-btn`). No pill or other-radius CTAs. See
  *Core UI primitives* in [`references/BLOCKS.md`](references/BLOCKS.md).
- **Slider / nav (switch) buttons:** prev/next for any carousel/slider use ONLY the brand
  `.slider-btn` standard (as in *Our Case Studies*) — 40px (2.5rem) square, radius
  `--radius-btn` (12px), `1px solid --primary` border, transparent bg, `--primary` chevron,
  hover → `--primary-light`, centred. Reuse the `.slider-btn` class — never invent another
  nav-button style.
- **Toggle switch:** any binary/segmented toggle (Monthly/Yearly, tabs) uses ONLY the brand
  `.ppc-toggle` / `.ppc-tg` standard (as in the pricing cards) — a `--radius-pill` track on
  `--white` with a `--border`, and the active segment filled `--ink` + white text. Reuse this
  markup; never build a bespoke switch. See *Core UI primitives* in [`references/BLOCKS.md`](references/BLOCKS.md).
- **Brand-blue section background:** the ONLY blue-fill background for a full-bleed section is
  the token **`--gradient-brand`** (`brand-500 → brand-800`, 135deg) — the statement band, cards
  carousel and stats band all use it. Never hand-write that `linear-gradient` inline; reference
  the token. (Dark/CTA panels are different — those use the `.shs-dark` image treatment below.)
- **Never two blue/dark full-bleed bands back-to-back (HARD).** Any blue/dark full-bleed section
  (`--gradient-brand` band, `section-stats`, `section-split-card` with `dark: true`, `.shs-dark` /
  `section-cta-dark`, `section-trust-band`) must ALWAYS be separated from the next one by a **light**
  section — the colour must breathe against white. Consecutive *light* sections are allowed (that's
  the vertical-rhythm rule above); consecutive *blue/dark* ones are NOT. In particular the closing
  `section-cta-dark` must be preceded by a light section — don't stack a blue statement band
  (`split-card dark:true`) directly on top of it; make that statement a light centred section instead
  (reference: `page-case-study-drtalks.php` / `page-case-study-atom-advantage.php`, the `.cs-outcome`
  block). The page hero gradient is the opener and doesn't count as a band for this rule.
- **Header & hero clearance:** one header on every page — the light-blue gradient bar
  (never plain white). The **hero block** (`section-hero`) is full-viewport — `min-height:
  100vh`, content vertically centred in the visible area **below** the fixed header (top
  padding `var(--header-h)` clears it — NOT `--hero-pt`, and no other vertical padding), and
  it grows instead of clipping when content is taller than the viewport. Any **non-hero
  first section** still clears the header with `padding-top: var(--hero-pt)`.
- **Hero layout invariant (HARD):** every hero — light or the blue **`variant => 'v2'`** — has
  the **identical block layout**: text column (eyebrow → h1 → lead → buttons → trust) left,
  media **framed in the right column and vertically centred**, compliance strip full-width
  below (label left, items right). A **variant only recolours** (background / text / border /
  opacity) — it must **never** change the layout, pin or viewport-size the media (which makes it
  cover the screen / go off-centre), or restyle the compliance strip. **Never add page-level CSS
  to "fix" the hero.** If a hero looks wrong, fix `template-parts/section-hero.php` so every page
  benefits; a page only ever passes props. Reference: `page-self-hosted-server.php` (light) and
  `page-npm-chat-component.php` (`v2`) render the same layout.
- **Dark panels:** ALWAYS the brand `.shs-dark` treatment (`--primary-dark` ~85% over
  `images/start-free.png`) — **never near-black**. For Book-a-Call / dark CTA blocks
  reuse [`template-parts/section-cta-dark.php`](template-parts/section-cta-dark.php).

## Quality floor (every page/section)

Target **Accessibility ≥ 95, SEO 100, CLS 0**.
- **A11y:** contrast ≥ 4.5:1 (muted text `--text-caption` or darker on white);
  in-text links underlined; heading order never skips (`h2`→`h3`); one `<main>`;
  icon-only buttons get `aria-label` + `type="button"`; every `<img>` has `alt`.
- **Performance:** right-size images (≤ 2× display width); every `<img>` has explicit
  `width`/`height`; `loading="lazy"` for below-the-fold media.
- **Responsive:** no horizontal scroll on mobile; stack 2-col rows ≤ 900px. Don't put
  `overflow` on `<html>`/`<body>` (breaks `position: sticky`).

---

## Card grids — never leave an orphan last row (HARD)

Any grid of cards (feature-cards, compliance-cards, feature-spotlight items, link-cards…)
must **account for how many cards there are** and fill the whole row — never render a partial
last row with empty slots on the right. Match the layout to the count:

- **Count divides the column count evenly** (e.g. 4 in a 2-up, 6 in a 3-up) → leave it.
- **Even but not a clean multiple** (e.g. 4 in a 3-up) → **change the column count** so it
  divides: 4 → `2×2`, 8 → `4×2`. Don't force a 3-up that orphans one card.
- **Odd with a multi-card orphan row** (e.g. 7 in a 4-up, 5 in a 3-up) → keep the base
  columns but **stretch the last row's cards** to span the full width equally, using a
  fine-grained track grid:
  ```css
  /* 7 cards, 4-up: 12-track → 4 per row (span 3), last 3 stretch (span 4) */
  .grid { grid-template-columns: repeat(12, minmax(0,1fr)); }
  .grid > .card { grid-column: span 3; }
  .grid > .card:nth-last-child(-n+3):nth-child(n+5) { grid-column: span 4; }
  ```
- **Single orphan** (e.g. 5 in a 3-up leaves 1) → don't stretch one card to full width (it
  reads half-empty). Re-balance instead: 5 → top row 3 + bottom row 2 stretched (6-track:
  cards `span 2`, last two `span 3`), or `2 + 3`.

**Scope it, don't edit the shared partial.** Wrap the specific section in a page-local class
(e.g. `.shl-models`, `.shl-benefits`) and put the override in the page `<style>`, so other
pages using the same block are unaffected. Apply it desktop-only (`@media (min-width: 901px)`)
and let the block's own responsive rules take over below. Comment the rule with the card
count it's tuned for — if the count changes, the spans must be retuned. Reference:
`page-self-hosted-llm-ai-agent.php` (Supported Models, Take Control, Train AI).

---

## Motion — reveal-on-scroll blocks in by default (HARD)

**Every marketing page — a new build or a redesign — ships the reveal-on-scroll animation.**
Content blocks **fade + slide in** as they enter the viewport (headers rise, side blocks slide
from their side, card grids/lists cascade), exactly like the home page. It's not optional polish;
it's part of the house style, so add it as a matter of course — don't wait to be asked.

It's a self-contained `<style>` + inline `<script>` per page (kept off `main.js`), driven by an
IntersectionObserver that tags blocks with `data-reveal` + a direction and flips them to
`.reveal-in`. Full copy-paste snippet, how to build the per-page `SELECTORS` list, and the
non-negotiable gotchas are in **[`references/reveal-on-scroll.md`](references/reveal-on-scroll.md)**.
The short version of the gotchas (get these wrong and blocks "pop" instead of easing in):

- Animate **`translate`**, never `transform` (it would fight the cards' hover `transform`).
- Use **`[data-reveal][data-reveal]`** so the reveal `transition` out-specifies a card's own
  `transition` (otherwise `opacity` isn't animated).
- **`overflow-x: clip`** on the `<main>` wrapper contains the horizontal slide without a
  scrollbar and (unlike `overflow: hidden`) keeps `position: sticky` working.
- **Never tag** a `position: sticky` element or the **hero**; scope selectors to the page's
  `<main>` class so header/footer menus are untouched.
- Wrap the hidden state in `@media (prefers-reduced-motion: no-preference)`; JS-off / reduced
  motion ⇒ everything visible, no animation.

Reference: `page-self-hosted-llm-ai-agent.php` (and `index.php` for the home original).

---

## Reusable block catalog

Each is a `template-parts/section-*.php` partial. Reuse via `get_template_part()`.
Screenshots, full prop tables and copy-paste snippets are in
**[`references/BLOCKS.md`](references/BLOCKS.md)** — read it before building a section.

> Below the section blocks, BLOCKS.md also documents the **Core UI primitives** —
> the single canonical CTA buttons, slider/nav buttons, toggle switch, and the
> `--gradient-brand` blue section background. These are locked: reuse them exactly,
> never restyle or reinvent. Their CSS ships in **`css/primitives.css`** (load it after
> `css/tokens.css`) — import that file and use the classes; do not re-implement the rules.

| Block | Partial | What it is |
|---|---|---|
| **Hero** | `section-hero.php` | Page opener: gradient bg, eyebrow/h1/lead + CTA buttons + trust row, product visual, optional compliance strip. |
| **Split card** | `section-split-card.php` | Brand-gradient card: heading + paragraphs beside an image. `reverse` flips sides. |
| **Blue statement band** | `section-split-card.php` (`dark: true`) | Full-bleed brand dark-blue section, heading + paragraphs in white. Edge-to-edge colour. |
| **Scroll-telling** | `section-why.php` | Pinned title + changing image (left), text track that slides per step on scroll (right); mobile accordion. |
| **Key features** | `section-key-features.php` | Auto-cycling accordion (one item open, progress loader) + product image. `reverse` supported. |
| **Feature cards** | `section-feature-cards.php` | Grid of gradient cards: coloured circle icon + heading + text + optional "Learn more". |
| **Link cards** | `section-link-cards.php` | Grid of cards (icon + heading + text + "Read more →") that fill brand blue on hover. For related links / SDKs / industries. |
| **Bento grid** | *(pattern on `page-self-hosted-server.php`)* | Asymmetric 2+3 card grid; large gradient/dark cards with peeking screenshots + media tiles. |
| **Dark CTA** | `section-cta-dark.php` | Brand `.shs-dark` panel (eyebrow/heading/text/buttons). Use for EVERY dark CTA. |
| **Pricing cards** | `section-pricing-cards.php` | 3-card pricing, middle highlighted, Monthly/Yearly toggle. |
| **Testimonials carousel** | `section-testimonials-carousel.php` | Auto-advancing 3-up testimonial carousel with prev/next. |
| **Cards carousel** | `section-cards-carousel.php` | Draggable horizontal slider of dark cards (next peeks); prev/next + swipe. For use-cases / challenge→solution. |
| **Case studies** | `section-case-studies.php` | Case-study cards row. |
| **Comparison** | `section-comparison.php` | Capability vs negative (✕) vs highlighted recommended (✓) column; responsive (cards on mobile). |
| **Feature spotlight** | `section-feature-spotlight.php` | Big flagship blue card (chat mockup + chips) + numbered white cards. For a headline capability + supporting features. |
| **Deployment stack** | `section-deployment.php` | Header + platform chips + dashed "your VPC" container laying an architecture diagram out as native cards (stacked layer groups + arrows, tinted core group, data/optional row) + legend. |
| **Compliance cards** | `section-compliance-cards.php` | Grid of white cards (default 4-up): soft-blue icon tile + green "✓ STATUS" tag, heading, text. For trust/compliance strips or "capability + status" grids. |
| **Stats band** | `section-stats.php` | Full-bleed brand-blue gradient band (same as `.cc-section`) with a header + flat divided stats: icon tile + big number + label. For "by the numbers" strips. |
| **Pricing / feature matrix** | `section-pricing-matrix.php` | Features × plans comparison in a white card: plan columns (one "Most popular" → blue header + highlighted column), rows grouped under mono labels, cells = green check / em-dash / text, optional legend. |
| **Vendor comparison** | `section-vendor-comparison.php` | "Us vs competitors": capability rows across a featured vendor (raised brand-blue card, checks + wins footer) + N competitor columns (red ✕ on weak rows). CSS grid; the featured column floats. |
| **Feature rows** | `section-feature-rows.php` | Vertical stack of row cards: soft-blue icon tile + heading/description + status pills on the right (green "✓ Available" / blue "⚙ Customizable"). |
| **Code + config covers** | `section-code-config.php` | Syntax-highlighted code editor mockup (dots + filename + Copy) on the left + labelled "what config covers" cards (icon + mono chip + text) on the right, with a footnote. |
| **Feature list + media** | `section-feature-list-media.php` | Two-column split: icon + heading + description rows (hairline dividers) on one side, a framed product image (or a dashed "drop an image" placeholder) on the other. |
| **Trust band** | `section-trust-band.php` | Full-bleed dark brand band (`.shs-dark`): mono label + row of white customer logos + a grid of headline stats (big number + caption). For a "trusted by" strip under a hero. |

Other partials exist (`section-choose-app`, `section-use-kit`, `section-quick-start`,
`section-book-call-modal`, `section-pricing`, …) — list them with
`ls template-parts/` and open the file's top docblock for params.

---

## Adding a new block (so the library grows)

When you build a section worth reusing:

1. Create `template-parts/section-<name>.php`. Accept data via `$args`
   (`wp_parse_args` with sane defaults) — see existing parts for the pattern.
2. **Tokens only**, self-contained: emit the block's `<style>` **once per request**
   using a `$GLOBALS['…_assets']` flag (so multiple instances on one page don't
   duplicate CSS), and unique ids for a11y if it has interactive state.
3. Make it flexible: optional image, `reverse`, any number of items where sensible.
4. Add a screenshot to `references/screenshots/` and an entry (with props + usage) to
   [`references/BLOCKS.md`](references/BLOCKS.md), plus a row in the table above.

Capture screenshots the same way they were made (Playwright + system Chrome, hide
`.header`, element screenshot by selector) — see `references/BLOCKS.md` footer.
