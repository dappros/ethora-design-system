<?php
/**
 * Reusable sticky TIMELINE NAV — a horizontal strip of year (or step) labels that
 * sticks below the fixed header once reached. The label whose target section is
 * currently in view is highlighted (bigger, ink-coloured — like an editorial
 * "Our history" strip); clicking a label smooth-scrolls to its section, with the
 * scroll offset accounting for the fixed header + the strip itself. The strip
 * scrolls horizontally when the labels overflow, and auto-centres the active one.
 *
 * Self-contained: ships its own CSS/JS once per request, supports multiple
 * instances, tokens only. Scrollspy is plain rAF-throttled scroll math (no
 * IntersectionObserver thresholds to tune). JS-off ⇒ a plain anchor strip.
 *
 * Pass props via the 3rd arg of get_template_part():
 *
 *   get_template_part( 'template-parts/section-timeline-nav', null, array(
 *     'items' => array(                                  // REQUIRED — 2+ items
 *       array( 'label' => '2004–2007', 'target' => 'era-2004' ),  // target = id of the section
 *       array( 'label' => '2008–2010', 'target' => 'era-2008' ),
 *       // …
 *     ),
 *     'aria_label' => 'History timeline',                 // nav aria-label (optional)
 *   ) );
 *
 * Give every target section the matching id — the partial also sets their
 * scroll-margin-top so native anchor jumps land below the sticky strip.
 *
 * @package ethora-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tln = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
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
  /* TIMELINE NAV — sticky year strip. Tokens only. */
  .tln { position: sticky; top: var(--header-h); z-index: calc(var(--z-header) - 10);
    background: var(--surface); border-bottom: 1px solid var(--hairline); }
  .tln-wrap { max-width: var(--content-max); margin: 0 auto; padding: 0 var(--section-x); }
  .tln-track { display: flex; align-items: center; gap: clamp(var(--space-16), 4vw, var(--space-64));
    overflow-x: auto; scrollbar-width: none; -ms-overflow-style: none; padding: var(--space-16) var(--space-8); }
  .tln-track::-webkit-scrollbar { display: none; }
  .tln-item { flex: none; background: none; border: 0; cursor: pointer; padding: var(--space-8);
    font-family: var(--font-sans); font-weight: var(--fw-semibold); font-size: var(--fs-2xl);
    line-height: var(--lh-snug); color: var(--text-caption); text-decoration: none;
    transition: color .3s ease, transform .3s ease; transform-origin: center; white-space: nowrap; }
  .tln-item:hover, .tln-item:focus-visible { color: var(--text-body); }
  .tln-item:focus-visible { outline: 2px solid var(--primary); outline-offset: 2px; border-radius: var(--radius-xs); }
  .tln-item.is-active { color: var(--ink); font-weight: var(--fw-bold); transform: scale(1.35); }
  /* native anchor fallback + scrollspy targets land below the sticky strip */
  [data-tln-target] { scroll-margin-top: calc(var(--header-h) + 96px); }
  @media (prefers-reduced-motion: reduce) { .tln-item { transition: none; } }
</style>
<?php endif; ?>

<nav class="tln" data-tln aria-label="<?php echo esc_attr( $tln['aria_label'] ); ?>">
  <div class="tln-wrap">
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
      var links = Array.prototype.slice.call(nav.querySelectorAll('[data-tln-link]'));
      var targets = links.map(function (l) { return document.getElementById(l.getAttribute('data-tln-link')); });
      if (!links.length) return;
      targets.forEach(function (t) { if (t) t.setAttribute('data-tln-target', ''); });

      function offset() { return nav.getBoundingClientRect().height + parseFloat(getComputedStyle(nav).top || 0) + 16; }

      function setActive(idx, centre) {
        links.forEach(function (l, i) { l.classList.toggle('is-active', i === idx); });
        if (centre) {
          var it = links[idx];
          track.scrollTo({ left: it.offsetLeft - track.clientWidth / 2 + it.clientWidth / 2, behavior: 'smooth' });
        }
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
        var line = offset() + 8, idx = 0;
        targets.forEach(function (t, i) { if (t && t.getBoundingClientRect().top <= line) idx = i; });
        if (!links[idx].classList.contains('is-active')) setActive(idx, true);
      }
      window.addEventListener('scroll', function () {
        if (!ticking) { ticking = true; requestAnimationFrame(spy); }
      }, { passive: true });
      spy();
    }
    function boot() { document.querySelectorAll('[data-tln]').forEach(initTLN); }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot);
    else boot();
  })();
</script>
<?php endif; ?>
