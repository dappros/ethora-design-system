<?php
/**
 * Reusable horizontal cards carousel.
 *
 * Title + lead on top, then a FULL-WIDTH track of (by default dark, brand
 * `.shs-dark`) cards: text in a column on the left, an image on the right. One card
 * is in focus and the next one peeks from the right edge. Switch with the centred
 * prev/next buttons (the brand `.slider-btn` standard) or by **dragging / swiping**
 * (mouse or touch). Self-contained (CSS + JS once per request), supports multiple
 * instances and any number of cards.
 *
 * Pass props via the 3rd arg of get_template_part():
 *
 *   get_template_part( 'template-parts/section-cards-carousel', null, array(
 *     'eyebrow' => '',                          // optional mono kicker
 *     'title'   => 'Section heading',
 *     'lead'    => 'Intro paragraph.',           // (optional)
 *     'light'   => false,                        // true = white cards instead of dark
 *     'cards'   => array(                         // REQUIRED — any length
 *       array(
 *         'title'     => 'Patient-provider messaging',
 *         'blocks'    => array(                    // stacked, labelled text blocks
 *           array( 'label' => 'The challenge', 'text' => '…' ),
 *           array( 'label' => 'How Ethora helps', 'text' => '…' ),
 *         ),
 *         'image'     => 'images/foo.png',         // theme-relative path or URL (right side)
 *         'image_alt' => 'Describe the image',
 *         'link_url'  => '/path/', 'link_label' => 'Read more',   // optional CTA
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

$cc = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'eyebrow' => '',
		'title'   => '',
		'lead'    => '',
		'light'   => false,
		'cards'   => array(),
	)
);

if ( empty( $cc['cards'] ) || ! is_array( $cc['cards'] ) ) {
	return;
}

$cc_uri = get_template_directory_uri();
$cc_src = function ( $img ) use ( $cc_uri ) {
	if ( ! $img ) {
		return '';
	}
	return preg_match( '#^(https?:)?//#', $img ) ? $img : $cc_uri . '/' . ltrim( $img, '/' );
};

$cc_assets = empty( $GLOBALS['shs_cc_assets'] );
if ( $cc_assets ) {
	$GLOBALS['shs_cc_assets'] = true;
}
?>
<?php if ( $cc_assets ) : ?>
<style>
  /* CARDS CAROUSEL — full-width draggable track; text column + image per card; next card peeks. Tokens only. */
  .cc-section { padding: var(--section-y) 0; overflow-x: hidden; }
  .cc-section, .cc-section *, .cc-section *::before, .cc-section *::after { box-sizing: border-box; }
  .cc-head { max-width: var(--content-max); margin: 0 auto; padding: 0 var(--section-x); }
  .cc-eyebrow { font-family: var(--font-mono); font-weight: var(--fw-medium); font-size: var(--fs-eyebrow); letter-spacing: var(--tracking-wide); text-transform: uppercase; color: var(--primary); margin: 0 0 var(--space-8); }
  .cc-h2 { font-family: var(--font-serif); font-weight: var(--fw-medium); font-size: var(--fs-h2); line-height: 1.08; letter-spacing: -.01em; color: var(--ink); margin: 0; }
  .cc-lead { font-size: var(--fs-lg); line-height: var(--lh-relaxed); color: var(--text-body); margin: var(--space-16) 0 0; }
  /* full-width viewport; the ACTIVE card is centred, its neighbours peek equally on both sides (section clips overflow) */
  .cc-viewport { position: relative; margin-top: var(--space-48); overflow: visible; cursor: grab; }
  .cc-viewport.is-dragging { cursor: grabbing; }
  .cc-viewport.is-dragging .cc-track { user-select: none; }
  .cc-track { display: flex; gap: var(--space-32); align-items: stretch; will-change: transform; }
  .cc-card { flex: 0 0 clamp(300px, 84vw, var(--content-max)); display: flex; flex-direction: column;
    border-radius: var(--radius-3xl); padding: clamp(var(--space-32),3.5vw,var(--space-48));
    background: var(--gradient-brand); color: var(--text-on-dark);
    transform: scale(.93); transform-origin: center center; transition: transform .5s cubic-bezier(.4,0,.2,1); }
  .cc-card.is-active { transform: scale(1); }
  .cc-section.is-light .cc-card { background: #fff; background-image: none; border: 1px solid var(--border); color: var(--text-body); }
  .cc-card-grid { display: grid; grid-template-columns: minmax(0,1fr) minmax(0,.85fr); gap: clamp(var(--space-32),4vw,var(--space-64)); align-items: center; height: 100%; }
  .cc-card-grid.no-media { grid-template-columns: 1fr; }
  .cc-card-body { min-width: 0; }
  .cc-card h3 { font-family: var(--font-serif); font-weight: var(--fw-semibold); font-size: var(--fs-h3-lg); line-height: var(--lh-snug); letter-spacing: -.01em; color: #fff; margin: 0; }
  .cc-section.is-light .cc-card h3 { color: var(--ink); }
  .cc-block { margin-top: var(--space-32); }
  .cc-block-label { font-family: var(--font-mono); font-weight: var(--fw-semibold); font-size: var(--fs-xs); letter-spacing: var(--tracking-wide); text-transform: uppercase; color: var(--accent-on-dark); margin: 0 0 var(--space-8); }
  .cc-section.is-light .cc-block-label { color: var(--primary); }
  .cc-block-text { font-size: var(--fs-md); line-height: var(--lh-relaxed); margin: 0; }
  .cc-card-media img { width: 100%; height: auto; display: block; border-radius: var(--radius-2xl); box-shadow: var(--shadow-lift); }
  .cc-card-more { align-self: flex-start; margin-top: var(--space-32); display: inline-flex; align-items: center; gap: var(--space-8);
    padding: var(--space-8) var(--space-16); border-radius: var(--radius-pill); background: #fff; color: var(--ink);
    font-family: var(--font-mono); font-weight: var(--fw-semibold); font-size: var(--fs-xs); letter-spacing: var(--tracking-wide); text-transform: uppercase; text-decoration: none; transition: gap .25s ease, background .2s ease; }
  .cc-card-more:hover { gap: var(--space-16); background: var(--tint); }
  /* nav buttons — the brand .slider-btn standard (40px, 12px radius, 1px primary border, blue chevron, hover primary-light), CENTRED */
  .cc-controls { display: flex; align-items: center; justify-content: center; gap: var(--space-16); margin-top: var(--space-48); }
  .cc-btn { width: 2.5rem; height: 2.5rem; border-radius: var(--radius-btn); border: 1px solid var(--primary); background: transparent; color: var(--primary);
    display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background .25s ease, opacity .2s ease; }
  .cc-btn:hover:not(:disabled) { background: var(--primary-light); }
  .cc-btn:disabled { opacity: .35; cursor: default; }
  @media (max-width: 760px) {
    .cc-card-grid { grid-template-columns: 1fr; gap: var(--space-32); }
    .cc-card-media { order: -1; }
  }
  @media (prefers-reduced-motion: reduce) { .cc-track, .cc-card { transition: none !important; } }
</style>
<?php endif; ?>

<section class="cc-section<?php echo $cc['light'] ? ' is-light' : ''; ?>" data-cc>
  <?php if ( $cc['eyebrow'] || $cc['title'] || $cc['lead'] ) : ?>
  <div class="cc-head">
    <?php if ( $cc['eyebrow'] ) : ?><p class="cc-eyebrow"><?php echo esc_html( $cc['eyebrow'] ); ?></p><?php endif; ?>
    <?php if ( $cc['title'] ) : ?><h2 class="cc-h2"><?php echo wp_kses_post( $cc['title'] ); ?></h2><?php endif; ?>
    <?php if ( $cc['lead'] ) : ?><p class="cc-lead"><?php echo wp_kses_post( $cc['lead'] ); ?></p><?php endif; ?>
  </div>
  <?php endif; ?>

  <div class="cc-viewport">
    <div class="cc-track">
      <?php
      foreach ( $cc['cards'] as $cc_i => $card ) :
		$card    = wp_parse_args( (array) $card, array( 'title' => '', 'blocks' => array(), 'image' => '', 'image_alt' => '', 'link_url' => '', 'link_label' => 'Read more' ) );
		$blocks  = is_array( $card['blocks'] ) ? $card['blocks'] : array();
		$has_img = (bool) $card['image'];
		?>
      <article class="cc-card<?php echo 0 === $cc_i ? ' is-active' : ''; ?>">
        <div class="cc-card-grid<?php echo $has_img ? '' : ' no-media'; ?>">
          <div class="cc-card-body">
            <?php if ( $card['title'] ) : ?><h3><?php echo wp_kses_post( $card['title'] ); ?></h3><?php endif; ?>
            <?php foreach ( $blocks as $blk ) : $blk = wp_parse_args( (array) $blk, array( 'label' => '', 'text' => '' ) ); ?>
            <div class="cc-block">
              <?php if ( $blk['label'] ) : ?><p class="cc-block-label"><?php echo esc_html( $blk['label'] ); ?></p><?php endif; ?>
              <?php if ( $blk['text'] ) : ?><p class="cc-block-text"><?php echo wp_kses_post( $blk['text'] ); ?></p><?php endif; ?>
            </div>
            <?php endforeach; ?>
            <?php if ( $card['link_url'] ) : ?>
            <a class="cc-card-more" href="<?php echo esc_url( $card['link_url'] ); ?>"><?php echo esc_html( $card['link_label'] ); ?> <span aria-hidden="true">&rarr;</span></a>
            <?php endif; ?>
          </div>
          <?php if ( $has_img ) : ?>
          <div class="cc-card-media"><img src="<?php echo esc_url( $cc_src( $card['image'] ) ); ?>" alt="<?php echo esc_attr( $card['image_alt'] ); ?>" loading="lazy" draggable="false" /></div>
          <?php endif; ?>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="cc-controls">
    <button type="button" class="cc-btn" data-cc-prev aria-label="Previous">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg>
    </button>
    <button type="button" class="cc-btn" data-cc-next aria-label="Next">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18l6-6-6-6"/></svg>
    </button>
  </div>
</section>

<?php if ( $cc_assets ) : ?>
<script>
  (function () {
    function initCC(sec) {
      var viewport = sec.querySelector('.cc-viewport');
      var track = sec.querySelector('.cc-track');
      var cards = [].slice.call(sec.querySelectorAll('.cc-card'));
      var prev = sec.querySelector('[data-cc-prev]');
      var next = sec.querySelector('[data-cc-next]');
      if (!track || cards.length < 1) { return; }
      var index = 0;
      // translateX needed to CENTRE card i in the viewport (offsetLeft/Width ignore the scale transform)
      function centerShift(i) {
        var c = cards[i];
        return c.offsetLeft + c.offsetWidth / 2 - viewport.clientWidth / 2;
      }
      function render(animate) {
        track.style.transition = animate ? 'transform .5s cubic-bezier(.4,0,.2,1)' : 'none';
        track.style.transform = 'translateX(' + (-centerShift(index)) + 'px)';
        cards.forEach(function (c, n) { c.classList.toggle('is-active', n === index); });
        if (prev) { prev.disabled = index <= 0; }
        if (next) { next.disabled = index >= cards.length - 1; }
      }
      function go(i) { index = Math.max(0, Math.min(cards.length - 1, i)); render(true); }
      if (prev) { prev.addEventListener('click', function () { go(index - 1); }); }
      if (next) { next.addEventListener('click', function () { go(index + 1); }); }

      // drag / swipe
      var dragging = false, startX = 0, base = 0, moved = 0;
      function down(e) {
        dragging = true; moved = 0;
        startX = e.touches ? e.touches[0].clientX : e.clientX;
        base = -centerShift(index);
        track.style.transition = 'none';
        viewport.classList.add('is-dragging');
      }
      function move(e) {
        if (!dragging) { return; }
        var x = e.touches ? e.touches[0].clientX : e.clientX;
        moved = x - startX;
        track.style.transform = 'translateX(' + (base + moved) + 'px)';
        if (e.cancelable && Math.abs(moved) > 6) { e.preventDefault(); }
      }
      function up() {
        if (!dragging) { return; }
        dragging = false;
        viewport.classList.remove('is-dragging');
        var threshold = Math.min(160, viewport.clientWidth * 0.12);
        if (moved < -threshold) { go(index + 1); }
        else if (moved > threshold) { go(index - 1); }
        else { render(true); }
      }
      viewport.addEventListener('mousedown', down);
      window.addEventListener('mousemove', move, { passive: false });
      window.addEventListener('mouseup', up);
      viewport.addEventListener('touchstart', down, { passive: true });
      viewport.addEventListener('touchmove', move, { passive: false });
      viewport.addEventListener('touchend', up);
      window.addEventListener('resize', function () { render(false); });
      render(false);
    }
    function run() {
      var secs = document.querySelectorAll('[data-cc]');
      for (var i = 0; i < secs.length; i++) { initCC(secs[i]); }
    }
    if (document.readyState !== 'loading') { run(); } else { document.addEventListener('DOMContentLoaded', run); }
  })();
</script>
<?php endif; ?>
