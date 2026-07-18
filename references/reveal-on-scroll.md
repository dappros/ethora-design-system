# Reveal-on-scroll — the default block-appearance animation

Every marketing page (new build **or** redesign) ships this by default: content blocks
**fade + slide in** as they scroll into the viewport (headers rise, side blocks slide from
their side, grids/lists cascade). It's the same mechanism the home page (`index.php`) uses.
Live reference: **`page-self-hosted-llm-ai-agent.php`**.

Self-contained per page: one `<style>` block + one inline `<script>` (kept **off `main.js`**
so an unrelated error there can't disable it). Respects `prefers-reduced-motion`. If JS is off,
nothing is ever hidden.

---

## 1. CSS — paste into the page `<style>` (scope `.pageclass` to your `<main>` class)

```css
/* the page slide is contained horizontally without a scrollbar; clip (not hidden) keeps
   position: sticky working inside .pageclass */
.pageclass { overflow-x: clip; }

@media (prefers-reduced-motion: no-preference) {
  /* `[data-reveal][data-reveal]` DOUBLES the attribute to raise specificity above a card's
     own `.card { transition: transform .3s… }` — otherwise that wins by source order and drops
     opacity, so the reveal "pops". We animate the INDEPENDENT `translate` property (never the
     card's hover `transform`), and keep the hover transitions (.3s) in the same list. */
  [data-reveal][data-reveal] {
    opacity: 0;
    transition: opacity .8s cubic-bezier(.22, 1, .36, 1),
                translate .8s cubic-bezier(.22, 1, .36, 1),
                transform .3s ease, box-shadow .3s ease, border-color .3s ease;
    will-change: opacity, translate;
  }
  [data-reveal].reveal-left  { translate: -90px 0; }
  [data-reveal].reveal-right { translate: 90px 0; }
  [data-reveal].reveal-up    { translate: 0 56px; }
  [data-reveal].reveal-in    { opacity: 1; translate: none; }
}
```

## 2. JS — paste before `get_footer()`; edit only the `SELECTORS` list

```html
<script>
  (function () {
    if (!("IntersectionObserver" in window)) return;
    if (window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;
    function init() {
      // [selector] → direction auto-detected from the block's horizontal centre.
      // [selector, "left"|"right"|"up"] → forced direction.
      var SELECTORS = [
        [".pageclass .block-head", "up"], [".pageclass .block-card"],
        // …one entry per block: header ("up") + its repeating cards/items (auto → cascade)
      ];
      var seen = [];
      SELECTORS.forEach(function (entry) {
        var sel = entry[0], forced = entry[1] || "";
        document.querySelectorAll(sel).forEach(function (el) {
          if (seen.indexOf(el) === -1) { seen.push(el); el.setAttribute("data-reveal", forced); }
        });
      });
      if (!seen.length) return;
      var vw = window.innerWidth;
      seen.forEach(function (el) {
        var forced = el.getAttribute("data-reveal"), dir;
        if (forced === "left" || forced === "right" || forced === "up") {
          dir = "reveal-" + forced;
        } else {
          var r = el.getBoundingClientRect(), c = r.left + r.width / 2;
          dir = c < vw * 0.42 ? "reveal-left" : c > vw * 0.58 ? "reveal-right" : "reveal-up";
        }
        el.classList.add(dir);
        // stagger siblings that share a parent (grids / lists) → cascade
        var parent = el.parentElement;
        if (parent) {
          var group = Array.prototype.filter.call(parent.children, function (ch) {
            return ch.hasAttribute && ch.hasAttribute("data-reveal");
          });
          var idx = group.indexOf(el);
          if (idx > 0) el.style.transitionDelay = Math.min(idx, 6) * 0.09 + "s";
        }
      });
      var io = new IntersectionObserver(function (entries, obs) {
        entries.forEach(function (e) {
          if (e.isIntersecting) { e.target.classList.add("reveal-in"); obs.unobserve(e.target); }
        });
      }, { threshold: 0.15, rootMargin: "0px 0px -8% 0px" });
      seen.forEach(function (el) { io.observe(el); });
    }
    if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", init);
    else init();
  })();
</script>
```

## 3. Building the `SELECTORS` list

- **One entry per block**: the block's **header** (`… -head` / `-eyebrow` — force `"up"`) plus its
  **repeating cards/items** (`… -card` / `-item`, no direction → the script auto-picks
  left/right/up by position and **staggers** siblings into a cascade).
- **Scope every selector to your `<main>` class** (`.pageclass …`) so the site header/footer
  menus are never tagged. Sections rendered *outside* `<main>` (e.g. a closing `section-faq`)
  need their own unscoped selector (e.g. `.faq-item`).
- Find the real class names from the rendered HTML (`curl … | grep -oE 'class="…"'`).

## 4. Non-negotiable gotchas

- **Animate `translate`, not `transform`.** Cards use `transform: scale()` on hover with their
  own short transition — reusing `transform` for the reveal makes them fight (opacity pops, hover
  jumps). `translate` is an independent property, so both compose cleanly.
- **`[data-reveal][data-reveal]`** (doubled attribute) is required so the reveal `transition`
  out-specifies the cards' own `transition` shorthand. Without it, `opacity` isn't animated and
  the block appears abruptly.
- **`overflow-x: clip`** on the `<main>` wrapper contains the horizontal slide (no stray
  scrollbar) **and** — unlike `overflow: hidden` — does **not** create a scroll container, so
  `position: sticky` descendants keep working.
- **Never tag a `position: sticky` element** (its `translate`/`transform` breaks the stick).
  Exclude it — e.g. target `… > div:first-child`, not the sticky column.
- **Never tag the hero** (it's above the fold; it must be visible on load).
- **Reduced-motion**: the hidden state lives inside `@media (prefers-reduced-motion:
  no-preference)` and the script bails on `reduce` — so those users, and any JS-off visitor,
  see everything visible with no animation.
