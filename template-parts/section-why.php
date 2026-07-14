<?php
/**
 * Reusable "why" section (the ".shs-why" pattern).
 *
 * A full-width head (eyebrow + title + lead) on top, then a plain vertical stack
 * of alternating image/text rows: the first row is image-left / text-right, the
 * next flips (image-right / text-left), and so on. No scroll interaction — it is
 * a simple top-to-bottom list. Self-contained (ships its own CSS once per
 * request) and supports multiple instances per page. On mobile every row stacks
 * with the image on top.
 *
 * Pass props via the 3rd arg of get_template_part():
 *
 *   get_template_part( 'template-parts/section-why', null, array(
 *     'eyebrow'  => 'Why self-host',            // optional mono kicker
 *     'title'    => 'Section heading',           // h2
 *     'lead'     => 'Intro paragraph.',           // (optional)
 *     'numbered' => true,                         // show 01/02/03 markers (default true)
 *     'frame'    => true,                         // false → images show whole, no card frame/shadow
 *     'steps'    => array(                         // REQUIRED — 1+ rows, each with its own image
 *       array(
 *         'title'     => 'Complete data ownership',
 *         'text'      => 'Full control over infrastructure…',
 *         'image'     => 'images/foo.png',         // theme-relative path or URL
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
		'frame'    => true, // false → images show whole (no card frame/shadow)
		'steps'    => array(),
	)
);

if ( empty( $wy['steps'] ) || ! is_array( $wy['steps'] ) ) {
	return;
}

$wy_uri = get_template_directory_uri();

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
  /* WHY — full-width head + a stack of alternating image/text rows. Tokens only. Self-contained. */
  .shs-why { padding: var(--section-y) var(--section-x); }
  .shs-why, .shs-why *, .shs-why *::before, .shs-why *::after { box-sizing: border-box; }
  .shs-why .why-inner { max-width: var(--content-max); margin: 0 auto; }

  /* ---- Full-width head ---- */
  .shs-why .why-head { width: 100%; }
  .shs-why .why-eyebrow { font-family: var(--font-mono); font-weight: var(--fw-medium); font-size: var(--fs-eyebrow); letter-spacing: var(--tracking-wide); text-transform: uppercase; color: var(--primary); margin: 0; }
  .shs-why .why-head h2 { font-family: var(--font-serif); font-weight: 500; font-size: var(--fs-h2); line-height: var(--lh-tight); letter-spacing: var(--tracking-tight); color: var(--ink); margin: var(--space-16) 0 0; }
  .shs-why .why-lead { font-size: var(--fs-lg); line-height: var(--lh-relaxed); color: var(--text-body); margin: var(--space-16) 0 0; max-width: var(--measure); }

  /* ---- Connected ribbon ----
     The gradient wash is ONE SVG path (built from the panel geometry by the script
     below), so the whole zigzag is a SINGLE shape: outer corners are convex, the
     joins between blocks are concave fillets belonging to that same shape, and the
     gradient is genuinely continuous with no bleed outside it. Text sits transparent
     on top of the ribbon; images sit in the notches on the page canvas. */
  .shs-why .why-rows { margin-top: var(--space-64); position: relative; display: flex; flex-direction: column; }
  .shs-why .why-ribbon { position: absolute; inset: 0; z-index: 0; pointer-events: none; }
  .shs-why .why-ribbon svg { position: absolute; inset: 0; width: 100%; height: 100%; display: block; }
  .shs-why .why-row { position: relative; z-index: 1; display: grid; grid-template-columns: 38% 62%; align-items: stretch; }
  .shs-why .why-row.is-reverse { grid-template-columns: 62% 38%; }
  /* default row: image left (col 1), text right (col 2) */
  .shs-why .why-row-media { order: 1; }
  .shs-why .why-row-text  { order: 2; }
  /* odd rows flip: text left, image right */
  .shs-why .why-row.is-reverse .why-row-media { order: 2; }
  .shs-why .why-row.is-reverse .why-row-text  { order: 1; }

  .shs-why .why-row-media { display: flex; align-items: center; justify-content: center; padding: var(--space-32); }
  .shs-why .why-row-media img { width: 100%; height: auto; display: block; border-radius: var(--radius-3xl); box-shadow: var(--shadow-lift); }
  .shs-why.no-frame .why-row-media img { border-radius: 0; box-shadow: none; }

  /* text panel is transparent — the single SVG ribbon shows through behind it */
  .shs-why .why-row-text { background: transparent; padding: clamp(var(--space-32), 4vw, var(--space-48)); display: flex; flex-direction: column; justify-content: center; }
  .shs-why .ws-num { font-family: var(--font-mono); font-size: var(--fs-sm); font-weight: var(--fw-bold); color: var(--primary); letter-spacing: .12em; }
  .shs-why .why-row-text h3 { font-family: var(--font-serif); font-weight: 600; font-size: clamp(24px,3vw,34px); color: var(--ink); line-height: var(--lh-snug); margin: var(--space-16) 0 0; }
  .shs-why .why-row-text p { font-size: var(--fs-lg); line-height: var(--lh-relaxed); color: var(--text-body); margin: var(--space-16) 0 0; max-width: var(--measure); }

  /* MOBILE: single column — hide the SVG ribbon; each panel becomes its own rounded card */
  @media (max-width: 900px) {
    .shs-why .why-rows { gap: var(--space-32); }
    .shs-why .why-ribbon { display: none; }
    .shs-why .why-row, .shs-why .why-row.is-reverse { grid-template-columns: 1fr; }
    .shs-why .why-row-media, .shs-why .why-row.is-reverse .why-row-media { order: 1; padding: 0; }
    .shs-why .why-row-text,  .shs-why .why-row.is-reverse .why-row-text  { order: 2; background: var(--gradient-soft-brand); border-radius: var(--radius-2xl); }
  }
</style>
<?php endif; ?>

<section class="shs-why<?php echo $wy['frame'] ? '' : ' no-frame'; ?>">
  <div class="why-inner">
    <div class="why-head">
      <?php if ( $wy['eyebrow'] ) : ?><span class="why-eyebrow"><?php echo esc_html( $wy['eyebrow'] ); ?></span><?php endif; ?>
      <?php if ( $wy['title'] ) : ?><h2><?php echo wp_kses_post( $wy['title'] ); ?></h2><?php endif; ?>
      <?php if ( $wy['lead'] ) : ?><p class="why-lead"><?php echo wp_kses_post( $wy['lead'] ); ?></p><?php endif; ?>
    </div>

    <div class="why-rows">
      <div class="why-ribbon" aria-hidden="true"></div>
      <?php foreach ( $wy['steps'] as $i => $st ) : $st = wp_parse_args( (array) $st, array( 'title' => '', 'text' => '', 'image' => '', 'image_alt' => '' ) ); ?>
      <article class="why-row<?php echo ( $i % 2 ) ? ' is-reverse' : ''; ?>">
        <div class="why-row-media">
          <?php if ( $st['image'] ) : ?><img src="<?php echo esc_url( $wy_src( $st['image'] ) ); ?>" alt="<?php echo esc_attr( $st['image_alt'] ); ?>" loading="lazy" /><?php endif; ?>
        </div>
        <div class="why-row-text">
          <?php if ( $st['title'] ) : ?><h3><?php echo wp_kses_post( $st['title'] ); ?></h3><?php endif; ?>
          <?php if ( $st['text'] ) : ?><p><?php echo wp_kses_post( $st['text'] ); ?></p><?php endif; ?>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php if ( $wy_assets ) : ?>
<script>
/* Build the WHY ribbon as one SVG path (convex outer corners + concave joins, single
   gradient) from the live panel geometry. Rebuilds on resize / content reflow. */
(function () {
  function roundedPolygon(pts, r) {
    var q = [];
    for (var i = 0; i < pts.length; i++) {
      var a = pts[i], b = q[q.length - 1];
      if (!b || Math.abs(a[0] - b[0]) > 0.5 || Math.abs(a[1] - b[1]) > 0.5) q.push(a);
    }
    if (q.length > 1) {
      var f = q[0], l = q[q.length - 1];
      if (Math.abs(f[0] - l[0]) < 0.5 && Math.abs(f[1] - l[1]) < 0.5) q.pop();
    }
    var n = q.length; if (n < 3) return '';
    var d = '';
    for (var i = 0; i < n; i++) {
      var prev = q[(i - 1 + n) % n], cur = q[i], next = q[(i + 1) % n];
      var v1x = prev[0] - cur[0], v1y = prev[1] - cur[1];
      var v2x = next[0] - cur[0], v2y = next[1] - cur[1];
      var l1 = Math.hypot(v1x, v1y), l2 = Math.hypot(v2x, v2y);
      if (l1 < 0.5 || l2 < 0.5) continue;
      var rr = Math.min(r, l1 / 2, l2 / 2);
      var t1x = cur[0] + v1x / l1 * rr, t1y = cur[1] + v1y / l1 * rr;
      var t2x = cur[0] + v2x / l2 * rr, t2y = cur[1] + v2y / l2 * rr;
      var cross = v1x * v2y - v1y * v2x;
      var sweep = cross < 0 ? 1 : 0;
      d += (i === 0 ? 'M' : 'L') + t1x.toFixed(1) + ' ' + t1y.toFixed(1)
         + 'A' + rr.toFixed(1) + ' ' + rr.toFixed(1) + ' 0 0 ' + sweep + ' ' + t2x.toFixed(1) + ' ' + t2y.toFixed(1);
    }
    return d + 'Z';
  }
  function ribbonPath(panels, r) {
    var n = panels.length, v = [];
    v.push([panels[0].x0, panels[0].top]);
    v.push([panels[0].x1, panels[0].top]);
    for (var i = 0; i < n; i++) { v.push([panels[i].x1, panels[i].bottom]); if (i < n - 1) v.push([panels[i + 1].x1, panels[i].bottom]); }
    v.push([panels[n - 1].x0, panels[n - 1].bottom]);
    for (var i = n - 1; i >= 0; i--) { v.push([panels[i].x0, panels[i].top]); if (i > 0) v.push([panels[i - 1].x0, panels[i].top]); }
    return roundedPolygon(v, r);
  }
  var uid = 0;
  function build(sec) {
    var rows = sec.querySelector('.why-rows'), ribbon = sec.querySelector('.why-ribbon');
    if (!rows || !ribbon) return;
    if (window.innerWidth <= 900) { ribbon.innerHTML = ''; return; }
    var texts = [].slice.call(sec.querySelectorAll('.why-row-text'));
    if (!texts.length) return;
    var rb = rows.getBoundingClientRect();
    var panels = texts.map(function (el) {
      var b = el.getBoundingClientRect();
      return { x0: b.left - rb.left, x1: b.right - rb.left, top: b.top - rb.top, bottom: b.bottom - rb.top };
    }).sort(function (a, b) { return a.top - b.top; });
    var W = Math.round(rb.width), H = Math.round(rb.height);
    var d = ribbonPath(panels, 24);
    var id = 'whyGrad' + (sec._whyId || (sec._whyId = ++uid));
    // Match CSS linear-gradient(-124deg, …): gradient line endpoints for a W×H box.
    var ang = -124 * Math.PI / 180, gx = Math.sin(ang), gy = -Math.cos(ang);
    var gl = (Math.abs(W * gx) + Math.abs(H * gy)) / 2, cx = W / 2, cy = H / 2;
    var x1 = (cx - gx * gl).toFixed(1), y1 = (cy - gy * gl).toFixed(1);
    var x2 = (cx + gx * gl).toFixed(1), y2 = (cy + gy * gl).toFixed(1);
    ribbon.innerHTML =
      '<svg width="' + W + '" height="' + H + '" viewBox="0 0 ' + W + ' ' + H + '" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">'
      + '<defs><linearGradient id="' + id + '" gradientUnits="userSpaceOnUse" x1="' + x1 + '" y1="' + y1 + '" x2="' + x2 + '" y2="' + y2 + '">'
      + '<stop offset="0" style="stop-color:var(--white)"/>'
      + '<stop offset="0.25" style="stop-color:var(--primary);stop-opacity:.03"/>'
      + '<stop offset="0.5" style="stop-color:var(--primary);stop-opacity:.08"/>'
      + '<stop offset="0.75" style="stop-color:var(--primary);stop-opacity:.03"/>'
      + '<stop offset="1" style="stop-color:var(--white)"/>'
      + '</linearGradient></defs>'
      + '<path d="' + d + '" fill="url(#' + id + ')"/></svg>';
  }
  function init(sec) {
    var rows = sec.querySelector('.why-rows'); if (!rows) return;
    var raf;
    function schedule() { if (raf) cancelAnimationFrame(raf); raf = requestAnimationFrame(function () { build(sec); }); }
    schedule();
    if ('ResizeObserver' in window) { new ResizeObserver(schedule).observe(rows); }
    window.addEventListener('resize', schedule, { passive: true });
    window.addEventListener('load', schedule);
  }
  function run() { var s = document.querySelectorAll('.shs-why'); for (var i = 0; i < s.length; i++) init(s[i]); }
  if (document.readyState !== 'loading') run(); else document.addEventListener('DOMContentLoaded', run);
})();
</script>
<?php endif; ?>
