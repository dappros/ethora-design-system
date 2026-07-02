<?php
/**
 * Reusable split card.
 *
 * A brand-gradient card with a heading + paragraphs on one side and an image on
 * the other (the "Reliable and Secure Chat Infrastructure" block design).
 * Self-contained: ships its own CSS once per request; works with or without an
 * image and in either orientation.
 *
 * Pass props via the 3rd arg of get_template_part():
 *
 *   get_template_part( 'template-parts/section-split-card', null, array(
 *     'eyebrow'      => '',                       // optional mono kicker
 *     'title'        => 'Heading',                // h2 (inline HTML allowed)
 *     'paragraphs'   => array( 'First.', 'Second.' ), // one <p> each (inline HTML allowed)
 *     'image'        => 'images/foo.png',         // theme-relative path OR absolute URL (optional)
 *     'image_alt'    => 'Describe the image',
 *     'image_width'  => 929,                        // for CLS (optional)
 *     'image_height' => 676,
 *     'reverse'      => false,                     // true = image on the LEFT, text on the right
 *     'dark'         => false,                     // true = FULL-BLEED brand-blue gradient section (same as .sb-section), white text.
 *                                                  //        Works with or without 'image' (optional, either side via 'reverse').
 *     'pad_top'      => '',                        // optional vertical padding override, e.g. 'var(--space-32)' / '48px'
 *     'pad_bottom'   => '',                        // optional (defaults to the section rhythm when empty)
 *   ) );
 *
 * @package ethora-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sc = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'eyebrow'      => '',
		'title'        => '',
		'paragraphs'   => array(),
		'image'        => '',
		'image_alt'    => '',
		'image_width'  => '',
		'image_height' => '',
		'reverse'      => false,
		'dark'         => false,
		'pad_top'      => '',
		'pad_bottom'   => '',
	)
);

// Allow a theme-relative path ("images/foo.png") or an absolute URL.
$sc_img = $sc['image'];
if ( $sc_img && ! preg_match( '#^(https?:)?//#', $sc_img ) ) {
	$sc_img = get_template_directory_uri() . '/' . ltrim( $sc_img, '/' );
}

// Optional vertical padding overrides (e.g. 'var(--space-32)' or '48px').
$sc_pad = '';
if ( '' !== $sc['pad_top'] ) {
	$sc_pad .= 'padding-top:' . $sc['pad_top'] . ';';
}
if ( '' !== $sc['pad_bottom'] ) {
	$sc_pad .= 'padding-bottom:' . $sc['pad_bottom'] . ';';
}

// Emit the shared CSS only once per request, no matter how many instances.
$sc_assets = empty( $GLOBALS['shs_split_assets'] );
if ( $sc_assets ) {
	$GLOBALS['shs_split_assets'] = true;
}
?>
<?php if ( $sc_assets ) : ?>
<style>
  /* SPLIT CARD — brand-gradient card, heading + paragraphs beside an image. Tokens only. */
  .shs-split-section { padding: var(--section-y-sm) var(--section-x); }
  .shs-split-section, .shs-split-section *, .shs-split-section *::before, .shs-split-section *::after { box-sizing: border-box; }
  .shs-split-card { max-width: var(--content-max); margin: 0 auto;
    background: linear-gradient(-124deg, rgba(255,255,255,1) 0%, rgba(0,82,205,.156) 71%, rgba(255,255,255,1) 100%);
    border: 1px solid var(--border); border-radius: var(--radius-3xl);
    padding: clamp(var(--space-32),4vw,var(--space-48));
    display: flex; flex-wrap: wrap; gap: clamp(var(--space-32),5vw,var(--space-64)); align-items: center; }
  .shs-split-body { flex: 1 1 380px; min-width: 300px; }
  .shs-split-media { flex: 1 1 360px; min-width: 300px; }
  .shs-split-card.is-reversed .shs-split-media { order: -1; }   /* image on the left */
  .shs-split-media img { display: block; width: 100%; height: auto; border-radius: var(--radius-2xl); }
  .shs-split-eyebrow { font-family: var(--font-mono); font-weight: var(--fw-medium); font-size: var(--fs-eyebrow);
    letter-spacing: var(--tracking-wide); text-transform: uppercase; color: var(--primary); margin: 0 0 var(--space-16); }
  .shs-split-body h2 { font-family: var(--font-serif); font-weight: var(--fw-medium); font-size: var(--fs-h2);
    line-height: 1.1; letter-spacing: -.01em; color: var(--ink); margin: 0; }
  .shs-split-body p { font-size: var(--fs-lg); line-height: var(--lh-relaxed); color: var(--text-body); margin: var(--space-16) 0 0; }
  .shs-split-body p a { color: var(--primary); text-decoration: underline; text-underline-offset: 2px; }
  /* dark variant — FULL-BLEED brand-blue gradient (same as .sb-section / .cc-section cards), white text */
  .shs-split-section.is-dark { padding: var(--section-y) var(--section-x);
    background: var(--gradient-brand); }
  .shs-split-section.is-dark .shs-split-card { background: none; border: 0; border-radius: 0; padding: 0; }
  .shs-split-section.is-dark .shs-split-eyebrow { color: var(--accent-on-dark); }
  .shs-split-section.is-dark h2 { color: #fff; }
  .shs-split-section.is-dark p { color: var(--text-on-dark); }
  .shs-split-section.is-dark p a { color: #fff; }
</style>
<?php endif; ?>

<section class="shs-split-section<?php echo $sc['dark'] ? ' is-dark' : ''; ?>"<?php echo $sc_pad ? ' style="' . esc_attr( $sc_pad ) . '"' : ''; ?>>
  <div class="shs-split-card<?php echo $sc['reverse'] ? ' is-reversed' : ''; ?>">
    <div class="shs-split-body">
      <?php if ( $sc['eyebrow'] ) : ?><p class="shs-split-eyebrow"><?php echo esc_html( $sc['eyebrow'] ); ?></p><?php endif; ?>
      <?php if ( $sc['title'] ) : ?><h2><?php echo wp_kses_post( $sc['title'] ); ?></h2><?php endif; ?>
      <?php foreach ( (array) $sc['paragraphs'] as $sc_p ) : ?>
      <p><?php echo wp_kses_post( $sc_p ); ?></p>
      <?php endforeach; ?>
    </div>
    <?php if ( $sc_img ) : ?>
    <div class="shs-split-media">
      <img src="<?php echo esc_url( $sc_img ); ?>"<?php echo $sc['image_width'] ? ' width="' . esc_attr( $sc['image_width'] ) . '"' : ''; ?><?php echo $sc['image_height'] ? ' height="' . esc_attr( $sc['image_height'] ) . '"' : ''; ?> alt="<?php echo esc_attr( $sc['image_alt'] ); ?>" loading="lazy" />
    </div>
    <?php endif; ?>
  </div>
</section>
