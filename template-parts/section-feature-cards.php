<?php
/**
 * Reusable feature cards.
 *
 * A responsive grid of cards on the brand gradient, each with a coloured circular
 * icon, a heading, a short description and an optional "Learn more" link.
 * Self-contained: ships its own CSS once per request; works with ANY number of
 * cards (auto-fit grid).
 *
 * Pass props via the 3rd arg of get_template_part():
 *
 *   get_template_part( 'template-parts/section-feature-cards', null, array(
 *     'eyebrow' => '',                       // optional mono kicker
 *     'title'   => 'Section heading',        // h2 (inline HTML allowed, optional)
 *     'lead'    => 'Optional intro.',         // (optional)
 *     'shade'   => false,                     // tint the section bg + hairline borders (optional)
 *     'expandable' => false,                  // clamp long card text + animated "Read more" toggle (optional)
 *     'clamp'      => '8.5em',                // collapsed text height when expandable (optional)
 *     'lead_wide'  => false,                  // lead spans the full container width instead of the 560px column (optional)
 *     'cards'   => array(                     // REQUIRED — any length
 *       array(
 *         'title'      => 'Selling',
 *         'text'       => 'Short description.',   // inline HTML allowed
 *         'icon'       => '<svg …>…</svg>',       // white line icon (stroke="currentColor"), optional
 *         'color'      => 'var(--primary)',       // icon-circle colour token (optional, default primary)
 *         'link_url'   => '/path/',               // optional — renders a "Learn more" button
 *         'link_label' => 'Learn more',           // optional (default "Learn more")
 *       ),
 *       // …
 *     ),
 *   ) );
 *
 * @package ethora-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$fc = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'eyebrow' => '',
		'title'   => '',
		'lead'    => '',
		'shade'         => false,
		'shade_borders' => true,   // when shaded, the tint band gets top/bottom hairline borders by default; pass false to drop them (keep the tint)
		'expandable'    => false,
		'clamp'         => '8.5em',
		'lead_wide'     => false,  // lead spans the full container width instead of the default 560px column
		'cards'         => array(),
	)
);

if ( empty( $fc['cards'] ) || ! is_array( $fc['cards'] ) ) {
	return;
}

// Emit the shared CSS only once per request, no matter how many instances.
$fc_assets = empty( $GLOBALS['shs_fc_assets'] );
if ( $fc_assets ) {
	$GLOBALS['shs_fc_assets'] = true;
}
?>
<?php if ( $fc_assets ) : ?>
<style>
  /* FEATURE CARDS — coloured icon + heading + text on the brand gradient. Tokens only. */
  .shs-fc-section { padding: var(--section-y) var(--section-x); }
  .shs-fc-section, .shs-fc-section *, .shs-fc-section *::before, .shs-fc-section *::after { box-sizing: border-box; }
  .shs-fc-section.is-shaded { background: var(--surface-alt); border-top: 1px solid var(--hairline); border-bottom: 1px solid var(--hairline); }
  .shs-fc-section.is-shaded.no-shade-borders { border-top: none; border-bottom: none; }   /* opt-out via 'shade_borders' => false */
  .shs-fc-wrap { max-width: var(--content-max); margin: 0 auto; }
  .shs-fc-eyebrow { font-family: var(--font-mono); font-weight: var(--fw-medium); font-size: var(--fs-eyebrow); letter-spacing: var(--tracking-wide); text-transform: uppercase; color: var(--primary); margin: 0 0 var(--space-8); }
  .shs-fc-h2 { font-family: var(--font-serif); font-weight: var(--fw-medium); font-size: var(--fs-h2); line-height: 1.08; letter-spacing: -.01em; color: var(--ink); margin: 0; max-width: 640px; }
  .shs-fc-lead { font-size: var(--fs-lg); line-height: var(--lh-relaxed); color: var(--text-body); margin: var(--space-16) 0 0; max-width: 560px; }
  .shs-fc-section.is-lead-wide .shs-fc-lead { max-width: none; }   /* lead_wide => true */
  .shs-fc-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: var(--space-16); margin-top: var(--space-48); }
  /* expandable: flex layout so an opened card can take the FULL container width and
     move to the top, while the remaining collapsed cards reflow below and fill their
     row. Opening therefore expands sideways, not just downward. */
  /* align-items: stretch → every card in a flex row is the height of its tallest sibling
     (uneven heading/text lengths never leave ragged, different-height cards in a row). The
     .shs-fc-more / toggle uses margin-top:auto, so the "Read more" line stays bottom-aligned. */
  .shs-fc-section.is-expandable .shs-fc-grid { display: flex; flex-wrap: wrap; align-items: stretch; }
  .shs-fc-section.is-expandable .shs-fc-card { flex: 1 1 280px; }
  .shs-fc-section.is-expandable .shs-fc-card.is-open { flex-basis: 100%; order: -1; }
  /* full-width open card: flow the long text into readable columns so the width is used well */
  .shs-fc-section.is-expandable .shs-fc-card.is-open .shs-fc-text { columns: 2 20em; column-gap: clamp(var(--space-32), 4vw, var(--space-64)); }
  .shs-fc-card { display: flex; flex-direction: column;
    background: linear-gradient(-124deg, rgba(255,255,255,1) 0%, rgba(0,82,205,.16) 72%, rgba(255,255,255,1) 100%);
    border: 1px solid var(--border); border-radius: var(--radius-3xl); padding: clamp(var(--space-32),3vw,var(--space-48)); }
  .shs-fc-ico { flex: none; width: 52px; height: 52px; border-radius: var(--radius-pill); color: #fff;
    display: flex; align-items: center; justify-content: center; margin-bottom: var(--space-16); }
  .shs-fc-ico svg { width: 24px; height: 24px; }
  .shs-fc-card h3 { font-family: var(--font-serif); font-weight: var(--fw-semibold); font-size: var(--fs-h3-lg);
    line-height: var(--lh-snug); letter-spacing: -.01em; color: var(--ink); margin: 0; }
  .shs-fc-text { font-size: var(--fs-md); line-height: var(--lh-relaxed); color: var(--text-body); margin: var(--space-16) 0 0; }
  .shs-fc-text strong, .shs-fc-text b { color: var(--ink); font-weight: var(--fw-semibold); }
  .shs-fc-text a { color: var(--primary); text-decoration: underline; text-underline-offset: 2px; }
  .shs-fc-more { align-self: flex-start; margin-top: auto; display: inline-flex; align-items: center; gap: var(--space-8);
    padding: var(--space-8) var(--space-16); background: #fff; border: 1px solid var(--border); border-radius: var(--radius-pill);
    font-family: var(--font-mono); font-weight: var(--fw-semibold); font-size: var(--fs-xs); letter-spacing: var(--tracking-wide);
    text-transform: uppercase; color: var(--ink); text-decoration: none; transition: background .25s ease, gap .25s ease; }
  .shs-fc-more:hover { background: var(--tint); gap: var(--space-16); }
  .shs-fc-card .shs-fc-text + .shs-fc-more { margin-top: var(--space-16); }
  /* ---- Expandable text (Read more) ---- */
  .shs-fc-clip { margin-top: var(--space-16); }
  .shs-fc-clip .shs-fc-text { margin: 0; }
  /* Fixed-height preview clamp — the JS measures scrollHeight against this clientHeight to
     decide whether a "Read more" is needed, so it must stay a definite height (not flex-grow). */
  .shs-fc-clip.is-collapsible { position: relative; overflow: hidden; max-height: var(--fc-clamp, 8.5em);
    transition: max-height .55s cubic-bezier(.4, 0, .2, 1);
    -webkit-mask-image: linear-gradient(to bottom, #000 58%, transparent 100%);
            mask-image: linear-gradient(to bottom, #000 58%, transparent 100%); }
  .shs-fc-clip.is-collapsible.is-open { max-height: none; -webkit-mask-image: none; mask-image: none; }
  /* margin-top:auto pins the toggle to the card's bottom edge so every "Read more" in a row
     sits on the same line (cards are equal-height); padding-top keeps a min gap above it even
     on the tallest card where auto collapses to 0. */
  .shs-fc-toggle { align-self: flex-start; margin: auto 0 0; padding-top: var(--space-16); display: inline-flex; align-items: center; gap: var(--space-8);
    background: none; border: 0; border-radius: 0;
    font-family: var(--font-mono); font-weight: var(--fw-semibold); font-size: var(--fs-xs); letter-spacing: var(--tracking-wide);
    text-transform: uppercase; color: var(--primary); cursor: pointer; transition: color .2s ease, gap .2s ease; }
  .shs-fc-toggle:hover { color: var(--primary-dark); gap: var(--space-16); }
  .shs-fc-toggle svg { width: 14px; height: 14px; transition: transform .35s cubic-bezier(.4, 0, .2, 1); }
  .shs-fc-toggle[aria-expanded="true"] svg { transform: rotate(180deg); }
  .shs-fc-toggle[hidden] { display: none; }
  @media (prefers-reduced-motion: reduce) { .shs-fc-clip.is-collapsible, .shs-fc-toggle svg { transition: none; } }
  @media (max-width: 900px) { .shs-fc-grid { grid-template-columns: 1fr; } }
</style>
<?php endif; ?>

<section class="shs-fc-section<?php echo $fc['shade'] ? ' is-shaded' : ''; ?><?php echo ( $fc['shade'] && ! $fc['shade_borders'] ) ? ' no-shade-borders' : ''; ?><?php echo $fc['expandable'] ? ' is-expandable' : ''; ?><?php echo $fc['lead_wide'] ? ' is-lead-wide' : ''; ?>">
  <div class="shs-fc-wrap">
    <?php if ( $fc['eyebrow'] || $fc['title'] || $fc['lead'] ) : ?>
    <div>
      <?php if ( $fc['eyebrow'] ) : ?><p class="shs-fc-eyebrow"><?php echo esc_html( $fc['eyebrow'] ); ?></p><?php endif; ?>
      <?php if ( $fc['title'] ) : ?><h2 class="shs-fc-h2"><?php echo wp_kses_post( $fc['title'] ); ?></h2><?php endif; ?>
      <?php if ( $fc['lead'] ) : ?><p class="shs-fc-lead"><?php echo wp_kses_post( $fc['lead'] ); ?></p><?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="shs-fc-grid">
      <?php
      foreach ( $fc['cards'] as $card ) :
		$card = wp_parse_args( (array) $card, array( 'title' => '', 'text' => '', 'icon' => '', 'color' => 'var(--primary)', 'link_url' => '', 'link_label' => 'Learn more' ) );
		?>
      <div class="shs-fc-card">
        <?php if ( $card['icon'] ) : ?>
        <span class="shs-fc-ico" aria-hidden="true" style="background:<?php echo esc_attr( $card['color'] ); ?>;"><?php echo $card['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted inline SVG markup ?></span>
        <?php endif; ?>
        <?php if ( $card['title'] ) : ?><h3><?php echo wp_kses_post( $card['title'] ); ?></h3><?php endif; ?>
        <?php if ( $card['text'] ) : ?>
          <?php if ( $fc['expandable'] ) : ?>
          <div class="shs-fc-clip"<?php echo $fc['clamp'] ? ' style="--fc-clamp:' . esc_attr( $fc['clamp'] ) . ';"' : ''; ?>>
            <p class="shs-fc-text"><?php echo wp_kses_post( $card['text'] ); ?></p>
          </div>
          <button class="shs-fc-toggle" type="button" aria-expanded="false" data-more="Read more" data-less="Read less" hidden>
            <span class="shs-fc-toggle-label">Read more</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"></path></svg>
          </button>
          <?php else : ?>
          <p class="shs-fc-text"><?php echo wp_kses_post( $card['text'] ); ?></p>
          <?php endif; ?>
        <?php endif; ?>
        <?php if ( $card['link_url'] ) : ?>
        <a class="shs-fc-more" href="<?php echo esc_url( $card['link_url'] ); ?>"><?php echo esc_html( $card['link_label'] ); ?> <span aria-hidden="true">&rarr;</span></a>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php if ( $fc_assets ) : ?>
<script>
/* Feature-cards "Read more": clamp long text and animate the reveal. Progressive
   enhancement — without JS the full text simply shows and the toggle stays hidden. */
(function () {
  var DUR = 520, EASE = 'cubic-bezier(.4, 0, .2, 1)';
  var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function cardsOf(grid) {
    return Array.prototype.filter.call(grid.children, function (c) { return c.classList && c.classList.contains('shs-fc-card'); });
  }
  function rectsOf(cards, grid) {
    var g = grid.getBoundingClientRect();
    return cards.map(function (c) { var r = c.getBoundingClientRect(); return { l: r.left - g.left, t: r.top - g.top, w: r.width, h: r.height }; });
  }

  // FLIP: measure First, mutate to Last, freeze at First (absolute), then transition
  // left/top/width/height to Last so the whole reflow — width, height and the siblings
  // sliding into place — animates together. No transform scale, so text isn't distorted.
  function animateReflow(grid, mutate) {
    if (reduce) { mutate(); return; }
    var cards = cardsOf(grid);
    var first = rectsOf(cards, grid);
    var firstH = grid.offsetHeight;
    mutate();
    var last = rectsOf(cards, grid);
    var lastH = grid.offsetHeight;

    grid.style.position = 'relative';
    grid.style.height = firstH + 'px';
    grid.style.transition = 'none';
    cards.forEach(function (c, i) {
      var f = first[i], clip = c.querySelector('.shs-fc-clip');
      c.style.boxSizing = 'border-box';
      c.style.position = 'absolute';
      c.style.margin = '0';
      c.style.left = f.l + 'px'; c.style.top = f.t + 'px'; c.style.width = f.w + 'px'; c.style.height = f.h + 'px';
      c.style.overflow = 'hidden';
      c.style.zIndex = c.classList.contains('is-open') ? '2' : '1';  // expanding panel lifts above the sliding ones
      c.style.transition = 'none';
      if (clip) clip.style.maxHeight = 'none';   // let the card's animating height do the clipping
    });
    grid.getBoundingClientRect();                 // reflow so the frozen state paints first

    requestAnimationFrame(function () {
      grid.style.transition = 'height ' + DUR + 'ms ' + EASE;
      grid.style.height = lastH + 'px';
      cards.forEach(function (c, i) {
        var l = last[i];
        c.style.transition = ['left', 'top', 'width', 'height'].map(function (p) { return p + ' ' + DUR + 'ms ' + EASE; }).join(',');
        c.style.left = l.l + 'px'; c.style.top = l.t + 'px'; c.style.width = l.w + 'px'; c.style.height = l.h + 'px';
      });
    });

    setTimeout(function () {
      grid.style.position = ''; grid.style.height = ''; grid.style.transition = '';
      cards.forEach(function (c) {
        c.style.boxSizing = ''; c.style.position = ''; c.style.margin = '';
        c.style.left = ''; c.style.top = ''; c.style.width = ''; c.style.height = '';
        c.style.overflow = ''; c.style.zIndex = ''; c.style.transition = '';
        var clip = c.querySelector('.shs-fc-clip'); if (clip) clip.style.maxHeight = '';
      });
    }, DUR + 40);
  }

  function setup(clip, btn) {
    clip.classList.add('is-collapsible');
    // If the text already fits inside the clamp, no toggle is needed.
    if (clip.scrollHeight <= clip.clientHeight + 2) {
      clip.classList.remove('is-collapsible');
      return;
    }
    btn.hidden = false;
    var label = btn.querySelector('.shs-fc-toggle-label');
    var card = clip.closest('.shs-fc-card');
    var grid = card && card.parentNode;
    var open = false;
    btn.addEventListener('click', function () {
      open = !open;
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
      if (label) label.textContent = open ? btn.getAttribute('data-less') : btn.getAttribute('data-more');
      var mutate = function () {
        if (card) card.classList.toggle('is-open', open);
        clip.classList.toggle('is-open', open);
      };
      if (grid) animateReflow(grid, mutate); else mutate();
    });
  }
  function run() {
    var clips = document.querySelectorAll('.shs-fc-card > .shs-fc-clip');
    for (var i = 0; i < clips.length; i++) {
      var clip = clips[i];
      var btn = clip.nextElementSibling;
      if (btn && btn.classList.contains('shs-fc-toggle')) setup(clip, btn);
    }
  }
  if (document.readyState !== 'loading') run(); else document.addEventListener('DOMContentLoaded', run);
})();
</script>
<?php endif; ?>
