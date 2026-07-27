<?php
/**
 * Reusable sticky TIMELINE NAV — an editorial "Our history" strip: a centred
 * title above a horizontal row of year (or step) labels. Only the YEARS ROW
 * sticks below the fixed header once reached — the title scrolls away with the
 * page. The row fades out at both edges (CSS mask). The ACTIVE label is always kept in the CENTRE of the strip
 * (the row slides under it), enlarged and ink-coloured; the rest stay muted.
 * Clicking a label smooth-scrolls to its target section, with the scroll offset
 * accounting for the fixed header (its real bottom is measured, so the WP admin
 * bar never pushes content under it) + the strip itself. Scrollspy follows the
 * scroll position and re-centres the strip.
 *
 * No background chrome of its own — the strip uses the page canvas colour
 * (var(--background)) so it blends with the page while content scrolls beneath.
 *
 * Self-contained: ships its own CSS/JS once per request, supports multiple
 * instances, tokens only. JS-off ⇒ a plain centred anchor strip (targets get
 * scroll-margin-top).
 *
 * Pass props via the 3rd arg of get_template_part():
 *
 *   get_template_part( 'template-parts/section-timeline-nav', null, array(
 *     'title' => 'Our history',                          // centred heading above the years (optional)
 *     'items' => array(                                  // REQUIRED — 2+ items
 *       array( 'label' => '2004-2007', 'target' => 'era-2004' ),  // target = id of the section
 *       array( 'label' => '2008-2010', 'target' => 'era-2008' ),
 *       // …
 *     ),
 *     'aria_label' => 'History timeline',                 // nav aria-label (optional)
 *   ) );
 *
 * Give every target section the matching id — the partial also sets their
 * scroll-margin-top so native anchor jumps land below the sticky strip.
 * NEVER tag the strip with reveal-on-scroll (position: sticky breaks under a translate).
 *
 * @package ethora-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tln = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'title'      => '',
		'items'      => array(),
		'aria_label' => 'Timeline',
	)
);

if ( empty( $tln['items'] ) || ! is_array( $tln['items'] ) ) {
	return;
}

// Emit the shared CSS/JS only once per request, no matter how many instances.
$tln_assets = empty( $GLOBALS['tln_assets'] );
if ( $tln_assets ) {
	$GLOBALS['tln_assets'] = true;
}
?>
<?php if ( $tln_assets ) : ?>
<style>
  /* TIMELINE NAV — sticky "Our history" year strip. Tokens only; no chrome of its
     own: the strip carries the page canvas colour so it blends with the page. */
  .tln-head { padding: var(--space-32) var(--section-x) 0; }
  .tln-title { font-family: var(--font-serif); font-weight: var(--fw-semibold); font-size: var(--fs-h2);
    line-height: var(--lh-heading); letter-spacing: var(--tracking-snug); color: var(--ink);
    text-align: center; margin: 0; }
  .tln { position: sticky; top: var(--header-h); z-index: calc(var(--z-header) - 10);
    background: var(--background); padding: var(--space-16) 0; }
  /* years fade out toward both edges of the strip */
  .tln-viewport { overflow-x: clip;
    -webkit-mask-image: linear-gradient(to right, transparent 0, #000 12%, #000 88%, transparent 100%);
            mask-image: linear-gradient(to right, transparent 0, #000 12%, #000 88%, transparent 100%); }
  .tln-track { display: flex; align-items: center; justify-content: center;
    gap: clamp(var(--space-32), 5vw, var(--space-80)); width: max-content; margin: 0 auto;
    padding: var(--space-16) var(--space-8); transition: transform .5s cubic-bezier(.22, 1, .36, 1); }
  .tln-item { flex: none; background: none; border: 0; cursor: pointer; padding: var(--space-8);
    font-family: var(--font-sans); font-weight: var(--fw-semibold); font-size: var(--fs-h3-lg);
    line-height: var(--lh-snug); color: var(--text-caption); text-decoration: none;
    transition: color .35s ease, transform .35s ease, font-weight .35s ease; transform-origin: center; white-space: nowrap; }
  .tln-item:hover, .tln-item:focus-visible { color: var(--text-body); }
  .tln-item:focus-visible { outline: 2px solid var(--primary); outline-offset: 2px; border-radius: var(--radius-xs); }
  .tln-item.is-active { color: var(--ink); font-weight: var(--fw-bold); transform: scale(1.6); }
  /* native anchor fallback + scrollspy targets land below the sticky strip */
  [data-tln-target] { scroll-margin-top: calc(var(--header-h) + 140px); }
  @media (prefers-reduced-motion: reduce) { .tln-item, .tln-track { transition: none; } }
</style>
<?php endif; ?>

<?php if ( $tln['title'] ) : ?>
<div class="tln-head"><h2 class="tln-title"><?php echo wp_kses_post( $tln['title'] ); ?></h2></div>
<?php endif; ?>
<nav class="tln" data-tln aria-label="<?php echo esc_attr( $tln['aria_label'] ); ?>">
  <div class="tln-viewport">
    <div class="tln-track" data-tln-track>
      <?php foreach ( $tln['items'] as $tln_i => $tln_it ) : $tln_it = wp_parse_args( (array) $tln_it, array( 'label' => '', 'target' => '' ) ); ?>
      <a class="tln-item<?php echo 0 === $tln_i ? ' is-active' : ''; ?>" href="#<?php echo esc_attr( $tln_it['target'] ); ?>" data-tln-link="<?php echo esc_attr( $tln_it['target'] ); ?>"><?php echo esc_html( $tln_it['label'] ); ?></a>
      <?php endforeach; ?>
    </div>
  </div>
</nav>

<?php if ( $tln_assets ) : ?>
<script>
  (function () {
    function initTLN(nav) {
      var track = nav.querySelector('[data-tln-track]');
      var viewport = nav.querySelector('.tln-viewport');
      var links = Array.prototype.slice.call(nav.querySelectorAll('[data-tln-link]'));
      var targets = links.map(function (l) { return document.getElementById(l.getAttribute('data-tln-link')); });
      if (!links.length) return;
      targets.forEach(function (t) { if (t) t.setAttribute('data-tln-target', ''); });

      // Stick right below the fixed header's REAL bottom edge (survives the WP
      // admin bar and any header height change; falls back to --header-h).
      var headerEl = document.querySelector('.header') || document.querySelector('header');
      function syncTop() {
        if (headerEl && getComputedStyle(headerEl).position === 'fixed') {
          nav.style.top = Math.round(headerEl.getBoundingClientRect().bottom) + 'px';
        }
      }

      function offset() { return nav.getBoundingClientRect().height + parseFloat(getComputedStyle(nav).top || 0) + 16; }

      // Keep the active label in the centre of the strip: slide the whole row so
      // the active item's centre matches the viewport's centre.
      function centre() {
        var it = nav.querySelector('.tln-item.is-active');
        if (!it) return;
        var shift = viewport.clientWidth / 2 - (it.offsetLeft + it.offsetWidth / 2);
        track.style.transform = 'translateX(' + Math.round(shift) + 'px)';
      }

      function setActive(idx) {
        links.forEach(function (l, i) { l.classList.toggle('is-active', i === idx); });
        centre();
      }

      links.forEach(function (l, i) {
        l.addEventListener('click', function (e) {
          var t = targets[i];
          if (!t) return;
          e.preventDefault();
          window.scrollTo({ top: t.getBoundingClientRect().top + window.scrollY - offset(), behavior: 'smooth' });
        });
      });

      var ticking = false;
      function spy() {
        ticking = false;
        syncTop();
        var line = offset() + 8, idx = 0;
        targets.forEach(function (t, i) { if (t && t.getBoundingClientRect().top <= line) idx = i; });
        if (!links[idx].classList.contains('is-active')) setActive(idx);
      }
      window.addEventListener('scroll', function () {
        if (!ticking) { ticking = true; requestAnimationFrame(spy); }
      }, { passive: true });
      window.addEventListener('resize', function () { syncTop(); centre(); });

      syncTop();
      centre();
      spy();
    }
    function boot() { document.querySelectorAll('[data-tln]').forEach(initTLN); }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot);
    else boot();
  })();
</script>
<?php endif; ?>
