<?php
/**
 * Reusable "Our Case Studies" section — the home-page design as a self-contained block.
 *
 * A sliding carousel (track + peek + scale + drag/swipe): each card has a screenshot,
 * a customer logo, a short description, a green-check "success highlights" list and a
 * "Read more" link. Centred prev/next (brand .slider-btn) + dots.
 *
 * Self-contained: ships its own CSS + JS once per request, scoped to `.csx-*` classes
 * so it works on ANY template (page templates load css/index.css, the home page loads
 * style-index.css) and never collides with the legacy `.case-study-*` sliders in
 * js/index.js / main.js. All values come from css/tokens.css (loaded on every page).
 *
 *   get_template_part( 'template-parts/section-case-studies' );   // no args — self-contained
 *
 * @package ethora-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$csx_u     = get_template_directory_uri();
$csx_title = ( isset( $args['title'] ) && $args['title'] ) ? $args['title'] : 'Our Case Studies';

/* The two flagship case studies (same content as the home page). */
$csx_cards = array(
	array(
		'image'     => $csx_u . '/images/DrTalks.png',
		'image_alt' => 'DrTalks',
		'logo'      => $csx_u . '/images/DrTalks-logo.svg',
		'logo_alt'  => 'DrTalks Logo',
		'paras'     => array(
			'Dr. Talks is one of the most prominent organizers of online healthcare conferences and summits.',
			'Ethora enabled them to create a context-aware AI-powered chatbot to help users navigate thousands of pages of medical content, correctly referencing pages, authors, videos and timestamps. 100% secure and HIPAA-compliant.',
			'This assistant enhances user experience by simplifying interaction with the platform and reducing search time.',
		),
		'points'    => array(
			'1000+ summits indexed by AI bot',
			'Instant user engagement',
			'Vector embeddings enable intelligent domain expert AI responses',
		),
		'url'       => home_url( '/case-study-drtalks/' ),
	),
	array(
		'image'     => $csx_u . '/images/Atom.png',
		'image_alt' => 'Atom',
		'logo'      => $csx_u . '/images/Atom-logo.svg',
		'logo_alt'  => 'Atom Logo',
		'paras'     => array(
			'Atom Advantage is a provider of innovative AI powered workers compensation and health insurance solutions in the United States.',
			'Ethora engine has enabled Atom Connect product used for communication between injured workers, nurses, and caseworkers. AI-powered, with documents exchange, and compliant with all regulations such as HIPAA and SOC2.',
			'This solution reduces manual labour for caseworkers and improves patients experience.',
		),
		'points'    => array(
			'Integration into caseworkers web portal',
			'White-labelled mobile app for chat and health documents wallet for injured workers',
			'Instant messaging between caseworkers and injured workers',
		),
		'url'       => home_url( '/case-study-atom-advantage/' ),
	),
);

$csx_check = '<svg class="csx-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>';

$csx_assets = empty( $GLOBALS['csx_assets'] );
if ( $csx_assets ) {
	$GLOBALS['csx_assets'] = true;
}
?>
<?php if ( $csx_assets ) : ?>
<style>
  /* Our Case Studies — self-contained sliding carousel (track + peek + scale + drag).
     Scoped to .csx- so it ships its own CSS/JS and works on any template. Tokens only. */
  /* overflow-x: clip (not hidden) — clips the peeking neighbour card horizontally WITHOUT
     making overflow-y compute to `auto`, which is what spawned a spurious vertical scrollbar. */
  .csx-section { padding: var(--space-64) 0; overflow-x: clip; }
  .csx-section * { box-sizing: border-box; }
  /* header + nav buttons — bundled into the block so they never depend on the legacy
     .case-studies global rules (which is where the blue border + top-right position lived). */
  .csx-head { position: relative; display: flex; flex-direction: column; align-items: center;
    justify-content: center; gap: var(--space-16); margin-bottom: var(--space-32); }
  .csx-head h2 { font-size: var(--fs-h2); line-height: var(--lh-heading); text-align: center; color: var(--ink); margin: 0; }
  .csx-controls { display: flex; justify-content: center; gap: var(--space-16); }
  .csx-controls .slider-btn { border: 1px solid var(--primary); color: var(--primary); }
  .csx-controls .slider-btn svg { color: var(--primary); }
  .csx-controls .slider-btn:disabled { opacity: .4; cursor: default; }
  .csx-viewport { position: relative; margin-top: var(--space-32); overflow: visible; cursor: grab; }
  .csx-viewport.is-dragging { cursor: grabbing; }
  .csx-viewport.is-dragging .csx-track { user-select: none; }
  .csx-viewport img { -webkit-user-drag: none; -webkit-user-select: none; user-select: none; }
  .csx-track { display: flex; gap: var(--space-32); align-items: stretch; will-change: transform; }
  .csx-card { flex: 0 0 clamp(300px, 86vw, var(--content-max)); display: grid; grid-template-columns: 1fr;
    gap: var(--space-32); align-items: flex-start; border-radius: var(--border-radius-lg); padding: var(--space-16);
    background: var(--gradient-soft-brand); min-height: 476px; transform: scale(.94); transform-origin: center center;
    transition: transform .5s cubic-bezier(.4, 0, .2, 1); }
  .csx-card.active { transform: scale(1); }
  /* desktop: image left, content right (2-up) — matches the home page */
  @media (min-width: 768px) {
    .csx-card { grid-template-columns: 1fr 1fr; padding: var(--space-32); }
    .csx-controls { position: absolute; top: 0; right: 0; }
    .csx-head h2 { width: 70%; }
  }
  .csx-image { display: flex; justify-content: center; min-height: 190px; }
  .csx-image img { max-width: 100%; height: auto; object-fit: contain; }
  .csx-content { display: flex; flex-direction: column; justify-content: space-between; align-items: flex-start; }
  .csx-header { display: flex; align-items: center; margin-bottom: var(--space-16); }
  .csx-content p { color: var(--text-light); padding-bottom: var(--space-8); }
  .csx-desc { font-size: var(--fs-base); display: flex; flex-direction: column; gap: var(--space-8); }
  .csx-success { list-style: none; margin: 0; padding: var(--space-16) 0 0; display: flex; flex-direction: column;
    align-items: flex-start; gap: var(--space-16); width: 100%; }
  .csx-point { display: flex; align-items: flex-start; gap: var(--space-8); color: var(--text-body);
    font-size: var(--fs-base); line-height: var(--lh-relaxed); }
  .csx-check { flex: none; width: 20px; height: 20px; margin-top: 2px; color: var(--green-text); }
  .csx-readmore { margin-top: var(--space-16); align-self: flex-start; display: inline-flex; align-items: center;
    color: var(--primary); font-weight: var(--fw-bold); font-size: var(--fs-sm); letter-spacing: var(--tracking-wide);
    text-transform: uppercase; text-decoration: none; transition: color .2s ease; }
  .csx-readmore:hover { color: var(--primary-dark); }
  .csx-readmore:focus-visible { outline: 2px solid var(--primary); outline-offset: 3px; border-radius: var(--radius-xs); }
  .csx-dots { display: flex; justify-content: center; gap: var(--space-8); padding-top: var(--space-16); }
  .csx-dot { width: .5rem; height: .5rem; padding: 0; border: 0; cursor: pointer; border-radius: var(--radius-pill);
    background-color: var(--border-strong); transition: transform .3s ease, background-color .3s ease; }
  .csx-dot.active { transform: scale(1.3); background-color: var(--primary); }
  @media (prefers-reduced-motion: reduce) { .csx-track, .csx-card { transition: none !important; } }
</style>
<?php endif; ?>

<section class="csx-section">
  <div class="container">
    <div class="csx-head">
      <h2><?php echo esc_html( $csx_title ); ?></h2>
      <div class="csx-controls">
        <button type="button" aria-label="Previous case study" class="slider-btn csx-prev">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
        </button>
        <button type="button" aria-label="Next case study" class="slider-btn csx-next">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
        </button>
      </div>
    </div>
  </div>

  <div class="csx-viewport">
    <div class="csx-track">
      <?php foreach ( $csx_cards as $i => $card ) : ?>
      <div class="csx-card<?php echo 0 === $i ? ' active' : ''; ?>">
        <div class="csx-image">
          <img src="<?php echo esc_url( $card['image'] ); ?>" alt="<?php echo esc_attr( $card['image_alt'] ); ?>" />
        </div>
        <div class="csx-content">
          <div class="csx-header">
            <img src="<?php echo esc_url( $card['logo'] ); ?>" alt="<?php echo esc_attr( $card['logo_alt'] ); ?>" height="40" />
          </div>
          <div class="csx-desc">
            <?php foreach ( $card['paras'] as $p ) : ?>
            <p><?php echo wp_kses_post( $p ); ?></p>
            <?php endforeach; ?>
          </div>
          <ul class="csx-success">
            <?php foreach ( $card['points'] as $pt ) : ?>
            <li class="csx-point"><?php echo $csx_check; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted inline SVG ?><span><?php echo esc_html( $pt ); ?></span></li>
            <?php endforeach; ?>
          </ul>
          <a class="csx-readmore" href="<?php echo esc_url( $card['url'] ); ?>">Read more</a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="container">
    <div class="csx-dots">
      <?php foreach ( $csx_cards as $i => $card ) : ?>
      <button type="button" class="csx-dot<?php echo 0 === $i ? ' active' : ''; ?>" aria-label="Go to case study <?php echo (int) ( $i + 1 ); ?>"></button>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php if ( $csx_assets ) : ?>
<script>
  (function () {
    function initCsx(section) {
      var viewport = section.querySelector('.csx-viewport');
      var track    = section.querySelector('.csx-track');
      var cards    = [].slice.call(section.querySelectorAll('.csx-card'));
      var dots     = [].slice.call(section.querySelectorAll('.csx-dot'));
      var prev     = section.querySelector('.csx-prev');
      var next     = section.querySelector('.csx-next');
      if (!viewport || !track || cards.length < 1) { return; }
      viewport.addEventListener('dragstart', function (e) { e.preventDefault(); });
      var index = 0;
      function centerShift(i) { var c = cards[i]; return c.offsetLeft + c.offsetWidth / 2 - viewport.clientWidth / 2; }
      function render(animate) {
        track.style.transition = animate ? 'transform .5s cubic-bezier(.4,0,.2,1)' : 'none';
        track.style.transform = 'translateX(' + (-centerShift(index)) + 'px)';
        cards.forEach(function (c, n) { c.classList.toggle('active', n === index); });
        dots.forEach(function (d, n) { d.classList.toggle('active', n === index); });
        if (prev) { prev.disabled = index <= 0; }
        if (next) { next.disabled = index >= cards.length - 1; }
      }
      function go(i) { index = Math.max(0, Math.min(cards.length - 1, i)); render(true); }
      if (prev) { prev.addEventListener('click', function () { go(index - 1); }); }
      if (next) { next.addEventListener('click', function () { go(index + 1); }); }
      dots.forEach(function (d, n) { d.addEventListener('click', function () { go(n); }); });
      var dragging = false, startX = 0, base = 0, moved = 0;
      function down(e) { dragging = true; moved = 0; startX = e.touches ? e.touches[0].clientX : e.clientX; base = -centerShift(index); track.style.transition = 'none'; viewport.classList.add('is-dragging'); }
      function move(e) { if (!dragging) { return; } var x = e.touches ? e.touches[0].clientX : e.clientX; moved = x - startX; track.style.transform = 'translateX(' + (base + moved) + 'px)'; if (e.cancelable && Math.abs(moved) > 6) { e.preventDefault(); } }
      function up() { if (!dragging) { return; } dragging = false; viewport.classList.remove('is-dragging'); var threshold = Math.min(160, viewport.clientWidth * 0.12); if (moved < -threshold) { go(index + 1); } else if (moved > threshold) { go(index - 1); } else { render(true); } }
      viewport.addEventListener('mousedown', down);
      window.addEventListener('mousemove', move, { passive: false });
      window.addEventListener('mouseup', up);
      viewport.addEventListener('touchstart', down, { passive: true });
      viewport.addEventListener('touchmove', move, { passive: false });
      viewport.addEventListener('touchend', up);
      window.addEventListener('resize', function () { render(false); });
      render(false);
      window.addEventListener('load', function () { render(false); });
    }
    function boot() { document.querySelectorAll('.csx-section').forEach(initCsx); }
    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', boot); } else { boot(); }
  })();
</script>
<?php endif; ?>
