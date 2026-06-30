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
   [`css/tokens.css`](../../../css/tokens.css) (the machine source) and
   [`DESIGN.md`](../../../DESIGN.md) (the human guide). A condensed version is below.
2. **The blocks** — ready-made, reusable sections in
   [`template-parts/`](../../../template-parts/). Reuse these instead of inventing
   new layouts. Full catalog with screenshots + props: **[`references/BLOCKS.md`](references/BLOCKS.md)**.

> The reference implementation is **`page-self-hosted-server.php`** — fully
> tokenised, uses every block below. Copy its patterns when building new pages.

---

## The one rule that matters

**Build only with `var(--token)`.** Never hardcode a hex colour or a px value for
colour / font / spacing / radius / width. If a value you need doesn't exist as a
token, **add it to `css/tokens.css` first**, then use it. This is the "no
отсебятина" rule — it's what keeps every page on-brand.

## Workflow — do this every time

1. **Read** [`css/tokens.css`](../../../css/tokens.css) and skim [`DESIGN.md`](../../../DESIGN.md).
2. **Before building a section, check the catalog** ([`references/BLOCKS.md`](references/BLOCKS.md)).
   If a block fits, reuse it via `get_template_part()` — don't rebuild it.
3. **Build with tokens only.** Match the surrounding code's idiom.
4. **Hit the quality floor** (a11y / performance / responsive — below).
5. **New reusable block?** Make it self-contained and add it to the catalog (see
   *Adding a new block*).

---

## Non-negotiable rules (summary)

Full detail and the token table are in [`DESIGN.md`](../../../DESIGN.md). The hard ones:

- **Tokens only.** No literal hex/px for colour, type, spacing, radius, width.
- **Colour:** brand blue `--primary` `#0052CD` + grey does ~90%. Dark/CTA panels use
  `--primary-dark` `#002398`. Semantic `--green`/`--orange`/`--red`/`--purple` only
  for meaning, sparingly. One bold accent per screen; keep the rest quiet.
- **Type:** **Open Sans only** — every `--font-*` maps to it. Differentiate with
  weight/size, never a second typeface. Headings = heavier weight.
- **Spacing:** strict **16px scale** — `--space-16/32/48/64/80/96`. `--space-8` is the
  ONLY sub-16 value (inline icon↔label/chip gaps). Never hand-type `12/22/30px`.
  Horizontal gutter is fixed **24px** (`--section-x`).
- **Width (HARD):** content **never exceeds 1200px** (`--container-xl`). Every
  full-width section wraps at **`--content-max` (1152)** centred, with the section
  providing `--section-x` padding — so all sections share the same edges and line up
  with the header. Never give a section a different max-width/gutter. Narrower
  *centred* columns may use `--container-lg/md/sm` or `--measure`.
- **Radius:** `--radius-*` tokens; CTA buttons are `--radius-btn` (12px); cards
  `--radius-2xl` (18) / big cards `--radius-3xl` (24); chips/avatars `--radius-pill`.
- **Buttons:** the brand CTAs (Get started / Book a Call) — `--radius-btn`, Open Sans
  600; primary = `--primary` bg + white (hover `--primary-dark`); outline = `2px solid
  --primary` + primary text. No pill or other-radius CTAs.
- **Header:** one header on every page — the light-blue gradient bar (never plain
  white). A page's first section clears the fixed header with `padding-top:
  var(--hero-pt)`.
- **Dark panels:** ALWAYS the brand `.shs-dark` treatment (`--primary-dark` ~85% over
  `images/start-free.png`) — **never near-black**. For Book-a-Call / dark CTA blocks
  reuse [`template-parts/section-cta-dark.php`](../../../template-parts/section-cta-dark.php).

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

## Reusable block catalog

Each is a `template-parts/section-*.php` partial. Reuse via `get_template_part()`.
Screenshots, full prop tables and copy-paste snippets are in
**[`references/BLOCKS.md`](references/BLOCKS.md)** — read it before building a section.

| Block | Partial | What it is |
|---|---|---|
| **Hero** | `section-hero.php` | Page opener: gradient bg, eyebrow/h1/lead + CTA buttons + trust row, product visual, optional compliance strip. |
| **Split card** | `section-split-card.php` | Brand-gradient card: heading + paragraphs beside an image. `reverse` flips sides. |
| **Key features** | `section-key-features.php` | Auto-cycling accordion (one item open, progress loader) + product image. `reverse` supported. |
| **Feature cards** | `section-feature-cards.php` | Grid of gradient cards: coloured circle icon + heading + text + optional "Learn more". |
| **Link cards** | `section-link-cards.php` | Grid of cards (icon + heading + text + "Read more →") that fill brand blue on hover. For related links / SDKs / industries. |
| **Bento grid** | *(pattern on `page-self-hosted-server.php`)* | Asymmetric 2+3 card grid; large gradient/dark cards with peeking screenshots + media tiles. |
| **Dark CTA** | `section-cta-dark.php` | Brand `.shs-dark` panel (eyebrow/heading/text/buttons). Use for EVERY dark CTA. |
| **Pricing cards** | `section-pricing-cards.php` | 3-card pricing, middle highlighted, Monthly/Yearly toggle. |
| **Testimonials carousel** | `section-testimonials-carousel.php` | Auto-advancing 3-up testimonial carousel with prev/next. |
| **Case studies** | `section-case-studies.php` | Case-study cards row. |

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
