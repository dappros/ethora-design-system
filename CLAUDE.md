# CLAUDE.md

## Frontend / markup

For any task that touches markup, styles or UI (HTML, CSS/SCSS, page markup,
components, landing pages, `page-*.php` templates with markup) — always apply the
`ethora-design` skill before writing any code.

`ethora-design` is a combined skill: the methodology and quality bar from
Claude Design (`frontend-design`) + the Ethora brand design system as the source of
truth for concrete values. Priority: process/composition/hierarchy come from the
methodology, but colour/font/spacing/radius/icons are always Ethora tokens (they
override any "free" choice). Key points: brand blue `#0052CD` (`--primary`) on the
`--background` canvas; **the only font is Open Sans** (every `--font-*` → Open Sans,
hierarchy by weight/size only); radii `--radius-btn` 12px (buttons) / `--radius-2xl`
18px (cards) / `--radius-3xl` 24px (large). Full guide — in `readme.md` inside the skill.

> The in-project skill **`.claude/skills/ethora-theme/`** (ships with the theme, can be
> handed to another developer) restates these hard rules and contains a **catalog of
> ready-made blocks with screenshots and props** (`references/BLOCKS.md`). It is the
> primary source for redesigns and new pages; apply it for any markup in this theme.

After building/changing noticeable UI — run a review with the `design` plugin skills
(Claude Cowork, `design@knowledge-work-plugins`): `design-critique` (design critique)
and `accessibility-review` (a11y audit). For design-system decisions — `design-system`,
for microcopy — `ux-copy`. This complements the generative `ethora-design`: that one
builds, `design` checks.

This does not apply to pure backend tasks: PHP logic, hooks, configs, bugfixes that
don't change markup/styles.

## Design tokens (source of truth in the repository)

All design values (colour, text sizes, weight, line-height, tracking, spacing, radii,
container widths, shadows, z-index) are defined once in
[`css/tokens.css`](css/tokens.css) (one `:root`, loaded globally first).
**Build and edit markup only through `var(--token)` — no hardcoded hex/px.**
If a value you need doesn't exist — first add the token to `tokens.css`, then use it.

The human-readable guide to the system is [`DESIGN.md`](DESIGN.md) (palette, type scale,
spacing, radii, containers + a11y/perf quality floor). The reference page, fully on
tokens, is `page-self-hosted-server.php`.

The interactive primitives — CTA buttons, slider/nav (switch) buttons and the toggle
switch — get their canonical, token-based CSS from **`css/primitives.css`** (loaded after
`tokens.css`). Reuse those classes; never re-declare the styles in a page or partial.

### Hard markup rules (DO NOT break)

1. **Content width — max 1200px.** Never exceed `--container-xl` (1200px, the header
   container width). No hardcoded widths (no `max-width: 1240/1140px`).
2. **All sections the same width and aligned.** Every full-width section uses a wrapper
   `max-width: var(--content-max)` (1152) + `margin: 0 auto`, and the section provides the
   side padding `--section-x` (24px). The edges of all sections match each other AND the
   header at any screen width. Don't give sections a different max-width/gutter — it's one
   continuous column.
3. **Spacing — strictly the 16px scale** (`--space-16/32/48/64/80/96`; `--space-8` is micro
   only), side gutter 24px (`--section-x`). No manual px.
4. **Open Sans only** (every `--font-*` → Open Sans; no Newsreader/IBM Plex/Varela/Inter).
5. **Buttons (CTA)** Get started / Book a Call — brand standard: radius `--radius-btn` (12px),
   primary = `var(--primary)` + white, outline = `2px var(--primary)` + blue text. Reuse the
   canonical classes, do NOT restyle: `.btn .btn-primary` / `.btn-outline` /
   `.btn-light` / `.btn-outline-light` (on dark), or the token-based partial variants
   (`.shs-btn*`, `.ppc-btn`) with the same spec. No pill/other radius.
6. **Header & hero** — one header across all pages — the light-blue gradient (not white). The
   hero (`section-hero`) is full-viewport (`min-height: 100vh`) with content centred **below**
   the header via `padding-top: var(--header-h)` (no `--hero-pt`, no other vertical padding); it
   grows, never clips. Non-hero first sections clear the header with `padding-top: var(--hero-pt)`.
   **Hero layout is a HARD invariant across variants:** light and the blue `variant => 'v2'` share
   the *identical* block layout — text column (eyebrow/h1/lead/buttons/trust) left, media **framed
   in the right column, vertically centred**, compliance strip full-width below (label left, items
   right). A variant may only recolour (bg/text/border/opacity); it must NEVER change the layout,
   pin or viewport-size the media (which makes it cover the screen / go off-centre), or restyle the
   compliance strip. **Never add page-level CSS to "fix" the hero** — fix `template-parts/section-hero.php`
   so every page benefits; pages only pass props.
7. **Slider/nav (switch) buttons — ONLY the `.slider-btn` standard** (as in "Our Case Studies"):
   40px (2.5rem) square, radius `--radius-btn` (12px), `1px solid var(--primary)` border,
   transparent background, `var(--primary)` chevron, hover → `var(--primary-light)`. Centred. Any
   prev/next in carousels/sliders — only these, don't invent others.
8. **Toggle/switch — ONLY the `.ppc-toggle` / `.ppc-tg` standard** (as in pricing): a pill container
   `--radius-pill` on `--white` with a `--border`, the active segment filled `--ink` + white
   text (an accent tag like "15% OFF" — `--primary`). Any binary/segmented toggle —
   only this one, don't invent your own.
9. **Blue section background — ONLY the `--gradient-brand` token** (`brand-500 → brand-800`, 135deg):
   statement band, cards-carousel, stats, flagship. Don't write that `linear-gradient` inline — only the token.
   (This is the blue fill; near-black is forbidden, dark/CTA panels use the separate `.shs-dark` over an image.)
10. **Section vertical rhythm — never double the y-padding between two non-blue sections.**
    Two consecutive *light* sections (white / `--surface-alt` / soft-gradient-on-white — anything
    that is NOT a blue full-bleed band) share a **single `var(--section-y-sm)`** seam, not two
    stacked paddings. A light section directly above another light section drops its
    `padding-bottom` to 0; the lower one owns the gap with `padding-top: var(--section-y-sm)`. A
    light section keeps its bottom padding when the section **below is a blue band** (blue bands
    always keep their own symmetric padding — the colour must breathe). Do it declaratively: wrap
    only the light sections in `<div class="shs-sec">` (leave blue bands unwrapped) and add the two
    `:has()`/adjacent-sibling rules to the page `<style>` (see `page-self-hosted-server.php` and the
    `ethora-theme` skill's "Section vertical rhythm" rule). Never hand-tune per-section padding to fake it.
    **Exception:** two adjacent split cards (`shs-split-section`) sit tighter — a single
    `var(--space-32)` seam (mark those wrappers `.shs-sec.is-split` and add
    `.<main> .shs-sec.is-split + .shs-sec.is-split > * { padding-top: var(--space-32) !important; }`).
    See `page-healthcare.php`.
11. **Never two blue/dark full-bleed bands back-to-back (HARD).** A blue/dark full-bleed section
    (`--gradient-brand` band, `section-stats`, `section-split-card` with `dark: true`, `.shs-dark`
    CTA / `section-cta-dark`, `section-trust-band`) must ALWAYS be separated from the next one by a
    **light** section — the colour has to breathe against white. Consecutive *light* sections are
    fine (that's rule 10); consecutive *blue/dark* ones are not. In particular the closing
    `section-cta-dark` must be preceded by a light section, so don't put a blue statement band
    (`split-card dark:true`) directly above it — make that statement a light centred section instead
    (see the case-study pages `page-case-study-*.php`, `.cs-outcome`). The page hero gradient is the
    opener and doesn't count as a band for this rule.
12. **Closing "Get started" CTA — trust row + Book a Call right (HARD).** The dark
    `section-cta-dark` above the footer ALWAYS has (a) a green-check `trust` row under the
    buttons (3 short items; reuse the page's own claims verbatim when they fit, else the house
    default `Free tier available` / `Enterprise SLA` / `No vendor lock-in`) and (b) two buttons:
    the page's own primary CTA (`ghost`) on the LEFT, **`Book a Call`** (`light`, `modal => true`)
    on the RIGHT — Book a Call is always present and always rightmost (extra original buttons may
    stay, but never to its right). Pages with a modal button include
    `template-parts/section-book-call-modal`. This rule is standing user approval for adding the
    Book a Call label + trust row; rule 13 still governs all other wording.
13. **Copy is untouchable (GOLDEN RULE).** A redesign restyles a page — it NEVER rewrites it.
    All existing user-facing text (headings, leads, paragraphs, list items, button labels, FAQ
    Q&As, table cells) is carried over **verbatim**. A block prop the page has no text for
    (a `lead`, a blurb, a trust row) stays **empty/omitted** — never invent copy. Any new or
    changed wording ships only with the user's **explicit prior approval**; broken/duplicated
    copy is flagged and asked about, never silently "fixed". Existing wording carries SEO
    intent — rewording a heading can lose rankings even when it "reads better".
14. **Hero composition — Book a Call + trust + badges (HARD).** Every hero ships all three:
    (a) **two buttons with Book a Call rightmost** — the page's own CTA (original label/URL
    verbatim) on the LEFT, **`Book a Call`** (`'modal' => true`) ALWAYS present and ALWAYS the
    rightmost button (light hero: CTA `outline` + Book a Call `primary`; `v2` hero: the white
    pair `outline-light` + `light`); (b) a **green-check `trust` row under the buttons** — 3
    short, minimal items (2–4 words) matching the page's context, reusing on-page claims
    verbatim when they fit; (c) **two floating `badges` over the hero media** (`badges` prop →
    `.ehero-badge`, 1st bottom-left, 2nd `'pos' => 'tr'` top-right) — short `title` + one-line
    `text` reflecting the page content + a thematic 17px line icon. Pages with a modal button
    include `template-parts/section-book-call-modal`. Standing user approval for adding the Book
    a Call label, trust items and badge text; rule 13 governs all other wording. Reference:
    `page-self-hosted-server.php`.

## Reusable blocks (`template-parts/`)

Before building a new section — **check the catalog and reuse a ready-made block**
instead of inventing your own markup. Catalog with screenshots, props and ready-made
snippets: `.claude/skills/ethora-theme/references/BLOCKS.md`.

Ready-made partials (include via `get_template_part`):
- `section-hero.php` — page hero: gradient, eyebrow/h1/lead + buttons + trust, visual, compliance strip.
- `section-split-card.php` — gradient "text + image" card (`reverse`); `dark: true` — full-width blue band.
- `section-key-features.php` — auto-accordion with a progress loader + image.
- `section-feature-cards.php` — grid of "coloured circle icon + heading + text" cards.
- `section-why.php` — scroll-telling: pinned heading + changing image on the left, sliding per-step text on the right; accordion on mobile.
- `section-cards-carousel.php` — horizontal slider of dark cards (the next one peeks), prev/next + drag/swipe; card = heading + labelled blocks.
- `section-comparison.php` — comparison: capability + "bad" column (red ✕) + highlighted recommended (green ✓, RECOMMENDED badge); responsive (cards on mobile).
- `section-feature-spotlight.php` — flagship blue card (chat mockup + chips) + numbered white cards; responsive.
- `section-link-cards.php` — grid of link cards (icon + heading + text + "Read more →"), fills blue on hover. For related links / SDKs / industries.
- `section-cta-dark.php` — dark brand CTA panel (for ANY dark CTA).
- `section-deployment.php` — deployment/architecture: header + platform chips + dashed "your VPC" container with a card diagram (layers with arrows, brand-tinted core group, data/optional row) + legend; all via props, defaults = the Ethora stack.
- `section-compliance-cards.php` — grid of white cards (4 per row by default): light-blue icon tile + green "✓ STATUS" tag, heading, text. For compliance/trust strips (GDPR/HIPAA/SOC 2) or any "capability + status".
- `section-stats.php` — full-width band on the brand-blue gradient (like `.cc-section`) with a header and flat metrics separated by thin dividers: icon tile + big number + caption. For "by the numbers" blocks.
- `section-pricing-matrix.php` — features × plans comparison in a white card: plan columns (one flagged "Most popular" → blue header + highlighted column), rows grouped under mono labels, cells = green check / em-dash / text, optional legend. For a feature/pricing matrix (distinct from the 3-card `section-pricing-cards.php`).
- `section-vendor-comparison.php` — "us vs competitors": capability rows across a featured vendor (raised brand-blue card, checks + wins footer) + N competitor columns (red ✕ on weak rows). CSS-grid; the featured column floats. Distinct from the 2-column `section-comparison.php`.
- `section-feature-rows.php` — vertical stack of row cards: soft-blue icon tile + heading/description + status pills on the right (green "✓ Available" / blue "⚙ Customizable"). For a feature × availability/customizable list.
- `section-code-config.php` — syntax-highlighted code editor mockup (traffic-light dots + filename + Copy) on the left + labelled "what config covers" cards (icon tile + mono chip + text) on the right + footnote. For a "show the config / customize" developer section.
- `section-feature-list-media.php` — two-column split: icon + heading + description rows (hairline dividers, in-text links) on one side, a framed product image (or a dashed "drop an image" placeholder) on the other. `reverse` flips sides. For a "why choose us" feature list beside a visual.
- `section-trust-band.php` — full-bleed dark brand band (`.shs-dark`): mono label + row of white customer logos + a grid of headline stats (big number + caption). For a "trusted by / by the numbers" strip under a hero. Distinct from the brand-blue `section-stats.php`.
- `section-pricing-cards.php` · `section-testimonials-carousel.php` · `section-case-studies.php` and others.

A new block worth reusing: build it on tokens, self-contained (CSS once per request via
a flag in `$GLOBALS`), then add a screenshot and an entry in `references/BLOCKS.md`.
Reference — `page-self-hosted-server.php`.
