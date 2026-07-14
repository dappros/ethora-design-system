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
 *     'invert'  => false,                        // true = INVERTED colours: blue section bg + white cards + light header
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
		'invert'  => false,
		'collapsible' => false,   // clamp each card's text to a fixed height; overflow gets a "Read more" that reveals the rest with an INNER scroll (card height stays fixed)
		'collapse_max' => '13rem', // clamped/scroll height of the text region when 'collapsible' is on
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
  .cc-head { max-width: var(--content-max); margin: 0 auto; padding: 0 var(--section-x); text-align: center; }
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
  /* INVERT variant — blue section, white cards; content STACKED (title + label + text, then a
     full-width image at the BOTTOM of the card). Opt-in via 'invert' => true.
     COMBINE with 'light' => true to keep the blue section but use the BIG default 2-column
     card (text left + image right) instead of the compact stacked one. */
  .cc-section.is-invert { background: var(--gradient-brand); }
  .cc-section.is-invert .cc-eyebrow { color: var(--accent-on-dark); }
  .cc-section.is-invert .cc-h2 { color: #fff; }
  .cc-section.is-invert .cc-lead { color: var(--text-on-dark); }
  .cc-section.is-invert .cc-card { background: #fff; background-image: none; border: 1px solid transparent; color: var(--text-body); box-shadow: var(--shadow-lift); }
  .cc-section.is-invert:not(.is-light) .cc-card { flex-basis: clamp(280px, 72vw, 500px);   /* compact card */
    padding: clamp(20px, 2.2vw, 26px) clamp(20px, 2.4vw, 28px) clamp(18px, 2vw, 24px); }
  .cc-section.is-invert .cc-card h3 { color: var(--ink); }
  .cc-section.is-invert .cc-block-label { color: var(--primary); }
  .cc-section.is-invert .cc-card-more { background: var(--primary); color: #fff; }
  .cc-section.is-invert .cc-card-more:hover { background: var(--primary-dark); }
  .cc-section.is-invert .cc-btn { border-color: #fff; color: #fff; }
  .cc-section.is-invert .cc-btn:hover:not(:disabled) { background: rgba(255,255,255,.15); }
  /* single column: text on top, then a FIXED-height image pinned to the bottom. The image area has a
     set aspect-ratio (object-fit: cover), so every card is exactly the same height regardless of the
     source image's own aspect ratio — switching cards never reflows/jumps, and lazy-loading reserves space.
     Skipped when 'light' is also on, so the big default 2-column layout is kept on the blue section. */
  .cc-section.is-invert:not(.is-light) .cc-card-grid { grid-template-columns: 1fr; grid-template-rows: 1fr auto; align-items: stretch; gap: var(--space-16); }
  .cc-section.is-invert:not(.is-light) .cc-card-media { order: 1; aspect-ratio: 16 / 10; border-radius: var(--radius-2xl); overflow: hidden; }
  .cc-section.is-invert:not(.is-light) .cc-card-media img { width: 100%; height: 100%; object-fit: cover; box-shadow: none; border-radius: 0; }
  /* COLLAPSIBLE text — opt-in via 'collapsible' => true. The blocks sit in a clamped region; if the
     content overflows, a "Read more" button reveals the rest WITHIN the same fixed height (inner scroll),
     so the card never grows. Clamp only applies on wider screens (mobile stacks, so height is cheap and
     an inner scroll would fight touch-swipe). */
  .cc-collapse { position: relative; }
  .cc-more { display: none; margin-top: var(--space-16); background: none; border: 0; padding: 0; cursor: pointer;
    font-family: var(--font-mono); font-weight: var(--fw-semibold); font-size: var(--fs-xs); letter-spacing: var(--tracking-wide);
    text-transform: uppercase; color: var(--primary); align-items: center; gap: var(--space-8); }
  .cc-more:hover { color: var(--primary-dark); }
  @media (min-width: 761px) {
    .cc-collapse.is-clampable { max-height: var(--cc-collapse-h, 13rem); overflow: hidden; }
    .cc-collapse.is-clampable.is-open { overflow-y: auto; }
    /* fade only while the text is actually clipped (JS toggles .is-clipped) — not when it fits or is open */
    .cc-collapse.is-clipped::after { content: ""; position: absolute; left: 0; right: 0; bottom: 0; height: var(--space-48);
      background: linear-gradient(to bottom, rgba(255,255,255,0), #fff); pointer-events: none; }
    .cc-more.is-shown { display: inline-flex; }
    .cc-more svg { transition: transform .25s ease; }
    .cc-more.is-open svg { transform: rotate(180deg); }
  }
  /* FLOATING BADGES — small "chip" cards overlaid on the card image (per-card 'badges' prop). They gently
     bob up and down (staggered), and can bleed slightly outside the image corners. Reduced-motion safe. */
  .cc-card-media { position: relative; }
  .cc-badge { position: absolute; z-index: 2; display: inline-flex; align-items: center; gap: var(--space-8);
    max-width: min(86%, 21rem); background: var(--white); border: 1px solid var(--border);
    border-radius: var(--radius-btn); padding: var(--space-8) var(--space-16) var(--space-8) var(--space-8);
    box-shadow: var(--shadow-card); animation: cc-badge-float 4.5s ease-in-out infinite; }
  .cc-badge--bl { left: clamp(-1rem, -1.5vw, -.375rem); bottom: 9%; }
  .cc-badge--tr { right: clamp(-.75rem, -1vw, -.25rem); top: 9%; animation-duration: 5.3s; animation-delay: -2.4s; }
  .cc-badge-ico { flex: none; width: 2.125rem; height: 2.125rem; border-radius: var(--radius-btn);
    background: var(--green-tint); border: 1px solid var(--green-border); color: var(--green-text);
    display: flex; align-items: center; justify-content: center; }
  .cc-badge-tx { min-width: 0; }
  .cc-badge-title { display: block; font-weight: var(--fw-bold); font-size: var(--fs-sm); line-height: 1.15; color: var(--ink); }
  .cc-badge-sub { display: block; font-size: var(--fs-xs); line-height: 1.25; color: var(--text-caption); }
  @keyframes cc-badge-float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-.5625rem); } }
  @media (max-width: 760px) {
    .cc-card-grid { grid-template-columns: 1fr; gap: var(--space-32); }
    .cc-card-media { order: -1; }
    .cc-badge { max-width: min(80%, 18rem); }
  }
  @media (prefers-reduced-motion: reduce) { .cc-track, .cc-card, .cc-badge { animation: none !important; transition: none !important; } }
</style>
<?php endif; ?>

<section class="cc-section<?php echo $cc['light'] ? ' is-light' : ''; ?><?php echo ! empty( $cc['invert'] ) ? ' is-invert' : ''; ?>" data-cc>
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
		$card    = wp_parse_args( (array) $card, array( 'title' => '', 'blocks' => array(), 'image' => '', 'image_alt' => '', 'link_url' => '', 'link_label' => 'Read more', 'badges' => array() ) );
		$blocks  = is_array( $card['blocks'] ) ? $card['blocks'] : array();
		$badges  = is_array( $card['badges'] ) ? $card['badges'] : array();
		$has_img = (bool) $card['image'];
		$cc_shield = '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>';
		?>
      <article class="cc-card<?php echo 0 === $cc_i ? ' is-active' : ''; ?>">
        <div class="cc-card-grid<?php echo $has_img ? '' : ' no-media'; ?>">
          <div class="cc-card-body">
            <?php if ( $card['title'] ) : ?><h3><?php echo wp_kses_post( $card['title'] ); ?></h3><?php endif; ?>
            <?php if ( $cc['collapsible'] ) : ?><div class="cc-collapse is-clampable" data-cc-collapse style="--cc-collapse-h:<?php echo esc_attr( $cc['collapse_max'] ); ?>;"><?php endif; ?>
            <?php foreach ( $blocks as $blk ) : $blk = wp_parse_args( (array) $blk, array( 'label' => '', 'text' => '' ) ); ?>
            <div class="cc-block">
              <?php if ( $blk['label'] ) : ?><p class="cc-block-label"><?php echo esc_html( $blk['label'] ); ?></p><?php endif; ?>
              <?php if ( $blk['text'] ) : ?><p class="cc-block-text"><?php echo wp_kses_post( $blk['text'] ); ?></p><?php endif; ?>
            </div>
            <?php endforeach; ?>
            <?php if ( $cc['collapsible'] ) : ?></div>
            <button type="button" class="cc-more" data-cc-more aria-expanded="false">
              <span data-cc-more-label>Read more</span>
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
            </button>
            <?php endif; ?>
            <?php if ( $card['link_url'] ) : ?>
            <a class="cc-card-more" href="<?php echo esc_url( $card['link_url'] ); ?>"><?php echo esc_html( $card['link_label'] ); ?> <span aria-hidden="true">&rarr;</span></a>
            <?php endif; ?>
          </div>
          <?php if ( $has_img ) : ?>
          <div class="cc-card-media">
            <img src="<?php echo esc_url( $cc_src( $card['image'] ) ); ?>" alt="<?php echo esc_attr( $card['image_alt'] ); ?>" loading="lazy" draggable="false" />
            <?php foreach ( $badges as $b_i => $bg ) : $bg = wp_parse_args( (array) $bg, array( 'title' => '', 'text' => '', 'icon' => '', 'pos' => '' ) );
              $pos = $bg['pos'] ? $bg['pos'] : ( 0 === $b_i ? 'bl' : 'tr' ); ?>
            <div class="cc-badge cc-badge--<?php echo esc_attr( $pos ); ?>">
              <span class="cc-badge-ico"><?php echo $bg['icon'] ? $bg['icon'] : $cc_shield; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
              <span class="cc-badge-tx">
                <?php if ( $bg['title'] ) : ?><span class="cc-badge-title"><?php echo esc_html( $bg['title'] ); ?></span><?php endif; ?>
                <?php if ( $bg['text'] ) : ?><span class="cc-badge-sub"><?php echo wp_kses_post( $bg['text'] ); ?></span><?php endif; ?>
              </span>
            </div>
            <?php endforeach; ?>
          </div>
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
    // COLLAPSIBLE cards: show "Read more" only when the clamped text actually overflows; toggling opens
    // an inner scroll within the same fixed height (the card never grows). Only active >= 761px.
    function initCollapse(sec) {
      var wraps = [].slice.call(sec.querySelectorAll('[data-cc-collapse]'));
      wraps.forEach(function (wrap) {
        var btn = wrap.parentNode.querySelector('[data-cc-more]');
        if (!btn) { return; }
        var label = btn.querySelector('[data-cc-more-label]');
        function overflowing() { return wrap.scrollHeight - wrap.clientHeight > 2; }
        function sync() {
          var wide = window.matchMedia('(min-width: 761px)').matches;
          if (!wide && wrap.classList.contains('is-open')) {
            wrap.classList.remove('is-open'); btn.classList.remove('is-open');
            btn.setAttribute('aria-expanded', 'false');
            if (label) { label.textContent = 'Read more'; }
          }
          var open = wrap.classList.contains('is-open');
          var of = overflowing();
          btn.classList.toggle('is-shown', wide && (open || of));
          wrap.classList.toggle('is-clipped', wide && of && !open);
        }
        btn.addEventListener('click', function () {
          var open = wrap.classList.toggle('is-open');
          btn.classList.toggle('is-open', open);
          btn.setAttribute('aria-expanded', open ? 'true' : 'false');
          if (label) { label.textContent = open ? 'Show less' : 'Read more'; }
          if (!open) { wrap.scrollTop = 0; }
          sync();
        });
        sync();
        window.addEventListener('resize', sync);
        if (document.fonts && document.fonts.ready) { document.fonts.ready.then(sync); }
      });
    }
    function run() {
      var secs = document.querySelectorAll('[data-cc]');
      for (var i = 0; i < secs.length; i++) { initCC(secs[i]); initCollapse(secs[i]); }
    }
    if (document.readyState !== 'loading') { run(); } else { document.addEventListener('DOMContentLoaded', run); }
  })();
</script>
<?php endif; ?>
