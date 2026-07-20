<?php
/**
 * Reusable "feature list + media" section — a two-column block: a vertical list of
 * icon + heading + description rows (with hairline dividers) on one side, and a framed
 * product image on the other (or a tasteful dashed "drop an image" placeholder when no
 * image is passed). Self-contained (CSS once per request), tokens only, stacks ≤900px.
 *
 *   get_template_part( 'template-parts/section-feature-list-media', null, array(
 *     'eyebrow' => 'Why Ethora',
 *     'title'   => 'Why developers choose Ethora over alternatives',
 *     'shade'   => false,
 *     'reverse' => false,                          // media on the LEFT
 *     'media'   => 'images/product-light.png',      // theme-relative/URL (optional)
 *     'media_alt' => 'Describe the image',
 *     'placeholder' => 'Drop a product screenshot or brand image',  // shown when no media
 *     'items'   => array(
 *       array( 'icon' => '<svg …>…</svg>', 'title' => 'Open source with self-hosting', 'text' => '… <a href="…">deploy…</a> …' ),
 *       // …
 *     ),
 *   ) );
 *
 * @package ethora-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$flm = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'eyebrow'     => '',
		'title'       => '',
		'lead'        => '',
		'shade'       => false,
		'reverse'     => false,
		'media'        => '',
		'media_alt'    => '',
		'media_shadow' => true,   // framed image casts a soft drop shadow by default; pass false for a flat, shadowless visual
		'placeholder' => 'Drop a product screenshot or brand image',
		'items'       => array(),
	)
);

if ( empty( $flm['items'] ) || ! is_array( $flm['items'] ) ) {
	return;
}

$flm_uri = get_template_directory_uri();
$flm_src = '';
if ( $flm['media'] ) {
	$flm_src = preg_match( '#^(https?:)?//#', $flm['media'] ) ? $flm['media'] : $flm_uri . '/' . ltrim( $flm['media'], '/' );
}

$flm_assets = empty( $GLOBALS['ethora_flm_assets'] );
if ( $flm_assets ) {
	$GLOBALS['ethora_flm_assets'] = true;
}
?>
<?php if ( $flm_assets ) : ?>
<style>
  /* FEATURE LIST + MEDIA — icon/heading/text rows beside a framed image. Tokens only. */
  .flm-section { padding: var(--section-y) var(--section-x); }
  .flm-section, .flm-section *, .flm-section *::before, .flm-section *::after { box-sizing: border-box; }
  .flm-section.is-shaded { background: var(--surface-alt); border-top: 1px solid var(--hairline); border-bottom: 1px solid var(--hairline); }
  .flm-wrap { max-width: var(--content-max); margin: 0 auto; }
  .flm-head { max-width: var(--measure); margin-bottom: var(--space-48); }
  .flm-eyebrow { font-family: var(--font-mono); font-weight: var(--fw-medium); font-size: var(--fs-eyebrow); letter-spacing: var(--tracking-wide); text-transform: uppercase; color: var(--primary); margin: 0 0 var(--space-16); }
  .flm-h2 { font-family: var(--font-serif); font-weight: var(--fw-medium); font-size: var(--fs-h2); line-height: 1.08; letter-spacing: -.01em; color: var(--ink); margin: 0; }
  .flm-lead { font-size: var(--fs-lg); line-height: var(--lh-relaxed); color: var(--text-body); margin: var(--space-16) 0 0; }
  .flm-grid { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: clamp(var(--space-32), 5vw, var(--space-64)); align-items: center; }
  .flm-grid.is-reverse .flm-media { order: -1; }
  /* list */
  .flm-item { display: flex; gap: var(--space-16); padding: var(--space-32) 0; }
  .flm-item + .flm-item { border-top: 1px solid var(--hairline); }
  .flm-item:first-child { padding-top: 0; }
  .flm-item:last-child { padding-bottom: 0; }
  .flm-item-icon { flex: none; width: 44px; height: 44px; border-radius: var(--radius-md); background: var(--tint); color: var(--primary); display: flex; align-items: center; justify-content: center; }
  .flm-item-icon svg { width: 22px; height: 22px; }
  .flm-item-body { min-width: 0; }
  .flm-item-title { font-family: var(--font-serif); font-weight: var(--fw-semibold); font-size: var(--fs-xl); color: var(--ink); line-height: var(--lh-snug); margin: 0; }
  .flm-item-text { font-size: var(--fs-md); line-height: var(--lh-relaxed); color: var(--text-body-soft); margin: var(--space-8) 0 0; }
  .flm-item-text a { color: var(--primary); font-weight: var(--fw-semibold); text-decoration: underline; text-underline-offset: 2px; }
  /* media */
  .flm-media img { width: 100%; height: auto; display: block; border-radius: var(--radius-2xl); box-shadow: var(--shadow-lift); }
  .flm-media.is-flat img { box-shadow: none; }   /* opt-out via 'media_shadow' => false */
  .flm-ph { display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; min-height: 380px; padding: var(--space-32);
    border: 2px dashed var(--border-strong); border-radius: var(--radius-2xl); background: var(--surface-alt); }
  .flm-ph svg { width: 40px; height: 40px; color: var(--text-muted); margin-bottom: var(--space-16); }
  .flm-ph-title { font-size: var(--fs-md); color: var(--text-caption); }
  .flm-ph-sub { font-size: var(--fs-sm); color: var(--text-muted); margin-top: var(--space-8); }
  @media (max-width: 900px) { .flm-grid { grid-template-columns: 1fr; } .flm-grid.is-reverse .flm-media { order: 0; } }
</style>
<?php endif; ?>

<section class="flm-section<?php echo $flm['shade'] ? ' is-shaded' : ''; ?>">
  <div class="flm-wrap">
    <?php if ( $flm['eyebrow'] || $flm['title'] || $flm['lead'] ) : ?>
    <div class="flm-head">
      <?php if ( $flm['eyebrow'] ) : ?><p class="flm-eyebrow"><?php echo esc_html( $flm['eyebrow'] ); ?></p><?php endif; ?>
      <?php if ( $flm['title'] ) : ?><h2 class="flm-h2"><?php echo wp_kses_post( $flm['title'] ); ?></h2><?php endif; ?>
      <?php if ( $flm['lead'] ) : ?><p class="flm-lead"><?php echo wp_kses_post( $flm['lead'] ); ?></p><?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="flm-grid<?php echo $flm['reverse'] ? ' is-reverse' : ''; ?>">
      <div class="flm-list">
        <?php foreach ( $flm['items'] as $it ) : $it = wp_parse_args( (array) $it, array( 'icon' => '', 'title' => '', 'text' => '' ) ); ?>
        <div class="flm-item">
          <?php if ( $it['icon'] ) : ?><span class="flm-item-icon" aria-hidden="true"><?php echo $it['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted inline SVG ?></span><?php endif; ?>
          <div class="flm-item-body">
            <?php if ( $it['title'] ) : ?><h3 class="flm-item-title"><?php echo wp_kses_post( $it['title'] ); ?></h3><?php endif; ?>
            <?php if ( $it['text'] ) : ?><p class="flm-item-text"><?php echo wp_kses_post( $it['text'] ); ?></p><?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <div class="flm-media<?php echo $flm['media_shadow'] ? '' : ' is-flat'; ?>">
        <?php if ( $flm_src ) : ?>
        <img src="<?php echo esc_url( $flm_src ); ?>" alt="<?php echo esc_attr( $flm['media_alt'] ); ?>" loading="lazy" />
        <?php else : ?>
        <div class="flm-ph">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
          <span class="flm-ph-title"><?php echo esc_html( $flm['placeholder'] ); ?></span>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
