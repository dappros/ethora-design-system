<?php
/**
 * Reusable scroll-telling section (the ".shs-why" pattern).
 *
 * A pinned pane on the left (eyebrow + title + lead + a changing image) and, on
 * the right, a text TRACK that slides up as you scroll — the image swaps and the
 * matching step highlights at each threshold. On mobile it collapses to an
 * accordion: tap a step to reveal its image below it. Self-contained (ships its
 * own CSS + JS once per request) and supports multiple instances per page.
 *
 * Pass props via the 3rd arg of get_template_part():
 *
 *   get_template_part( 'template-parts/section-why', null, array(
 *     'eyebrow'  => 'Why self-host',           // optional mono kicker
 *     'title'    => 'Section heading',          // h2
 *     'lead'     => 'Intro paragraph.',          // (optional)
 *     'numbered' => true,                        // show 01/02/03 markers (default true)
 *     'rhombus'  => true,                        // decorative background shape (default true)
 *     'frame'    => true,                        // false → images are self-framed (own bg/shadow/rounded): show whole (contain), no card frame/shadow
 *     'steps'    => array(                        // REQUIRED — 2+ steps, each with its own image
 *       array(
 *         'title'     => 'Complete data ownership',
 *         'text'      => 'Full control over infrastructure…',
 *         'image'     => 'images/foo.png',        // theme-relative path or URL (used desktop + mobile)
 *         'image_alt' => 'Describe the image',
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

$wy = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'eyebrow'  => '',
		'title'    => '',
		'lead'     => '',
		'numbered' => true,
		'rhombus'  => true,
		'frame'    => true, // false → images are self-framed (own bg/shadow): show whole (contain), no card frame/shadow
		'steps'    => array(),
	)
);

// Need at least two steps for the scroll interaction to make sense.
if ( empty( $wy['steps'] ) || ! is_array( $wy['steps'] ) || count( $wy['steps'] ) < 2 ) {
	return;
}

$wy_uri  = get_template_directory_uri();
$wy_n    = count( $wy['steps'] );
$wy_th   = ( $wy_n * 60 ) . 'vh';   // scroll length: ~60vh per step (3 steps = 180vh, like the original)

// Resolve a theme-relative path or pass an absolute URL through.
$wy_src = function ( $img ) use ( $wy_uri ) {
	if ( ! $img ) {
		return '';
	}
	return preg_match( '#^(https?:)?//#', $img ) ? $img : $wy_uri . '/' . ltrim( $img, '/' );
};

$wy_assets = empty( $GLOBALS['shs_why_assets'] );
if ( $wy_assets ) {
	$GLOBALS['shs_why_assets'] = true;
}
?>
<?php if ( $wy_assets ) : ?>
<style>
  /* WHY / scroll-telling — pinned title + changing image (left), sliding text track (right);
     mobile = accordion. Tokens only. Self-contained. */
  .shs-why { padding: clamp(72px,9vw,132px) var(--section-x) var(--section-y); }
  .shs-why, .shs-why *, .shs-why *::before, .shs-why *::after { box-sizing: border-box; }
  .shs-why .why-inner { max-width: var(--content-max); margin: 0 auto; }
  .shs-why .why-track { position: relative; }
  .shs-why .why-eyebrow { font-family: var(--font-mono); font-weight: var(--fw-medium); font-size: var(--fs-eyebrow); letter-spacing: var(--tracking-wide); text-transform: uppercase; color: var(--primary); margin: 0; }
  /* 3fr/2fr = 60/40 ratio that ACCOUNTS for the column-gap (unlike 60%/40% which overflow by the gap) */
  .shs-why .why-pin { position: sticky; top: calc(var(--header-h) + var(--space-32)); min-height: calc(100vh - var(--header-h) - var(--space-64)); display: grid; grid-template-columns: minmax(0,3fr) minmax(0,2fr); grid-template-rows: auto 1fr; column-gap: clamp(32px,5vw,72px); align-content: center; }
  /* decorative rhombus — left vertex touches the viewport left edge (not beyond) */
  .shs-why.has-rhombus .why-pin::before { content: ""; position: absolute; z-index: 0; pointer-events: none;
    top: 0; bottom: 0; left: 50%; transform: translateX(-50%); width: 100vw;
    background: url('<?php echo esc_url( $wy_uri . '/images/rhombus-features-demo.svg' ); ?>') no-repeat 0 34% / clamp(470px,51vw,800px) auto; }
  .shs-why .why-head, .shs-why .why-visual, .shs-why .why-viewport { position: relative; z-index: 1; }
  .shs-why .why-head { grid-column: 1; grid-row: 1; }
  .shs-why .why-head h2 { font-family: var(--font-serif); font-weight: 500; font-size: var(--fs-h2); line-height: var(--lh-tight); letter-spacing: var(--tracking-tight); color: var(--ink); margin: var(--space-16) 0 0; }
  .shs-why .why-lead { font-size: var(--fs-lg); line-height: var(--lh-relaxed); color: var(--text-body); margin: var(--space-16) 0 0; max-width: 480px; }
  .shs-why .why-visual { grid-column: 1; grid-row: 2; align-self: center; display: grid; position: relative; height: clamp(300px, 40vh, 440px); margin-top: var(--space-32); }
  .shs-why .wv { grid-area: 1 / 1; border-radius: var(--radius-3xl); overflow: hidden; box-shadow: var(--shadow-lift); opacity: 0; transform: scale(.985); transition: opacity .5s ease, transform .5s ease; pointer-events: none; }
  .shs-why .wv.is-active { opacity: 1; transform: none; }
  .shs-why .wv img { width: 100%; height: 100%; object-fit: cover; object-position: center top; display: block; }
  /* self-framed images (own bg/shadow/rounded card): drop the card frame + show the whole image */
  .shs-why.no-frame .wv { border-radius: 0; overflow: visible; box-shadow: none; }
  .shs-why.no-frame .wv img { object-fit: contain; object-position: center; }
  /* right: fixed window with a sliding text track (opposite the image) */
  .shs-why .why-viewport { grid-column: 2; grid-row: 2; align-self: center; overflow: hidden; height: clamp(240px, 32vh, 320px); }
  .shs-why .why-texttrack { display: block; will-change: transform; }
  .shs-why .why-step { height: clamp(240px, 32vh, 320px); display: flex; flex-direction: column; justify-content: center; opacity: .28; transition: opacity .4s ease; }
  .shs-why .why-step.is-active { opacity: 1; }
  .shs-why .why-step .ws-num { font-family: var(--font-mono); font-size: var(--fs-sm); font-weight: 700; color: var(--primary); letter-spacing: .12em; }
  .shs-why .why-step h3 { font-family: var(--font-serif); font-weight: 600; font-size: clamp(24px,3vw,34px); color: var(--ink); line-height: var(--lh-snug); margin: var(--space-16) 0 0; }
  .shs-why .why-step p { font-size: var(--fs-lg); line-height: var(--lh-relaxed); color: var(--text-body); margin: var(--space-16) 0 0; max-width: 460px; }
  .shs-why .why-step-media { display: none; }   /* desktop uses the sticky image; this is the mobile per-step image */
  .shs-why .why-step-media img { width: 100%; height: auto; display: block; border-radius: var(--radius-2xl); box-shadow: var(--shadow-card); }
  /* MOBILE: accordion — tap a text to reveal its image below it (one open at a time) */
  @media (max-width: 900px) {
    .shs-why .why-track { height: auto !important; }
    .shs-why .why-pin { position: static; min-height: 0; grid-template-columns: 1fr; grid-template-rows: none; gap: 0; }
    .shs-why .why-head, .shs-why .why-visual, .shs-why .why-viewport { grid-column: auto; grid-row: auto; }
    .shs-why .why-head { margin-bottom: var(--space-16); }
    .shs-why .why-visual { display: none; }
    .shs-why .why-viewport { height: auto; overflow: visible; }
    .shs-why .why-texttrack { transform: none !important; }
    .shs-why .why-step { height: auto; opacity: 1; padding: var(--space-32) 0; border-top: 1px solid var(--hairline); cursor: pointer; }
    .shs-why .why-step.is-active { border-top-color: var(--primary); }
    .shs-why .why-step-media { display: block; max-height: 0; opacity: 0; overflow: hidden; margin-top: 0; transition: max-height .45s ease, opacity .35s ease, margin-top .35s ease; }
    .shs-why .why-step.is-active .why-step-media { max-height: 80vh; opacity: 1; margin-top: var(--space-16); }
  }
  @media (prefers-reduced-motion: reduce) { .shs-why .wv { transition: opacity .2s ease; } }
</style>
<?php endif; ?>

<section class="shs-why<?php echo $wy['rhombus'] ? ' has-rhombus' : ''; ?><?php echo $wy['frame'] ? '' : ' no-frame'; ?>">
  <div class="why-inner">
    <div class="why-track" style="height:<?php echo esc_attr( $wy_th ); ?>;">
      <div class="why-pin">
        <div class="why-head">
          <?php if ( $wy['eyebrow'] ) : ?><span class="why-eyebrow"><?php echo esc_html( $wy['eyebrow'] ); ?></span><?php endif; ?>
          <?php if ( $wy['title'] ) : ?><h2><?php echo wp_kses_post( $wy['title'] ); ?></h2><?php endif; ?>
          <?php if ( $wy['lead'] ) : ?><p class="why-lead"><?php echo wp_kses_post( $wy['lead'] ); ?></p><?php endif; ?>
        </div>
        <!-- changing image (desktop) -->
        <div class="why-visual">
          <?php foreach ( $wy['steps'] as $i => $st ) : $st = wp_parse_args( (array) $st, array( 'title' => '', 'text' => '', 'image' => '', 'image_alt' => '' ) ); ?>
          <div class="wv<?php echo 0 === $i ? ' is-active' : ''; ?>" data-i="<?php echo (int) $i; ?>">
            <?php if ( $st['image'] ) : ?><img src="<?php echo esc_url( $wy_src( $st['image'] ) ); ?>" alt="<?php echo esc_attr( $st['image_alt'] ); ?>" loading="lazy" /><?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>
        <!-- sliding text (desktop) / accordion (mobile) -->
        <div class="why-viewport">
          <div class="why-texttrack">
            <?php foreach ( $wy['steps'] as $i => $st ) : $st = wp_parse_args( (array) $st, array( 'title' => '', 'text' => '', 'image' => '', 'image_alt' => '' ) ); ?>
            <article class="why-step<?php echo 0 === $i ? ' is-active' : ''; ?>" data-i="<?php echo (int) $i; ?>">
              <?php if ( $wy['numbered'] ) : ?><span class="ws-num"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span><?php endif; ?>
              <?php if ( $st['title'] ) : ?><h3><?php echo wp_kses_post( $st['title'] ); ?></h3><?php endif; ?>
              <?php if ( $st['text'] ) : ?><p><?php echo wp_kses_post( $st['text'] ); ?></p><?php endif; ?>
              <?php if ( $st['image'] ) : ?><div class="why-step-media"><img src="<?php echo esc_url( $wy_src( $st['image'] ) ); ?>" alt="<?php echo esc_attr( $st['image_alt'] ); ?>" loading="lazy" /></div><?php endif; ?>
            </article>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php if ( $wy_assets ) : ?>
<script>
  (function () {
    function initWhy(sec) {
      var track = sec.querySelector('.why-track');
      var pin = sec.querySelector('.why-pin');
      var viewport = sec.querySelector('.why-viewport');
      var texttrack = sec.querySelector('.why-texttrack');
      var steps = [].slice.call(sec.querySelectorAll('.why-step'));
      var cards = [].slice.call(sec.querySelectorAll('.wv'));
      if (!track || !pin || !viewport || !texttrack || steps.length < 2) { return; }
      var N = steps.length, lastActive = -1, ticking = false;
      function frame() {
        ticking = false;
        if (window.innerWidth <= 900) { texttrack.style.transform = ''; return; }
        var stickyTop = parseFloat(getComputedStyle(pin).top) || 0;
        var range = track.offsetHeight - pin.offsetHeight;   // scroll distance while pinned
        var top = track.getBoundingClientRect().top;
        var progress = range > 0 ? Math.min(1, Math.max(0, (stickyTop - top) / range)) : 0;
        var stepH = viewport.offsetHeight;
        texttrack.style.transform = 'translateY(' + (-progress * (N - 1) * stepH) + 'px)';
        var active = Math.round(progress * (N - 1));
        if (active !== lastActive) {
          lastActive = active;
          steps.forEach(function (s, i) { s.classList.toggle('is-active', i === active); });
          cards.forEach(function (c, i) { c.classList.toggle('is-active', i === active); });
        }
      }
      function onScroll() { if (!ticking) { ticking = true; requestAnimationFrame(frame); } }
      window.addEventListener('scroll', onScroll, { passive: true });
      window.addEventListener('resize', onScroll, { passive: true });
      onScroll();
      // MOBILE accordion: tap a step to open its image (one open at a time)
      function setActive(i) {
        steps.forEach(function (s, n) { s.classList.toggle('is-active', n === i); });
        cards.forEach(function (c, n) { c.classList.toggle('is-active', n === i); });
        lastActive = i;
      }
      steps.forEach(function (s, i) {
        s.addEventListener('click', function () { if (window.innerWidth <= 900) { setActive(i); } });
      });
    }
    function run() {
      var secs = document.querySelectorAll('.shs-why');
      for (var i = 0; i < secs.length; i++) { initWhy(secs[i]); }
    }
    if (document.readyState !== 'loading') { run(); } else { document.addEventListener('DOMContentLoaded', run); }
  })();
</script>
<?php endif; ?>
