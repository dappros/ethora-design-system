<?php
/**
 * Reusable trust band — a full-bleed dark brand band (the `.shs-dark` treatment:
 * `--primary-dark` tinted over an image) with a small mono label, a row of customer
 * logos (white/knocked-out) and a grid of headline stats (big number + caption).
 * Self-contained (CSS once per request), tokens only, responsive (4 → 2 → 1 columns).
 *
 *   get_template_part( 'template-parts/section-trust-band', null, array(
 *     'label' => 'Trusted by healthcare, finance, and enterprise teams',
 *     'logos' => array(
 *       array( 'src' => 'images/DrTalks-logo.svg', 'alt' => 'DrTalks' ),
 *       // …
 *     ),
 *     'stats' => array(
 *       array( 'num' => '100%', 'cap' => 'Data ownership, inside your own security perimeter' ),
 *       // …
 *     ),
 *     'image' => 'images/testimonials.png',   // background image (optional; default testimonials.png)
 *   ) );
 *
 * @package ethora-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tb = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'label' => '',
		'logos' => array(),
		'stats' => array(),
		'image' => 'images/testimonials.png',
	)
);

if ( empty( $tb['stats'] ) && empty( $tb['logos'] ) ) {
	return;
}

$tb_uri = get_template_directory_uri();
$tb_src = function ( $img ) use ( $tb_uri ) {
	if ( ! $img ) {
		return '';
	}
	return preg_match( '#^(https?:)?//#', $img ) ? $img : $tb_uri . '/' . ltrim( $img, '/' );
};

$tb_bg_img = $tb_src( $tb['image'] );
$tb_style  = '--tb-cols: ' . max( 1, count( $tb['stats'] ) ) . ';';
if ( $tb_bg_img ) {
	$tb_style .= " background-image: linear-gradient(rgba(0,35,152,.85), rgba(0,35,152,.5)), url('" . esc_url( $tb_bg_img ) . "');";
}

$tb_assets = empty( $GLOBALS['ethora_tb_assets'] );
if ( $tb_assets ) {
	$GLOBALS['ethora_tb_assets'] = true;
}
?>
<?php if ( $tb_assets ) : ?>
<style>
  /* TRUST BAND — full-bleed dark brand band (.shs-dark treatment): logos + headline stats. Tokens only. */
  .tb-band { padding: var(--space-48) var(--section-x); background-color: var(--primary-dark);
    background-size: cover; background-position: center; background-repeat: no-repeat; }
  .tb-band, .tb-band *, .tb-band *::before, .tb-band *::after { box-sizing: border-box; }
  .tb-band .tb-inner { max-width: var(--content-max); margin: 0 auto; }
  .tb-band .tb-label { text-align: center; font-family: var(--font-mono); font-size: var(--fs-eyebrow); font-weight: var(--fw-medium); letter-spacing: var(--tracking-wider); text-transform: uppercase; color: var(--accent-on-dark); margin-bottom: var(--space-16); }
  .tb-band .tb-logos { display: flex; align-items: center; justify-content: center; gap: var(--space-48); flex-wrap: wrap; }
  .tb-band .tb-logos img { height: 34px; width: auto; filter: brightness(0) invert(1); opacity: .85; }
  .tb-band .tb-stats { display: grid; grid-template-columns: repeat(var(--tb-cols, 4), 1fr); gap: var(--space-32); margin-top: var(--space-64); }
  .tb-band .tb-stats:first-child { margin-top: 0; }
  .tb-band .tb-stat { text-align: center; padding: 0 var(--space-8); }
  .tb-band .tb-num { font-family: var(--font-serif); font-weight: var(--fw-semibold); font-size: clamp(17px, 1.9vw, 23px); color: var(--white); letter-spacing: -.02em; line-height: 1.15; white-space: nowrap; }
  .tb-band .tb-cap { font-size: var(--fs-sm); color: var(--text-on-dark); margin-top: var(--space-8); line-height: 1.4; }
  @media (max-width: 900px) { .tb-band .tb-stats { grid-template-columns: repeat(2, 1fr); } }
  @media (max-width: 520px) { .tb-band .tb-stats { grid-template-columns: 1fr; } }
</style>
<?php endif; ?>

<section class="tb-band" style="<?php echo $tb_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- URL escaped above ?>">
  <div class="tb-inner">
    <?php if ( $tb['label'] ) : ?><div class="tb-label"><?php echo wp_kses_post( $tb['label'] ); ?></div><?php endif; ?>
    <?php if ( ! empty( $tb['logos'] ) ) : ?>
    <div class="tb-logos">
      <?php foreach ( $tb['logos'] as $logo ) : $logo = wp_parse_args( (array) $logo, array( 'src' => '', 'alt' => '', 'h' => '' ) ); if ( ! $logo['src'] ) { continue; } ?>
      <img src="<?php echo esc_url( $tb_src( $logo['src'] ) ); ?>" alt="<?php echo esc_attr( $logo['alt'] ); ?>"<?php echo $logo['h'] ? ' style="height:' . intval( $logo['h'] ) . 'px"' : ''; ?> />
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <?php if ( ! empty( $tb['stats'] ) ) : ?>
    <div class="tb-stats">
      <?php foreach ( $tb['stats'] as $stat ) : $stat = wp_parse_args( (array) $stat, array( 'num' => '', 'cap' => '' ) ); ?>
      <div class="tb-stat"><div class="tb-num"><?php echo wp_kses_post( $stat['num'] ); ?></div><div class="tb-cap"><?php echo wp_kses_post( $stat['cap'] ); ?></div></div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>
