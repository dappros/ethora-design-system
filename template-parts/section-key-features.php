<?php
/**
 * Reusable "Key Features" section.
 *
 * Interactive accordion (one block open at a time, auto-cycling with a progress
 * loader that drives the switch; hovering the card — or focusing a header — pauses the
 * loader and resumes it on leave) + a product image on the side. Self-contained:
 * ships its own CSS/JS once per request, works with ANY number of blocks, and
 * supports multiple instances on one page.
 *
 * Pass props via the 3rd arg of get_template_part():
 *
 *   get_template_part( 'template-parts/section-key-features', null, array(
 *     'eyebrow'      => 'Features',                 // small kicker (optional, '' hides it)
 *     'title'        => 'Key Features of …',        // h2 (inline HTML allowed)
 *     'lead'         => 'Optional intro paragraph.', // (optional)
 *     'image'        => 'images/foo.png',           // theme-relative path OR absolute URL (optional)
 *     'image_alt'    => 'Describe the image',
 *     'image_width'  => 881,                          // for CLS (optional)
 *     'image_height' => 779,
 *     'interval'     => 4.2,                          // seconds per block (optional, default 4.2)
 *     'shade'        => false,                        // tint the section bg + hairline borders (optional)
 *     'reverse'      => false,                        // image on the LEFT, accordion on the right (optional)
 *     'footnote'     => 'Closing note …',             // small bordered paragraph under the grid (optional)
 *     'features'     => array(                        // REQUIRED — any length
 *       array(
 *         'title' => 'Real-time messaging',
 *         'text'  => 'Instant delivery …',            // inline HTML allowed
 *         'icon'  => '<svg …>…</svg>',                // raw SVG markup (optional; omit for a text-only row)
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

$kf = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'eyebrow'      => '',
		'title'        => '',
		'lead'         => '',
		'image'        => '',
		'image_alt'    => '',
		'image_width'  => '',
		'image_height' => '',
		'interval'     => 4.2,
		'shade'        => false,
		'reverse'      => false,
		'footnote'     => '',
		'features'     => array(),
	)
);

// Nothing to show without blocks.
if ( empty( $kf['features'] ) || ! is_array( $kf['features'] ) ) {
	return;
}

// Allow a theme-relative path ("images/foo.png") or an absolute URL.
$kf_img = $kf['image'];
if ( $kf_img && ! preg_match( '#^(https?:)?//#', $kf_img ) ) {
	$kf_img = get_template_directory_uri() . '/' . ltrim( $kf_img, '/' );
}

// Unique per-instance id prefix so aria attributes never collide when reused.
$GLOBALS['shs_kf_seq'] = isset( $GLOBALS['shs_kf_seq'] ) ? $GLOBALS['shs_kf_seq'] + 1 : 1;
$kf_uid                = 'shs-kf-' . $GLOBALS['shs_kf_seq'];

// Emit the shared CSS + JS only once per request, no matter how many instances.
$kf_assets = empty( $GLOBALS['shs_kf_assets'] );
if ( $kf_assets ) {
	$GLOBALS['shs_kf_assets'] = true;
}
?>
<?php if ( $kf_assets ) : ?>
<style>
  /* KEY FEATURES — interactive accordion (one open at a time, auto-cycles) + product image.
     Self-contained: every value comes from the global design tokens (css/tokens.css). */
  .shs-kf-section { padding: var(--section-y) var(--section-x); }
  .shs-kf-section, .shs-kf-section *, .shs-kf-section *::before, .shs-kf-section *::after { box-sizing: border-box; }
  .shs-kf-section.is-shaded { background: var(--surface-alt); border-top: 1px solid var(--hairline); border-bottom: 1px solid var(--hairline); }
  .shs-kf-wrap { max-width: var(--content-max); margin: 0 auto; }
  .shs-kf-eyebrow { font-family: var(--font-mono); font-weight: var(--fw-medium); font-size: var(--fs-eyebrow); letter-spacing: var(--tracking-wide); text-transform: uppercase; color: var(--primary); margin: 0; }
  .shs-kf-h2 { font-family: var(--font-serif); font-weight: var(--fw-medium); font-size: var(--fs-h2); line-height: 1.08; letter-spacing: -.01em; color: var(--ink); margin: var(--space-16) 0 0; }
  .shs-kf-lead { font-size: var(--fs-lg); line-height: var(--lh-relaxed); color: var(--text-body); margin: var(--space-16) 0 0; }
  .shs-kf-grid { display: grid; grid-template-columns: minmax(0,1.05fr) minmax(0,.95fr); gap: clamp(var(--space-32),5vw,var(--space-64)); align-items: center; margin-top: var(--space-48);     background: linear-gradient(-124deg, rgba(255, 255, 255, 1) 0%, rgba(0, 82, 205, .156) 71%, rgba(255, 255, 255, 1) 100%); border: 1px solid var(--border); border-radius: var(--radius-3xl); padding: var(--space-32)}
  /* reverse: image on the left, accordion on the right */
  .shs-kf-section.is-reversed .shs-kf-grid { grid-template-columns: minmax(0,.95fr) minmax(0,1.05fr); }
  .shs-kf-section.is-reversed .shs-kf-media { order: -1; }
  .shs-kf-list { display: flex; flex-direction: column; gap: var(--space-8) }
  .shs-kf-item { position: relative; background: var(--surface-alt); border: 1px solid transparent; border-radius: var(--radius-xl); overflow: hidden;
    transition: background .3s ease, border-color .3s ease, box-shadow .3s ease; }
  .shs-kf-item.is-open { background: #fff; border-color: var(--border); box-shadow: var(--shadow-card); }
  .shs-kf-head { width: 100%; display: flex; align-items: center; gap: var(--space-16); padding: var(--space-16);
    background: none; border: none; cursor: pointer; text-align: left; font-family: var(--font-body); }
  .shs-kf-head:focus-visible { outline: 2px solid var(--primary); outline-offset: -2px; border-radius: var(--radius-xl); }
  .shs-kf-ico { flex: none; width: 40px; height: 40px; border-radius: var(--radius-md); background: var(--tint);
    display: flex; align-items: center; justify-content: center; }
  .shs-kf-ico svg { width: 20px; height: 20px; }
  .shs-kf-title { flex: 1 1 auto; min-width: 0; font-weight: var(--fw-semibold); font-size: var(--fs-xl); color: var(--ink); line-height: 1.3; }
  /* height is measured by JS (tallest description) so the open panel is always the same size → list height never jumps */
  .shs-kf-panel { height: 0; overflow: hidden; opacity: 0; transition: height .4s ease, opacity .3s ease; }
  .shs-kf-item.is-open .shs-kf-panel { height: var(--kf-panel-h, 96px); opacity: 1; }
  .shs-kf-panel-inner { padding: 0 var(--space-16) var(--space-16) calc(40px + var(--space-16) + var(--space-16)); }
  .shs-kf-item.is-noicon .shs-kf-panel-inner { padding-left: var(--space-16); }   /* no icon → align text to the title */
  .shs-kf-panel-inner p { margin: 0; font-size: var(--fs-md); line-height: var(--lh-relaxed); color: var(--text-body); }
  .shs-kf-panel-inner a { color: var(--primary); text-decoration: underline; text-underline-offset: 2px; }
  .shs-kf-footnote { font-size: var(--fs-md); line-height: var(--lh-relaxed); color: var(--text-body-soft); margin: var(--space-48) 0 0; padding-top: var(--space-32); border-top: 1px solid var(--hairline); }
  .shs-kf-footnote a { color: var(--primary); text-decoration: underline; text-underline-offset: 2px; }
  /* auto-advance progress loader — fills over one cycle, then animationend triggers the next item */
  .shs-kf-bar { position: absolute; left: 0; right: 0; bottom: 0; height: 3px; background: var(--border-grid); opacity: 0; transition: opacity .3s ease; }
  .shs-kf-item.is-open .shs-kf-bar { opacity: 1; }
  .shs-kf-bar-fill { display: block; height: 100%; width: 0; background: var(--primary); }
  .shs-kf-item.is-open .shs-kf-bar-fill { animation: kfLoad var(--kf-dur, 4.2s) linear forwards; }   /* duration = the cycle length */
  /* hover/focus pauses the loader (and therefore the auto-advance) mid-fill; it resumes on leave */
  .shs-kf-section.is-paused .shs-kf-item.is-open .shs-kf-bar-fill { animation-play-state: paused; }
  @keyframes kfLoad { from { width: 0; } to { width: 100%; } }
  .shs-kf-media img { width: 100%; height: auto; display: block; border-radius: var(--radius-2xl); box-shadow: var(--shadow-card); }
  @media (max-width: 900px) {
    .shs-kf-grid { grid-template-columns: 1fr; gap: var(--space-32); }
    .shs-kf-media { order: -1; }
  }
  @media (prefers-reduced-motion: reduce) {
    .shs-kf-item, .shs-kf-panel { transition: none; }
    .shs-kf-item.is-open .shs-kf-bar-fill { animation: none; }
    .shs-kf-bar { display: none; }
  }
</style>
<?php endif; ?>

<section class="shs-kf-section<?php echo $kf['shade'] ? ' is-shaded' : ''; ?><?php echo $kf['reverse'] ? ' is-reversed' : ''; ?>" data-shs-kf data-interval="<?php echo esc_attr( $kf['interval'] ); ?>">
  <div class="shs-kf-wrap">
    <?php if ( $kf['eyebrow'] || $kf['title'] || $kf['lead'] ) : ?>
    <div>
      <?php if ( $kf['eyebrow'] ) : ?><p class="shs-kf-eyebrow"><?php echo esc_html( $kf['eyebrow'] ); ?></p><?php endif; ?>
      <?php if ( $kf['title'] ) : ?><h2 class="shs-kf-h2"><?php echo wp_kses_post( $kf['title'] ); ?></h2><?php endif; ?>
      <?php if ( $kf['lead'] ) : ?><p class="shs-kf-lead"><?php echo wp_kses_post( $kf['lead'] ); ?></p><?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="shs-kf-grid">
      <div class="shs-kf-list">
        <?php
        foreach ( $kf['features'] as $i => $f ) :
			$f    = wp_parse_args( (array) $f, array( 'title' => '', 'text' => '', 'icon' => '' ) );
			$open = ( 0 === $i );
			?>
        <div class="shs-kf-item<?php echo $open ? ' is-open' : ''; ?><?php echo $f['icon'] ? '' : ' is-noicon'; ?>">
          <button type="button" class="shs-kf-head" id="<?php echo esc_attr( $kf_uid . '-head-' . $i ); ?>" aria-expanded="<?php echo $open ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr( $kf_uid . '-panel-' . $i ); ?>">
            <?php if ( $f['icon'] ) : ?><span class="shs-kf-ico" aria-hidden="true"><?php echo $f['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted inline SVG markup ?></span><?php endif; ?>
            <span class="shs-kf-title"><?php echo wp_kses_post( $f['title'] ); ?></span>
          </button>
          <div class="shs-kf-panel" id="<?php echo esc_attr( $kf_uid . '-panel-' . $i ); ?>" role="region" aria-labelledby="<?php echo esc_attr( $kf_uid . '-head-' . $i ); ?>">
            <div class="shs-kf-panel-inner"><p><?php echo wp_kses_post( $f['text'] ); ?></p></div>
          </div>
          <div class="shs-kf-bar" aria-hidden="true"><span class="shs-kf-bar-fill"></span></div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php if ( $kf_img ) : ?>
      <div class="shs-kf-media">
        <img src="<?php echo esc_url( $kf_img ); ?>"<?php echo $kf['image_width'] ? ' width="' . esc_attr( $kf['image_width'] ) . '"' : ''; ?><?php echo $kf['image_height'] ? ' height="' . esc_attr( $kf['image_height'] ) . '"' : ''; ?> alt="<?php echo esc_attr( $kf['image_alt'] ); ?>" loading="lazy" />
      </div>
      <?php endif; ?>
    </div>

    <?php if ( $kf['footnote'] ) : ?>
    <p class="shs-kf-footnote"><?php echo wp_kses_post( $kf['footnote'] ); ?></p>
    <?php endif; ?>
  </div>
</section>

<?php if ( $kf_assets ) : ?>
<script>
  (function () {
    function initKF(sec) {
      var items = [].slice.call(sec.querySelectorAll('.shs-kf-item'));
      if (items.length < 2) { return; }
      var heads  = items.map(function (it) { return it.querySelector('.shs-kf-head'); });
      var bars   = items.map(function (it) { return it.querySelector('.shs-kf-bar-fill'); });
      var inners = items.map(function (it) { return it.querySelector('.shs-kf-panel-inner'); });
      var current = 0;
      var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      var interval = parseFloat(sec.getAttribute('data-interval'));
      if (interval > 0) { sec.style.setProperty('--kf-dur', interval + 's'); }
      // fix the open-panel height to the tallest description so the list never changes size on switch
      function measure() {
        var max = 0;
        inners.forEach(function (el) { if (el) { max = Math.max(max, el.offsetHeight); } });
        sec.style.setProperty('--kf-panel-h', max + 'px');
      }
      function open(i) {
        current = i;
        items.forEach(function (it, n) {
          var on = (n === i);
          it.classList.toggle('is-open', on);
          heads[n].setAttribute('aria-expanded', on ? 'true' : 'false');
        });
        // restart the loader on the now-open bar so its fill always runs from 0
        var bar = bars[i];
        if (!reduce && bar) { bar.style.animation = 'none'; void bar.offsetWidth; bar.style.animation = ''; }
      }
      function next() { open((current + 1) % items.length); }
      measure();
      open(0);
      var rt;
      window.addEventListener('resize', function () { clearTimeout(rt); rt = setTimeout(measure, 150); }, { passive: true });
      if (document.fonts && document.fonts.ready) { document.fonts.ready.then(measure); }
      // the progress bar IS the timer: when its fill finishes (animationend), advance to the
      // next item. Bar and slide share one clock, so they never desync. Auto-play always runs.
      if (!reduce) {
        bars.forEach(function (bar, i) {
          if (!bar) { return; }
          bar.addEventListener('animationend', function () { if (i === current) { next(); } });
        });
      }
      heads.forEach(function (h, i) {
        h.addEventListener('click', function () { open(i); });   // open clicked, loader restarts here
      });
      // pause the auto-advance while the mouse is over the card (or a header is focused); resume on leave.
      // animation-play-state freezes the loader mid-fill, so animationend can't fire → the cycle halts and
      // continues from the same spot afterwards.
      if (!reduce) {
        var hoverZone = sec.querySelector('.shs-kf-grid') || sec;
        var pause  = function () { sec.classList.add('is-paused'); };
        var resume = function () { sec.classList.remove('is-paused'); };
        hoverZone.addEventListener('mouseenter', pause);
        hoverZone.addEventListener('mouseleave', resume);
        hoverZone.addEventListener('focusin', pause);
        hoverZone.addEventListener('focusout', resume);
      }
    }
    function run() {
      var secs = document.querySelectorAll('[data-shs-kf]');
      for (var i = 0; i < secs.length; i++) { initKF(secs[i]); }
    }
    if (document.readyState !== 'loading') { run(); } else { document.addEventListener('DOMContentLoaded', run); }
  })();
</script>
<?php endif; ?>
