# Ethora Theme — Block Catalog

Ready-made, reusable sections. **Reuse these instead of inventing new layouts.**
Each renders via `get_template_part()`. All are tokenised (`css/tokens.css`),
self-contained (ship their own CSS once per request) and responsive.

Class prefixes are intentionally short and project-specific (`shs-` = self-hosted
server origin, `ppc-` = pricing, `tc-`/`tcar` = testimonials, `cta-dark`, …). The
screenshots below show what each name actually looks like, so you don't have to
guess from the markup.

> Live reference: every block here is used on **`page-self-hosted-server.php`**
> (URL `/self-hosted-chat-server-aws/`). Open it to see them in context.

---

## 1. Hero — `section-hero.php`

Page opener on the brand diagonal gradient: eyebrow + `<h1>` + lead + CTA buttons +
a green-check trust row on the left, a product visual (inline SVG or image) on the
right, decorative rhombus corners, and an optional compliance strip below. Full-viewport
(`min-height: 100vh`): content is vertically centred in the area below the fixed header
(top padding `--header-h` clears it — no `--hero-pt`), and the hero grows instead of
clipping when content is taller than the viewport.

![Hero](screenshots/hero.png)

**Props**

| Prop | Type | Notes |
|---|---|---|
| `eyebrow` / `title` / `lead` | string | `title` → `<h1>`; all inline-HTML-friendly |
| `buttons` | array | each: `label`, `style` (`primary`/`outline`/`light`/`ghost`/`outline-light`), `url`, `new_tab`, `id`, or `modal: true` (renders a `.book-demo-button` that opens the Book-a-Call modal). On a dark/v2 hero use the white styles: `light` (white fill) / `outline-light` (white outline) / `ghost` (translucent) |
| `trust` | array | green-check items (strings) |
| `media` | string | theme-relative path or URL — `.svg` is **inlined** (smooth GPU compositing), raster → `<img>` |
| `media_alt` / `media_width` / `media_height` / `media_html` | | raster alt+dims, or raw markup override |
| `media_shadow` | bool | framed raster media casts a soft drop shadow (`--shadow-lift`) by default; pass `false` for a flat, shadowless visual |
| `rhombus` | bool | decorative corner shapes (default `true`) |
| `compliance` | array | `{ label, items[] }` — bottom strip (optional) |
| `full_height` | bool | full-viewport hero: `min-height: 100vh`, content vertically centred in the area below the fixed header (top padding `--header-h`, no `--hero-pt`); grows instead of clipping tall content (default `true`; set `false` for a compact hero) |
| `variant` | string | `''` = default (light gradient, dark text). **`'v2'`** = **bright brand-blue** hero (`--gradient-hero-v2`, `#4188f3 → #0640c3`) with **white** text and rhombus at `opacity .1`. **`v2` changes ONLY the colour — the layout is byte-for-byte the default hero** (same two-column grid, media framed in the right column and vertically centred, same compliance strip). It does NOT pin/enlarge the media or move any block, so it never covers the copy and needs zero per-page CSS. Pair with white buttons (`light` + `outline-light`); `full_height => true` optional. Any framed raster media works — no special cut-out needed |

> **Hero layout is a hard invariant (do NOT break).** Every hero — light or `v2` — has the **identical block layout**: text column (eyebrow → h1 → lead → buttons → trust) on the left, media framed in the right column and **vertically centred**, compliance strip full-width below (label left, items right). A variant may only recolour (background / text / border / opacity). **Never** fork the layout per variant, pin/viewport-size the media, or restyle the compliance strip, and **never** add page-level CSS to "fix" the hero — if a hero looks wrong, fix `section-hero.php`, not the page. Just pass props.

```php
get_template_part( 'template-parts/section-hero', null, array(
  'title'   => 'Self-Hosted Chat Server: Deploy on AWS or On-Premises',
  'lead'    => 'Build your own secure and private chat platform…',
  'buttons' => array(
    array( 'label' => 'Book a Call', 'style' => 'primary', 'modal' => true ),
    array( 'label' => 'Get started', 'style' => 'outline', 'url' => 'https://app.chat.ethora.com/register', 'new_tab' => true, 'id' => 'accregred' ),
  ),
  'trust'      => array( '100% data ownership', 'Enterprise SLA', 'No vendor lock-in' ),
  'media'      => 'images/hero-chat.svg',
  'compliance' => array( 'label' => 'Compliance, built in at every layer.', 'items' => array( 'HIPAA', 'SOC 2', 'GDPR' ) ),
) );
```

---

## 2. Split card — `section-split-card.php`

Brand-gradient card with a heading + paragraphs on one side and an image on the
other. `reverse` puts the image on the left.

![Split card](screenshots/split-card.png)

Reversed (`'reverse' => true`):

![Split card reversed](screenshots/split-card-reverse.png)

**Props**

| Prop | Type | Notes |
|---|---|---|
| `eyebrow` | string | optional mono kicker |
| `title` | string | h2 (inline HTML allowed) |
| `paragraphs` | array | one `<p>` per item (inline HTML allowed) |
| `image` | string | theme-relative path (`images/foo.png`) or absolute URL — optional |
| `image_alt` / `image_width` / `image_height` | string/int | alt + CLS dimensions |
| `reverse` | bool | `true` = image on the LEFT |
| `pad_top` / `pad_bottom` | string | optional vertical-padding overrides, e.g. `'var(--space-32)'` / `'48px'` (default = section rhythm) |

```php
get_template_part( 'template-parts/section-split-card', null, array(
  'title'        => 'Geography of Your Chat Server',
  'paragraphs'   => array( 'First paragraph.', 'Second paragraph.' ),
  'image'        => 'images/self-hosted-chat-server-aws-why-two.png',
  'image_alt'    => 'Deploy close to your userbase across regions',
  'image_width'  => 1733, 'image_height' => 907,
  'reverse'      => true,
) );
```

---

## 3. Blue statement band — `section-split-card.php` (`dark: true`)

A **full-bleed** brand-blue section (the brand gradient `brand-500 → brand-800`, same as
`.sb-section` / the cards-carousel cards) with a heading + paragraphs in white. The colour
runs edge-to-edge — background **and** side gutters are blue — while the text stays aligned
to the content container. It's the `dark` variant of the split card: pass `'dark' => true`
— either as a bold statement / section divider (no image) **or with an optional image
on either side** (`image` + `reverse`), same as the light split card but full-bleed dark.

![Blue statement band](screenshots/split-card-dark.png)

**Props** — same as Split card (#2) plus:

| Prop | Type | Notes |
|---|---|---|
| `dark` | bool | `true` = full-bleed brand dark-blue section, white text (eyebrow → `--accent-on-dark`, links → white) |

```php
get_template_part( 'template-parts/section-split-card', null, array(
  'title'      => 'Enable secure in-app communication for healthcare teams',
  'paragraphs' => array( 'One bold statement paragraph…' ),
  'dark'       => true,
) );
```

---

## 4. Scroll-telling — `section-why.php`

A scroll-driven "telling" section: a **pinned** pane on the left (eyebrow + title + lead +
a changing image) and, on the right, a text track that slides up as you scroll — the image
swaps and the matching numbered step highlights at each threshold. On mobile it collapses to
a tap-to-open **accordion** (one step's image at a time). Self-contained (CSS + JS once),
supports multiple instances. Each step needs its own image.

![Scroll-telling](screenshots/why.png)

**Props**

| Prop | Type | Notes |
|---|---|---|
| `eyebrow` / `title` / `lead` | string | pinned header (eyebrow + h2 + lead) |
| `numbered` | bool | show `01/02/03` markers (default `true`) |
| `rhombus` | bool | decorative background shape (default `true`) |
| `steps` | array | **required**, 2+ — each: `title`, `text` (inline HTML), `image` (path/URL, used desktop **and** mobile), `image_alt` |
| `frame` | bool | default `true` (card frame: shadow + radius + `cover`). Set **false** for self-framed images (own bg/shadow) → shows whole image (`contain`), no extra frame |

```php
get_template_part( 'template-parts/section-why', null, array(
  'eyebrow' => 'Why self-host with Ethora',
  'title'   => 'Full ownership without the operational burden',
  'lead'    => 'A self-hosted chat server is a messaging platform you deploy…',
  'steps'   => array(
    array( 'title' => 'Complete data ownership', 'text' => 'Full control over…',
           'image' => 'images/foo-one.png', 'image_alt' => '…' ),
    // …2+ steps, each with its own image
  ),
) );
```

Scroll length scales with step count (~60vh per step). Best for a short narrative of 3–5
steps where each has a supporting visual.

---

## 5. Key features — `section-key-features.php`

Interactive accordion: one item open at a time, auto-cycles with a progress
loader at the bottom of the open card (the loader drives the switch), product
image on the side. Click selects an item; height is fixed to the tallest item so
it never jumps. **Hovering the card (or focusing a header) pauses the loader — and
therefore the auto-advance — and it resumes from the same spot on leave.** `reverse`
flips sides. Supports multiple instances per page.

![Key features](screenshots/key-features.png)

Reversed + text-only rows (no icons):

![Key features reversed](screenshots/key-features-reverse.png)

**Props**

| Prop | Type | Notes |
|---|---|---|
| `eyebrow` / `title` / `lead` | string | header (all optional) |
| `image` / `image_alt` / `image_width` / `image_height` | | side image (optional) |
| `interval` | number | seconds per item (default `4.2`) |
| `reverse` | bool | image on the LEFT |
| `shade` | bool | tint section bg + hairline borders |
| `media_shadow` | bool | side image casts `--shadow-card` by default; pass `false` for a flat, shadowless image |
| `pad_top` / `pad_bottom` | string | optional vertical-padding overrides, e.g. `'var(--space-32)'` (default = section rhythm) |
| `features` | array | **required**, any length — each: `title`, `text`, `icon` (raw SVG, optional) |

```php
$ic = 'width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0052CD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"';
get_template_part( 'template-parts/section-key-features', null, array(
  'title'    => 'Key Features',
  'lead'     => 'Select any feature to see the details.',
  'image'    => 'images/foo.png', 'image_alt' => '…', 'image_width' => 881, 'image_height' => 779,
  'features' => array(
    array( 'title' => 'Real-time messaging', 'text' => 'Instant delivery…', 'icon' => '<svg '.$ic.'>…</svg>' ),
    // …any number; omit 'icon' for a text-only row
  ),
) );
```

---

## 6. Feature cards — `section-feature-cards.php`

Responsive grid of cards on the brand gradient: coloured circular icon + heading +
short text + optional "Learn more →" button. Auto-fit grid — works with any number
of cards.

![Feature cards](screenshots/feature-cards.png)

**Props**

| Prop | Type | Notes |
|---|---|---|
| `eyebrow` / `title` / `lead` | string | header (optional) |
| `shade` | bool | tint section bg (adds top/bottom hairline borders) |
| `shade_borders` | bool | when `shade` is on, the tint band gets top/bottom hairline borders by default; pass `false` to drop them (keep the tint) |
| `cards` | array | **required** — each: `title`, `text` (inline HTML), `icon` (white line SVG, `stroke="currentColor"`), `color` (icon-circle colour token, default `var(--primary)`), `link_url` + `link_label` (optional → renders the button) |

```php
$ic = 'width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"';
get_template_part( 'template-parts/section-feature-cards', null, array(
  'title' => 'Compliance (HIPAA, SOC2, GDPR and CCPA)',
  'cards' => array(
    array( 'title' => 'GDPR and CCPA', 'text' => '…', 'color' => 'var(--primary)', 'icon' => '<svg '.$ic.'>…</svg>' ),
    array( 'title' => 'HIPAA and SOC2', 'text' => '…', 'color' => 'var(--green)',   'icon' => '<svg '.$ic.'>…</svg>' ),
    array( 'title' => 'BAA and DPA',    'text' => '…', 'color' => 'var(--orange)',  'icon' => '<svg '.$ic.'>…</svg>',
           'link_url' => '/healthcare-chat-sdk/', 'link_label' => 'Learn more' ),
  ),
) );
```

Icon-circle colours come from tokens (`--primary`, `--green`, `--orange`, …). Keep
it restrained — don't turn it into a rainbow.

---

## 7. Link cards — `section-link-cards.php`

Responsive grid of cards (icon tile + heading + description + "Read more →"). On
hover the brand blue fills in from the bottom-right corner and the text/icon turn
white. Whole card is the link by default. Used for "Explore Related Solutions" and
ideal for any "related links / SDKs / industries" grid.

![Link cards](screenshots/link-cards.png)

**Props**

| Prop | Type | Notes |
|---|---|---|
| `eyebrow` / `title` / `lead` | string | header (optional) |
| `shade` | bool | tint section bg |
| `card_as_link` | bool | default `true` (whole card clickable). Set **false** if a card's `text` contains its own `<a>` (avoids nested anchors) — then only "Read more" is the link |
| `footnote` | string | small paragraph under the grid (HTML, optional) |
| `cards` | array | **required** — each: `title`, `text` (inline HTML), `icon` (line SVG, optional), `url`, `link_label` (default "Read more") |

```php
$ic = 'width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0052CD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"';
get_template_part( 'template-parts/section-link-cards', null, array(
  'title'    => 'Explore Related Solutions',
  'lead'     => 'Choose the right tools for your stack.',
  'footnote' => '<a href="/pricing/">See pricing plans</a> for self-hosted deployment options.',
  'cards'    => array(
    array( 'url' => '/chat-sdk/chat-sdk-reactjs-web', 'title' => 'React Chat SDK', 'text' => 'Add messaging to your web app.', 'icon' => '<svg '.$ic.'>…</svg>' ),
    // …any number
  ),
) );
```

> The "Flexible Architecture for Any Industry" grid on `page-self-hosted-server.php`
> uses the same visual design but is still inline (`.shs-ind-*`) — its card text
> contains in-text links, so it would use `'card_as_link' => false`. Migrate it to
> this partial when convenient.

---

## 8. Bento grid — *pattern* (inline on `page-self-hosted-server.php`)

Asymmetric bento: 2 large cards on top (a light brand-gradient card and a dark
`.shs-dark` card, each with a screenshot "peeking" out of the bottom-right corner) +
3 cards below (white text card + two media tiles). Big heading, bullet list inside.

![Bento grid](screenshots/bento-deployment.png)

Not yet a partial — it lives inline in the DEPLOYMENT (07) section of
`page-self-hosted-server.php` (search `shs-bento-grid`). Lift it into
`section-bento.php` when a second page needs it; the markup + scoped `<style>` are
self-contained and ready to parametrise.

---

## 9. Dark CTA — `section-cta-dark.php`

Brand `.shs-dark` panel (deep `--primary-dark` over `start-free.png`) with eyebrow /
heading / text / buttons. **Use this for EVERY dark CTA / Book-a-Call block** — never
hand-roll a near-black panel. Params: `eyebrow`, `heading`, `text`, `id`, `buttons`
(`label` / `url` / `style` `light`|`ghost` / `modal`), **`wide`** (`true` = full-bleed
panel spanning the section width, only the 24px gutter — not capped at `--content-max`),
**`image`** (override the background image, theme-relative/URL), **`fade`** (`true` =
the self-hosted page's `.shs-dark` gradient `.85 → .5` so the image texture shows, e.g.
`'image' => 'images/testimonials.png', 'fade' => true` to match `page-self-hosted-server.php`),
and **`trust`** (array of strings → a centred green-check row under the buttons, e.g.
`'trust' => array( 'No credit card', 'Free tier available', 'BAA on Enterprise' )`).

![Dark CTA](screenshots/cta-dark.png)

```php
get_template_part( 'template-parts/section', 'cta-dark', array(
  'eyebrow' => 'Book a call',
  'heading' => 'Save months and launch faster',
  'text'    => '…',
  'id'      => 'book-a-call',
  'buttons' => array(
    array( 'label' => 'Start Free Trial', 'url' => '…', 'style' => 'light' ),
    array( 'label' => 'Book a Call',      'url' => '…', 'style' => 'ghost' ), // or 'modal' => true
  ),
) );
```

---

## 10. Pricing cards — `section-pricing-cards.php`

Three pricing cards, middle highlighted, Monthly/Yearly toggle. Prices live in the
`$pp_plans` array inside the partial (edit in one place).

![Pricing cards](screenshots/pricing-cards.png)

**Params:** `eyebrow`, `heading`, `subheading`, `show_header` (bool — false when the
page already has a hero heading), `bg` (`'alt'` | `'white'` | `'none'`).
`section-pricing.php` wraps it for an in-page section; `page-pricing.php` wraps it for
the full pricing page.

---

## 11. Testimonials carousel — `section-testimonials-carousel.php`

Auto-advancing carousel: 3 cards (2 on tablet, 1 on mobile), infinite loop, prev/next
buttons.

![Testimonials carousel](screenshots/testimonials-carousel.png)

**Params:** `eyebrow`, `heading`, `interval` (ms, default 5000).

---

## 12. Case studies — `section-case-studies.php`

The home-page **"Our Case Studies"** carousel, as a self-contained block. This is the
**canonical** look — whenever a page needs "Our Case Studies", render exactly this block; do
not hand-roll the old `.case-study-*` markup. What it must always include:

- **Cards** — full-bleed sliding track, the active card centred with its neighbour peeking +
  scaling. On desktop (≥768px) each card is **two columns** (screenshot left, content right);
  it stacks on mobile. Content = customer logo + short description + a green-check **success
  highlights** list + a **Read more** link.
- **Nav (switch) buttons** — the brand `.slider-btn` prev/next, **blue-outlined**
  (`1px solid var(--primary)` + primary chevron), **pinned top-right of the header** on
  desktop (centred under the heading on mobile). These are **bundled inside the block** (own
  `.csx-head` / `.csx-controls` CSS), NOT the legacy `.case-studies` globals — so they render
  correctly on any page. They are part of the design: never ship the section without them.
- **Dots** under the track, and **drag / swipe** (mouse + touch).

Self-contained — ships its **own CSS + JS once per request**, scoped to `.csx-*` classes so
it works on any template (page templates load `css/index.css`, the home page loads
`style-index.css`) and never collides with the legacy `.case-study-*` sliders in
`js/index.js` / `main.js`. Tokens only. **No args** — the two flagship case studies (DrTalks,
Atom Advantage) are baked in; pass `'title'` to override the heading.

```php
get_template_part( 'template-parts/section-case-studies' );
```

Live use: the home page (`index.php`), `page-self-hosted-llm-ai-agent.php`, `page-healthcare.php`
— all render the same block, so an edit here updates every page.

![Case studies](screenshots/case-studies.png)

---

## 13. Cards carousel — `section-cards-carousel.php`

Title + lead on top, then a **full-width** track of dark brand cards: each card has a
**text column on the left and an image on the right**; the **active card is centred** and its neighbours **peek equally** on both sides (and scale up/down on switch). Switch with the **centred** prev/next buttons (the brand `.slider-btn`
standard) **or by dragging / swiping** (mouse and touch). Text blocks stack vertically and
are labelled. Best for use-cases or "challenge → solution" content. `'light' => true` for
white cards instead of dark.

![Cards carousel](screenshots/cards-carousel.png)

**Props**

| Prop | Type | Notes |
|---|---|---|
| `eyebrow` / `title` / `lead` | string | header (optional) |
| `light` | bool | white cards instead of the default dark brand panel |
| `invert` | bool | **inverted colours** — the SECTION fills brand-blue (`--gradient-brand`) and the cards turn WHITE with dark text (header text → light, nav buttons → white). White cards use tighter vertical padding (less bottom whitespace). See below. |
| `collapsible` | bool | clamp each card's text to a fixed height so tall/uneven cards stay compact; when the text overflows, a **Read more** button appears and reveals the rest via an **inner scroll** — the card never grows. Only active ≥ 761px (mobile flows full text). Pair `collapse_max` (default `13rem`) to tune the clamp/scroll height. Live use: **Hosting Infrastructure** on `page-self-hosted-server.php`. |
| `collapse_max` | string | CSS length for the clamped text region when `collapsible` is on (default `13rem`) |
| `cards` | array | **required**, any length — each: `title`, `blocks` (array of `{ label, text }`, stacked), `image` + `image_alt` (right side, optional → text spans full width without it), optional `link_url` + `link_label` (CTA pill), optional `badges` (see below) |

Per-card **floating badges** — small white chip cards overlaid on the card image that gently
bob up and down (staggered, reduced-motion safe). Each card may set `badges` => array of
`{ title, text, icon?, pos? }`: `icon` is raw SVG (defaults to a green shield tile), `pos` is
`'bl'` (bottom-left, default for the 1st) or `'tr'` (top-right, default for the 2nd). Green
tile uses `--green-tint`/`--green-border`/`--green-text`. Live use: **Hosting Infrastructure**
on `page-self-hosted-server.php` ("Your infrastructure · AWS · Azure · GCP · on-premises").

Nav buttons follow the **slider/nav button RULE** (`.slider-btn`: 40px, `--radius-btn`,
`1px --primary` border, transparent, `--primary` chevron, hover `--primary-light`, centred).

```php
get_template_part( 'template-parts/section-cards-carousel', null, array(
  'title' => 'Healthcare Communication Use Cases',
  'lead'  => 'Ethora addresses each scenario within a single platform.',
  'cards' => array(
    array(
      'title' => 'Patient-provider messaging',
      'image' => 'images/foo.png', 'image_alt' => '…',
      'blocks' => array(
        array( 'label' => 'The challenge', 'text' => '…' ),
        array( 'label' => 'How Ethora helps', 'text' => '…' ),
      ),
    ),
    // …any number
  ),
) );
```

### 13a. Inverted variant — `'invert' => true`

The colour-flipped version of the same carousel: the **section background becomes
brand-blue** (`--gradient-brand`) and the **cards turn white** with dark text — the mirror
of the default (light section + blue cards). Header text goes light (`#fff` heading,
`--text-on-dark` lead, `--accent-on-dark` eyebrow), the white cards get a soft
`--shadow-lift` to separate from the blue field, block labels use `--primary`, and the nav
buttons become white.

The white cards also switch to a **stacked layout**: title, mono label and text on top,
then the card **image full width at the bottom**. The image sits in a **fixed
aspect-ratio box** (`16/10`, `object-fit: cover`) pinned to the card bottom, so **every
card is exactly the same height** regardless of each source image's own aspect ratio —
switching cards never reflows or jumps (this also reserves space for lazy-loaded images).
Cards are compact (`flex-basis` max ~500px). (The default / `light` carousel keeps its
side-by-side text-left / image-right layout; the stacked treatment is specific to
`invert`.)

Compose it exactly like the carousel above — just add `'invert' => true`. Live use: the
**Key Benefits** block on `page-chat-sdk-android.php`.

**Blue section + BIG side-by-side cards** — combine `'invert' => true` **with**
`'light' => true`. You keep the brand-blue section and light header, but the cards stay
the **large default layout (text left, image right)** instead of the compact stacked one —
white cards with `--shadow-lift`, no forced image crop. Use this when the cards carry a lot
of copy or a full illustration that shouldn't be cover-cropped. Live use: the **Hosting
Infrastructure (IaaS Provider) Options** block on `page-self-hosted-server.php`.

![Cards carousel — inverted (blue section, white cards)](screenshots/cards-carousel-invert.png)

```php
get_template_part( 'template-parts/section-cards-carousel', null, array(
  'invert' => true,                                   // blue section + white cards
  'title'  => 'Key Benefits of Ethora Chat SDK for Android',
  'lead'   => 'Everything you need to ship a native-feeling chat experience on Android.',
  'cards'  => array(
    array(
      'title'  => 'Modular UI Components',
      'image'  => 'images/foo.png', 'image_alt' => '…',
      'blocks' => array( array( 'label' => 'Native UI', 'text' => '…' ) ),
    ),
    // …any number
  ),
) );
```

---

## 14. Comparison — `section-comparison.php`

A "build it yourself vs the recommended option" comparison: a capability column on the
left, a plain **negative** column (red ✕ per row) and a **highlighted brand-blue
recommended** column (green ✓ per row, solid-blue header, `RECOMMENDED` badge). Inside a
white card. Responsive: a 3-column grid on desktop that **reflows to per-row cards** on
mobile (column names repeat inside each option).

![Comparison](screenshots/comparison.png)

**Props**

| Prop | Type | Notes |
|---|---|---|
| `eyebrow` / `title` / `lead` | string | header (optional) |
| `capability_label` | string | small label over the left column (default `Capability`) |
| `col_a` | array | `{ title, subtitle, icon }` — the negative column (red ✕ rows) |
| `col_b` | array | `{ title, subtitle, icon, recommended }` — the highlighted column (green ✓ rows) |
| `rows` | array | **required** — each: `capability`, `a` (negative text), `b` (positive text) |

```php
get_template_part( 'template-parts/section-comparison', null, array(
  'title' => 'Building it yourself vs Ethora\'s SDK',
  'col_a' => array( 'title' => 'Custom build', 'subtitle' => 'Building your own', 'icon' => '<svg…wrench…>' ),
  'col_b' => array( 'title' => 'Ethora SDK', 'subtitle' => 'Managed & pre-built', 'recommended' => true, 'icon' => '<svg…>' ),
  'rows'  => array(
    array( 'capability' => 'Time to deploy', 'a' => '3–6 months…', 'b' => 'Production-ready in 14 days' ),
    // …
  ),
) );
```

The ✕ / ✓ row marks are built in (red / green). Keep it to two columns.

---

## 15. Feature spotlight — `section-feature-spotlight.php`

A "flagship + grid" feature layout: title + lead, then one big **flagship** card on a
brand-blue gradient (numbered `01`, label + heading + text + chips, with a built-in **chat
mockup** or an image on the right), followed by a grid of smaller **numbered white cards**
(`02`, `03` …) — tinted icon + heading + text. Responsive (flagship stacks, cards reflow).

![Feature spotlight](screenshots/feature-spotlight.png)

**Props**

| Prop | Type | Notes |
|---|---|---|
| `eyebrow` / `title` / `lead` | string | header (optional) |
| `flagship` | array | `{ label, title, text, chips[], chat{ name,status,question,answer,file,avatar_icon } }` — or `image` + `image_alt` instead of `chat` |
| `items` | array | the smaller cards (numbered from 02) — each `{ icon (svg stroke="currentColor"), title, text }` |

```php
get_template_part( 'template-parts/section-feature-spotlight', null, array(
  'title'    => 'AI-Powered Healthcare Assistant',
  'lead'     => '…',
  'flagship' => array(
    'label' => 'Flagship capability', 'title' => 'Intelligent patient FAQ handling', 'text' => '…',
    'chips' => array( 'Trained on your docs', '24/7 answers' ),
    'chat'  => array( 'name' => 'Ethora Assistant', 'question' => 'What should I do before my MRI scan?',
                      'answer' => 'For your MRI: avoid metal jewelry…', 'file' => 'pre-op-guide.pdf' ),
  ),
  'items'    => array(
    array( 'icon' => '<svg…>', 'title' => 'Clinical query routing', 'text' => '…' ),
    // …
  ),
) );
```

---

## 16. Deployment stack — `section-deployment.php`

A centred header (eyebrow + title + lead), a row of platform chips (icon + label),
then a dashed **"your infrastructure" container** that lays an architecture diagram
out as native cards: stacked layer groups joined by down-arrows, a highlighted
brand-tinted **core** group, and a two-column row of side-by-side groups (data +
optional). Closes with a colour legend. Replaces a flat architecture PNG with an
on-brand, responsive, tokenised layout. Self-contained (CSS once per request).

![Deployment stack](screenshots/deployment.png)

**Props** — ALL optional; the defaults reproduce the Ethora self-hosted stack, so a
page usually overrides only `title` and `lead`.

| Prop | Type | Notes |
|---|---|---|
| `eyebrow` / `title` / `lead` | string | header (default eyebrow `Deployment`) |
| `platforms_label` | string | small label over the chips (default "The same stack, deployed anywhere") |
| `platforms` | array | each: `label`, `icon` (line SVG `stroke="currentColor"`, optional). Defaults to AWS/Google Cloud/Azure/On-premises/AWS Marketplace/Private cloud |
| `vpc_label` / `vpc_icon` | string | badge on the container top edge (default shield + "…data never leaves your VPC") |
| `groups` | array | the layers — each: `label`, `note`, `badge` (e.g. `optional`), `tint` (`'core'` = soft-brand bg), `dashed` (bool), `dot` (`core`\|`edge`\|`data`\|`optional` marker), `half` (bool → shares a row with adjacent half groups), `cols` (1–3 fixed card columns), `cards` (each `title` + `subtitle`) |
| `legend` | array | each: `label`, `dot` (same keys as group `dot`) |

Layout rule: consecutive `half` groups flow into one two-column row; full-width
groups stack, with a down-arrow drawn between two adjacent full-width layers.

```php
get_template_part( 'template-parts/section-deployment', null, array(
  'title' => 'Your servers. Your data. Your rules.',
  'lead'  => 'Self-hosting is the default with Ethora…',
  // groups/platforms/legend fall back to the Ethora stack defaults
) );
```

---

## 17. Compliance cards — `section-compliance-cards.php`

A responsive grid of white cards (**default 4-up**), each with a soft-blue rounded
icon tile and a green **"✓ STATUS"** tag on the top row, then a heading and a short
description. On hover the brand blue fills in from the bottom-right corner and the
contents turn white (same effect as Link cards #7). For trust/compliance strips (GDPR,
HIPAA, SOC 2 …); **omit `status` and it becomes a plain icon-card grid** (e.g. a
product-modules / "what you get" section — set `cols: 3` for six items). Self-contained
(CSS once per request); reflows to 2-up ≤900px, 1-up ≤560px.

![Compliance cards](screenshots/compliance-cards.png)

**Props**

| Prop | Type | Notes |
|---|---|---|
| `eyebrow` / `title` / `lead` | string | optional centred header |
| `shade` | bool | tint the section bg + hairline borders |
| `cols` | int | desktop columns, 2–4 (default 4) |
| `cards` | array | **required** — each: `icon` (line SVG `stroke="currentColor"`, shown in the blue tile), `title`, `text` (inline HTML), `status` (optional — green ✓ tag, uppercased in CSS) |

```php
get_template_part( 'template-parts/section-compliance-cards', null, array(
  'shade' => true,
  'cards' => array(
    array( 'title' => 'GDPR', 'status' => 'Compliant', 'text' => 'EU data protection…', 'icon' => '<svg …>' ),
    array( 'title' => 'HIPAA', 'status' => 'In production', 'text' => 'Healthcare PHI…', 'icon' => '<svg …>' ),
    // …
  ),
) );
```

Status text uses `--success-strong` (AA green on white). Keep the icons as line SVGs
(`stroke="currentColor"`) — they inherit the brand-blue tile colour.

---

## 18. Stats band — `section-stats.php`

A **full-bleed brand-blue** section (the same gradient as the `.cc-section` cards:
`brand-500 → brand-800`) with an optional centred header and a row of flat stats
separated by thin dividers — each a translucent icon tile, a big white number and a
label. For "by the numbers" / impact strips. Self-contained (CSS once per request);
stacks to a single column (with top dividers) on mobile.

![Stats band](screenshots/stats.png)

**Props**

| Prop | Type | Notes |
|---|---|---|
| `eyebrow` / `title` / `lead` | string | optional centred header (eyebrow → `--accent-on-dark`, heading white, lead `--text-on-dark`) |
| `stats` | array | **required** — each: `icon` (line SVG `stroke="currentColor"`, white in the glass tile), `value` (big number), `label` |

```php
$si = 'width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"';
get_template_part( 'template-parts/section-stats', null, array(
  'stats' => array(
    array( 'value' => '~95%', 'label' => 'faster dev time', 'icon' => '<svg '.$si.'>…</svg>' ),
    array( 'value' => '5 min', 'label' => 'widget install',  'icon' => '<svg '.$si.'>…</svg>' ),
    // …
  ),
) );
```

Background is the brand-blue gradient shared with the cards carousel (`.cc-section`) —
never a near-black fill. Icon tiles / dividers use translucent white
(`rgba(255,255,255,.14/.16)`), the theme's established dark-panel pattern.

---

## 19. Pricing / feature matrix — `section-pricing-matrix.php`

A features × plans comparison inside a white rounded card: plan columns across the top,
with one flagged **"Most popular"** (filled brand-blue header + a light-blue **highlighted
column** running the full height, rounded top/bottom, filled `--tint-blue`). Feature rows
**zebra-stripe** (white / soft-grey, all the way down) and can be organised under mono
**group labels** (`group_labels: false` drops them for a flat table). Each cell is a **green
"included" check** in a soft-green tile, an **em-dash** "not available", or **free text**
(numbers, `Unlimited`, `Community`, `99.9% SLA` …) — text in the popular column renders
brand-blue. Note: the theme has aggressive global `<thead>` styling, so the block resets it
(`.pm thead { background: transparent }`) — keep that when reusing. Closes with an
optional **Included / Not-available legend**. Self-contained (CSS once per request), tokens
only, horizontal-scroll on narrow screens. Use for the "Feature matrix" / plan-comparison
section (distinct from `section-pricing-cards.php`, which is the 3-card pricing block).

![Pricing / feature matrix](screenshots/pricing-matrix.png)

**Props**

| Prop | Type | Notes |
|---|---|---|
| `eyebrow` / `title` / `lead` | string | optional centred header |
| `shade` | bool | tint the section bg |
| `plans` | array | **required** — the columns; each: `name`, `price`, `popular` (bool → the highlighted column), `badge` (popular column tag, default "Most popular"), optional `cta` (`array('label','url')` link, or `array('label','modal'=>true)` to open the book-call modal — renders a button under the price; outline on white columns, white-fill on the blue popular column) |
| `groups` | array | **required** — each: `label` (mono group heading) + `rows[]`; each row: `feature` (inline HTML) + `values[]` **aligned to `plans` order** — a value of `true` → green check, `false` → em-dash, any string → text |
| `group_labels` | bool | show the mono group-label separator rows (default `true`); set **`false`** for a flat table (no group headings) |
| `legend` | bool | show the Included / Not-available legend (default `true`) |
| `note` | string | small centred footnote under the table (HTML, optional) |

```php
get_template_part( 'template-parts/section-pricing-matrix', null, array(
  'title'  => 'Android Chat SDK Feature Matrix',
  'plans'  => array(
    array( 'name' => 'Free',       'price' => '$0/mo' ),
    array( 'name' => 'Business',   'price' => '$99/mo', 'popular' => true, 'badge' => 'Most popular' ),
    array( 'name' => 'Enterprise', 'price' => 'Custom' ),
  ),
  'groups' => array(
    array( 'label' => 'Messaging & core features', 'rows' => array(
      array( 'feature' => '1:1 and group messaging', 'values' => array( true, true, true ) ),
    ) ),
    array( 'label' => 'Capacity', 'rows' => array(
      array( 'feature' => 'Monthly active users', 'values' => array( '1,000', '5,000', 'Unlimited' ) ),
    ) ),
    array( 'label' => 'Support & deployment', 'rows' => array(
      array( 'feature' => 'Self-hosted deployment', 'values' => array( false, false, true ) ),
    ) ),
  ),
) );
```

Live use: the **Feature Matrix** section on `page-chat-sdk-android.php`.

---

## 20. Vendor comparison — `section-vendor-comparison.php`

An "us vs the competitors" comparison: one capability per row across a **featured vendor** +
N competitor columns. The featured vendor (e.g. Ethora) is a **raised brand-blue card**
(logo + name + subtitle in the header, a white check — or a dot for a neutral row — plus the
value per row, and a darker "wins" footer); each competitor is a plain column whose weak rows
get a **red ✕ + muted text** (neutral rows are plain text, no mark). Closes with a "where we
win" row (counts per column) and an optional footnote. Built on **CSS Grid** (not a table) so
every row aligns across columns; the featured card is an absolutely-positioned overlay so it
floats without consuming a grid track. Self-contained (CSS once per request), tokens only,
horizontal-scroll on narrow screens.

![Vendor comparison](screenshots/vendor-comparison.png)

**Props**

| Prop | Type | Notes |
|---|---|---|
| `eyebrow` / `title` / `lead` | string | optional centred header |
| `shade` | bool | tint the section bg |
| `capabilities` | array | **required** — the row labels (strings, inline HTML ok), in order |
| `featured` | array | **required** — the highlighted column: `name`, `subtitle`, `logo` (theme-relative/URL, default `images/Logo.svg`), `wins` (footer text), `values[]` **aligned to `capabilities`** — a plain string → check + text, or `array('text'=>…, 'neutral'=>true)` → dot instead of check |
| `competitors` | array | **required** — each a column: `name`, `subtitle`, `wins`, `values[]` aligned to `capabilities` — a plain string → neutral text (no mark), or `array('text'=>…, 'bad'=>true)` → red ✕ + muted text |
| `wins_label` | string | left-column label for the wins row (e.g. `Where Ethora wins`). The wins row is skipped entirely when neither `wins_label` nor the featured `wins` is set |
| `marks` | bool | default `true`. Set `false` for **text-only cells** (no check / dot / ✕) — use when values are prose rather than Yes/No, so marks would clutter. Live use: the self-hosted-server comparison on `page-self-hosted-server.php` |
| `note` | string | footnote under the grid (HTML, optional) — rendered as a centred soft-blue **"takeaway" callout** (lightbulb icon tile + kicker + text) |
| `note_label` | string | kicker above the note callout (default `The bottom line`; set `''` to hide) |

```php
get_template_part( 'template-parts/section-vendor-comparison', null, array(
  'title'        => 'How Ethora Compares to Other Android Chat SDKs',
  'capabilities' => array( 'Android SDK language', 'Self-hosted option', 'AI chatbots built-in' ),
  'featured'     => array(
    'name' => 'Ethora', 'subtitle' => 'Open-source · self-hostable', 'logo' => 'images/Logo.svg',
    'wins' => '7 of 9 categories',
    'values' => array( array( 'text' => 'Kotlin (+ Java)', 'neutral' => true ), 'Yes', 'Yes (RAG, LLM)' ),
  ),
  'competitors'  => array(
    array( 'name' => 'Stream', 'subtitle' => 'Cloud only', 'wins' => '2 / 9',
           'values' => array( 'Kotlin', array( 'text' => 'No', 'bad' => true ), array( 'text' => 'No', 'bad' => true ) ) ),
    // …more competitors
  ),
  'wins_label'   => 'Where Ethora wins',
  'note'         => '…',
) );
```

Live use: the **How Ethora Compares** section on `page-chat-sdk-android.php`. (Distinct from
`section-comparison.php`, which is a 2-column build-vs-recommended comparison.)

---

## 21. Feature rows — `section-feature-rows.php`

A vertical stack of white **row cards**: a soft-blue icon tile on the left, a heading +
description in the middle, and one or more **status pills** on the right (e.g. a green
"✓ Available" + a blue "⚙ Customizable"). Hover lifts the card. Good for a
"feature × availability/customizable" list or any capability rundown with per-row badges.
Self-contained (CSS once per request), tokens only; on ≤720px the pills wrap below the text.

![Feature rows](screenshots/feature-rows.png)

**Props**

| Prop | Type | Notes |
|---|---|---|
| `eyebrow` / `title` / `lead` | string | optional centred header |
| `shade` | bool | tint the section bg |
| `items` | array | **required** — each: `icon` (line SVG `stroke="currentColor"`, shown in the blue tile), `title`, `text` (inline HTML), `tags[]` (right-side pills) |
| `items[].tags[]` | array | each pill: `label`, `variant` (`success` = green, `brand` = blue, `muted` = grey), `icon` (raw SVG, optional — e.g. a check or gear) |

```php
$ic  = 'width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"';
$tic = 'width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"';
get_template_part( 'template-parts/section-feature-rows', null, array(
  'shade' => true,
  'title' => 'Feature Availability & Customization',
  'items' => array(
    array(
      'title' => 'Chat / Messaging', 'text' => 'Real-time communication…',
      'icon'  => '<svg '.$ic.'>…</svg>',
      'tags'  => array(
        array( 'label' => 'Available',    'variant' => 'success', 'icon' => '<svg '.$tic.'><path d="M20 6 9 17l-5-5"/></svg>' ),
        array( 'label' => 'Customizable', 'variant' => 'brand',   'icon' => '<svg '.$tic.'>…gear…</svg>' ),
      ),
    ),
    // …
  ),
) );
```

Live use: the **Feature Availability** section on `page-chat-sdk-android.php`.

---

## 22. Code + config covers — `section-code-config.php`

A developer-facing "show the code" section: an eyebrow / title / lead, then a two-column
grid — a **syntax-highlighted code editor mockup** on the left (dark `--ink` panel with
traffic-light dots + a filename tab + a working **Copy** button) and a labelled **"what
config covers"** stack of cards on the right (soft-blue icon tile + a mono **chip** label +
a short description). Closes with a footnote (inline `<code>` chips + a link). Built for the
"Customize the chat UI" style block. Self-contained (CSS + copy JS once), tokens for the UI;
the code panel uses literal hex for syntax colours (same precedent as `section-quick-start`).
Stacks to one column ≤960px.

![Code + config covers](screenshots/code-config.png)

**Props**

| Prop | Type | Notes |
|---|---|---|
| `eyebrow` / `title` / `lead` | string | header (title/lead inline-HTML-friendly) |
| `shade` | bool | tint the section bg |
| `file` | string | filename shown in the editor tab (default `App.tsx`) |
| `code` | string | **trusted pre-highlighted HTML** — wrap tokens in spans: `t-kw` keyword, `t-str` string, `t-com` comment, `t-fn` function/name, `t-tag` JSX tag, `t-prop` property/accent, `t-punc` punctuation. Escape `<`/`>` as `&lt;`/`&gt;`. Pass via a nowdoc (`<<<'CODE'`). |
| `covers_label` | string | right-column label (HTML ok — highlight a word with `<span class="ccf-hl">…</span>`) |
| `items` | array | the config cards — each: `icon` (line SVG `stroke="currentColor"`), `label` (mono chip), `text` |
| `footnote` | string | closing paragraph (HTML; `<code>` renders as a chip) |

```php
$code = <<<'CODE'
<span class="t-kw">import</span> { <span class="t-fn">Chat</span> } <span class="t-kw">from</span> <span class="t-str">"@ethora/chat-component"</span>;
CODE;
get_template_part( 'template-parts/section-code-config', null, array(
  'eyebrow'      => 'React chat component',
  'title'        => 'Customize the chat UI to match your brand',
  'lead'         => '…',
  'file'         => 'App.tsx',
  'code'         => $code,
  'covers_label' => 'What <span class="ccf-hl">config</span> covers',
  'items'        => array(
    array( 'label' => 'colors & typography', 'text' => '…', 'icon' => '<svg …>…</svg>' ),
    // …
  ),
  'footnote'     => 'The full <code>IConfig</code> exposes 60+ options…',
) );
```

Live use: the **Customization** section on `page-chat-sdk-reactjs-web.php`.

---

## 23. Feature list + media — `section-feature-list-media.php`

A two-column split: a vertical **list of icon + heading + description rows** (hairline
dividers between them, in-text links allowed) on one side, and a **framed product image**
on the other — or a tasteful dashed **"drop an image" placeholder** empty-state when no
`media` is passed. Optional eyebrow + title header above the grid. `reverse` puts the media
on the left. Self-contained (CSS once), tokens only; stacks to one column ≤900px.

![Feature list + media](screenshots/feature-list-media.png)

**Props**

| Prop | Type | Notes |
|---|---|---|
| `eyebrow` / `title` / `lead` | string | optional header (left-aligned, above the grid) |
| `shade` | bool | tint the section bg |
| `reverse` | bool | media on the LEFT |
| `media` / `media_alt` | string | framed image (theme-relative/URL). Omit → dashed placeholder |
| `media_shadow` | bool | framed image casts a soft drop shadow (`--shadow-lift`) by default; pass `false` for a flat, shadowless visual |
| `placeholder` | string | placeholder caption (default "Drop a product screenshot or brand image") |
| `items` | array | **required** — each: `icon` (line SVG `stroke="currentColor"`), `title`, `text` (inline HTML — links render brand-blue/underlined) |

```php
$ic = 'width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"';
get_template_part( 'template-parts/section-feature-list-media', null, array(
  'eyebrow' => 'Why Ethora',
  'title'   => 'Why developers choose Ethora over alternatives',
  'media'   => 'images/product-light.png',
  'items'   => array(
    array( 'title' => 'Open source with self-hosting', 'text' => '… <a href="/chat-sdk/self-hosted-chat-server-aws/">deploy on your own servers</a> …', 'icon' => '<svg '.$ic.'>…</svg>' ),
    // …
  ),
) );
```

Live use: the **Why developers choose Ethora** section on `page-chat-sdk-reactjs-web.php`.

---

## 24. Trust band — `section-trust-band.php`

A **full-bleed dark brand band** (the `.shs-dark` treatment: `--primary-dark` tinted over an
image) with a small mono label, a centred row of **customer logos** (white-knocked-out) and a
grid of **headline stats** (big serif number + caption). For a "trusted by / by the numbers"
strip right under a hero. Self-contained (CSS once), tokens only, responsive (stats 4 → 2 → 1).

![Trust band](screenshots/trust-band.png)

**Props**

| Prop | Type | Notes |
|---|---|---|
| `label` | string | small uppercase mono caption (e.g. "Trusted by healthcare teams…") |
| `logos` | array | each: `src` (theme-relative/URL — the `brightness(0) invert(1)` filter knocks it white) + `alt` |
| `stats` | array | each: `num` (big number/word, inline HTML ok) + `cap` (caption) — the grid column count follows the stat count |
| `image` | string | background image (default `images/testimonials.png`) |

```php
get_template_part( 'template-parts/section-trust-band', null, array(
  'label' => 'Trusted by healthcare teams and clinical networks',
  'logos' => array(
    array( 'src' => 'images/DrTalks-logo.svg', 'alt' => 'DrTalks' ),
    // …
  ),
  'stats' => array(
    array( 'num' => '100%', 'cap' => 'HIPAA-compliant patient and provider messaging' ),
    array( 'num' => 'HIPAA &middot; SOC&nbsp;2 &middot; GDPR', 'cap' => 'Compliance-ready, BAA/DPA available' ),
    // …
  ),
) );
```

Live use: right under the hero on `page-self-hosted-server.php` and `page-healthcare.php`.
(Distinct from the **Stats band** #18, which is the brand-blue gradient metrics band — this
one is the darker `.shs-dark` logos-and-stats trust strip.)

---

## 25. FAQ accordion — `section-faq.php`

The standard **FAQ accordion** (the healthcare-page design): bordered question cards; clicking
a question expands its answer, one open at a time. Styling is the theme's **global `.faq*` CSS**
(`css/index.css`) and the toggle is the **global handler in `main.js`** — the partial only emits
markup, so it looks and behaves like every other FAQ on the site. Use it for ANY page FAQ instead
of hand-writing `.faq-item` blocks.

**Props**

| Prop | Type | Notes |
|---|---|---|
| `items` | array | **required** — each: `q` (question), `a` (answer: a string, or an array of paragraph strings; inline HTML/links ok), optional `open` (force this item open/closed) |
| `title` | string | heading (default `FAQ`) |
| `subheader` | string | intro line under the heading (optional, HTML ok) |
| `id` | string | section id for anchor links (e.g. `faq`) (optional) |
| `open_first` | bool | expand the first item by default (default `true`) |
| `schema` | bool | also emit `FAQPage` JSON-LD built from the items (default `false` — omit if the page already outputs its own FAQ schema) |

```php
get_template_part( 'template-parts/section-faq', null, array(
  'title' => 'FAQ',
  'items' => array(
    array( 'q' => 'Can I deploy on my own infrastructure?', 'a' => 'Yes, you can use your own servers or cloud (for example, AWS).' ),
    array( 'q' => 'Multi-paragraph answer?', 'a' => array( 'First paragraph.', 'Second, with a <a href="/contact/">link</a>.' ) ),
  ),
) );
```

Live use: `page-self-hosted-server.php`. (The page keeps its own hand-written `FAQPage`
JSON-LD, so `schema` is left off there.)

---

## Other partials

List everything with `ls template-parts/` and read each file's top docblock for its
params: `section-choose-app`, `section-use-kit`, `section-quick-start`,
`section-pricing`, `section-cta`, `section-testimonials`, `section-book-call-modal`.

`section-use-kit` (sticky illustration + benefits list) supports **`'sticky' => true`** (the
left image sticks while the list scrolls, like `.shs-whatis`) and **inline-SVG benefit icons**
(pass a raw `<svg stroke="#fff"…>` instead of a filename for a thematic icon on the blue tile).
Also: **`'outro_icon' => '<svg…>'`** renders the `outro` as a soft-blue **callout box** with the
icon (wrap the lead in `<strong>` for a bold kicker), and **`'modifier' => 'use-kit-cards'`** makes
each benefit row lift into a white card on hover (elevation + shadow).

---

## Core UI primitives (LOCKED — reuse exactly, never restyle)

Below the section blocks sit four **primitives**. Unlike blocks (which you compose),
these are the single canonical form for their job. There is exactly one of each — reuse
the class/markup verbatim; do **not** invent a variant, change the radius/colour, or
hand-roll a new one. All are tokenised.

> **The canonical CSS for the primitives ships in `css/primitives.css`.** Load it after
> `css/tokens.css` and use these exact classes — do **not** re-implement the rules in your
> own stylesheet. One import gives every consumer the brand's buttons, slider/nav buttons
> and toggle, wired to the tokens.
>
> ```html
> <link rel="stylesheet" href="css/tokens.css">
> <link rel="stylesheet" href="css/primitives.css">
> ```

### P1. CTA buttons — `.btn .btn-primary` / `.btn-outline`

The brand CTAs (Get started / Book a Call). Radius `--radius-btn` (12px), Open Sans 600.
Primary = `--primary` bg + white (hover `--primary-dark` + lift); outline = transparent +
`2px solid --primary` + primary text (hover `--primary-light`). On dark / bright-blue (hero
v2) panels use the **white** variants: `.btn-light` (white fill, blue text) / `.btn-outline-light`
(transparent, 2px white border, white text → fills white on hover) / `.btn-ghost` (translucent).

![CTA buttons](screenshots/primitive-buttons.png)

```html
<a href="https://app.chat.ethora.com/register" class="btn btn-primary" id="accregred">Get started</a>
<a href="#" class="btn btn-outline book-demo-button">Book a Call</a>   <!-- opens the Book-a-Call modal -->
```

**CSS:** `css/primitives.css` (`.btn` + `.btn-primary` / `.btn-outline` / `.btn-light` /
`.btn-outline-light` / `.btn-ghost`). In the live theme the same rules also exist in `css/index.css`; the
token-based partial variants (`.shs-btn*` in `section-hero`/`section-cta-dark`, `.ppc-btn`)
follow the identical spec. **Never** create a pill or other-radius CTA.

### P2. Slider / nav (switch) buttons — `.slider-btn`

Prev/next for **any** carousel or slider (as in *Our Case Studies*). 40px (2.5rem) square,
radius `--radius-btn` (12px), `1px solid --primary` border, transparent bg, `--primary`
chevron, hover → `--primary-light`, centred. On a dark/brand background add `.light`
(white border + chevron, hover translucent white).

![Slider nav buttons](screenshots/primitive-slider-btn.png)

```html
<div class="slider-controls">
  <button class="slider-btn prev" type="button" aria-label="Previous">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
  </button>
  <button class="slider-btn next" type="button" aria-label="Next">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
  </button>
</div>
```

**CSS:** `css/primitives.css` (`.slider-controls`, `.slider-btn`, `.slider-btn.light`).
Always `type="button"` + `aria-label` (icon-only). **Never** invent another nav-button style.

### P3. Toggle switch — `.ppc-toggle` / `.ppc-tg`

Any binary/segmented toggle (Monthly/Yearly, tabs). A `--radius-pill` track on `--white`
with a `--border`; segments in Open Sans 600; the **active segment filled `--ink` + white
text**. An inline accent (e.g. a "15% OFF" tag) uses `--primary` (`--accent-on-dark` when active).

![Toggle switch](screenshots/primitive-toggle.png)

```html
<div class="ppc-toggle" role="group" aria-label="Billing period">
  <button type="button" class="ppc-tg active" data-bill="monthly">Monthly</button>
  <button type="button" class="ppc-tg" data-bill="yearly">Yearly <span class="ppc-off">15% OFF</span></button>
</div>
```

**CSS:** `css/primitives.css` (`.ppc-toggle`, `.ppc-tg`, `.ppc-off`); the block that uses it
wires the `.active` toggle in JS (see `section-pricing-cards.php`). **Never** build a bespoke switch/toggle.

### P4. Brand-blue section background — `--gradient-brand`

The single blue-fill background for a **full-bleed section**: token `--gradient-brand`
(`linear-gradient(135deg, var(--brand-500) 0%, var(--brand-800) 100%)`). Used by the Blue
statement band (#3), Cards carousel (#13 `.cc-section`), Stats band (#18 `.sb-section`) and
the Feature-spotlight flagship. White text on it: heading `#fff`, body `--text-on-dark`,
eyebrow `--accent-on-dark`.

![Brand-blue section background](screenshots/split-card-dark.png)

```css
.my-section { background: var(--gradient-brand); padding: var(--section-y) var(--section-x); }
```

**Never** hand-write that `linear-gradient` inline — reference the token, so one edit
recolours every blue section. This is the *blue* fill; near-black is never allowed, and
dark/CTA panels use the separate `.shs-dark` image treatment (#9).

---

## How these screenshots were made

Local must be running. From the theme root:

```bash
# playwright + system Chrome; hide the fixed .header so it doesn't overlap;
# screenshot each block by CSS selector. See the script used in the repo history.
node <script>.mjs ".claude/skills/ethora-theme/references/screenshots"
# then downsize: sips --resampleWidth 1200 screenshots/*.png
```

Selectors: `.shs-split-section`, `.shs-kf-section`, `.shs-fc-section`,
`section:has(.shs-bento-grid)`, `.cta-dark`, `.ppc`, `.tcar`, `.case-studies`,
`.dep-section`, `.cmpl-section`, `.sb-section`.
Re-run after editing a block to refresh its screenshot.
