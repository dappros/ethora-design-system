<?php
/**
 * Reusable stats band.
 *
 * A full-bleed brand-blue section (the same brand gradient as .cc-section cards:
 * brand-500 → brand-800) with an optional centred header and a row of flat stats
 * separated by thin dividers — each a translucent icon tile, a big number and a label.
 * Self-contained: ships its own CSS once per request; every value is a design token
 * (translucent-white surfaces follow the theme's dark-panel pattern).
 *
 * Pass props via the 3rd arg of get_template_part():
 *
 *   get_template_part( 'template-parts/section-stats', null, array(
 *     'eyebrow' => '', 'title' => '', 'lead' => '',   // optional centred header
 *     'stats'   => array(                              // REQUIRED — any length
 *       array(
 *         'icon'  => '<svg …>…</svg>',                 // line icon (stroke="currentColor")
 *         'value' => '~95%',
 *         'label' => 'faster dev time vs. building from scratch',
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

$sb = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'eyebrow' => '',
		'title'   => '',
		'lead'    => '',
		'stats'   => array(),
	)
);

if ( empty( $sb['stats'] ) ) {
	return;
}

$sb_assets = empty( $GLOBALS['sb_stats_assets'] );
if ( $sb_assets ) {
	$GLOBALS['sb_stats_assets'] = true;
}
?>
<?php if ( $sb_assets ) : ?>
<style>
  /* STATS BAND — full-bleed brand-blue gradient (same as .cc-section cards), flat divided stats. Tokens only. */
  .sb-section { padding: var(--section-y) var(--section-x);
    background: var(--gradient-brand); }
  .sb-section, .sb-section *, .sb-section *::before, .sb-section *::after { box-sizing: border-box; }
  .sb-wrap { max-width: var(--content-max); margin: 0 auto; }
  .sb-head { max-width: var(--container-md); margin: 0 auto var(--space-48); text-align: center; }
  .sb-eyebrow { font-weight: var(--fw-semibold); font-size: var(--fs-eyebrow); letter-spacing: var(--tracking-wide); text-transform: uppercase; color: var(--accent-on-dark); margin: 0 0 var(--space-16); }
  .sb-head h2 { font-weight: var(--fw-bold); font-size: var(--fs-h2); line-height: var(--lh-heading); letter-spacing: var(--tracking-snug); color: var(--white); margin: 0; }
  .sb-lead { font-size: var(--fs-lg); line-height: var(--lh-relaxed); color: var(--text-on-dark); margin: var(--space-16) auto 0; max-width: var(--measure); }

  .sb-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 0; }
  .sb-tile { text-align: center; padding: var(--space-16) var(--space-48); }
  .sb-tile + .sb-tile { border-left: 1px solid rgba(255, 255, 255, .16); }
  .sb-ico { display: inline-flex; align-items: center; justify-content: center; width: 56px; height: 56px; border-radius: var(--radius-xl); background: rgba(255, 255, 255, .14); color: var(--white); margin-bottom: var(--space-16); }
  .sb-ico svg { width: 28px; height: 28px; }
  .sb-value { font-size: var(--fs-cta); font-weight: var(--fw-bold); line-height: var(--lh-tight); color: var(--white); margin: 0 0 var(--space-8); }
  .sb-label { font-size: var(--fs-lg); line-height: var(--lh-base); color: var(--text-on-dark); margin: 0; }
  @media (max-width: 768px) {
    .sb-grid { grid-template-columns: 1fr; }
    .sb-tile { padding: var(--space-32) var(--space-16); }
    .sb-tile + .sb-tile { border-left: 0; border-top: 1px solid rgba(255, 255, 255, .16); }
  }
</style>
<?php endif; ?>

<section class="sb-section">
  <div class="sb-wrap">
    <?php if ( $sb['eyebrow'] || $sb['title'] || $sb['lead'] ) : ?>
    <div class="sb-head">
      <?php if ( $sb['eyebrow'] ) : ?><p class="sb-eyebrow"><?php echo esc_html( $sb['eyebrow'] ); ?></p><?php endif; ?>
      <?php if ( $sb['title'] ) : ?><h2><?php echo wp_kses_post( $sb['title'] ); ?></h2><?php endif; ?>
      <?php if ( $sb['lead'] ) : ?><p class="sb-lead"><?php echo wp_kses_post( $sb['lead'] ); ?></p><?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="sb-grid">
      <?php foreach ( $sb['stats'] as $s ) : ?>
      <div class="sb-tile">
        <?php if ( ! empty( $s['icon'] ) ) : ?>
        <span class="sb-ico"><?php echo $s['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted inline SVG ?></span>
        <?php endif; ?>
        <?php if ( ! empty( $s['value'] ) ) : ?><p class="sb-value"><?php echo wp_kses_post( $s['value'] ); ?></p><?php endif; ?>
        <?php if ( ! empty( $s['label'] ) ) : ?><p class="sb-label"><?php echo wp_kses_post( $s['label'] ); ?></p><?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
