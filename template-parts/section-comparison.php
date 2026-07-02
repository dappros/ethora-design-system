<?php
/**
 * Reusable comparison block ("Custom build" vs the recommended option).
 *
 * A capability column on the left, a plain "negative" column (red ✕ per row) and a
 * highlighted brand-blue "positive" column (green ✓ per row, RECOMMENDED badge).
 * Responsive: a 3-column grid on desktop that reflows to per-row cards on mobile.
 * Self-contained (ships its own CSS once per request).
 *
 * Pass props via the 3rd arg of get_template_part():
 *
 *   get_template_part( 'template-parts/section-comparison', null, array(
 *     'eyebrow' => '', 'title' => '', 'lead' => '',    // optional header
 *     'capability_label' => 'Capability',
 *     'col_a' => array( 'title' => 'Custom build', 'subtitle' => 'Building your own', 'icon' => '<svg…>' ),
 *     'col_b' => array( 'title' => 'Ethora SDK', 'subtitle' => 'Managed & pre-built', 'icon' => '<svg…>', 'recommended' => true ),
 *     'rows'  => array(
 *       array( 'capability' => 'Time to deploy', 'a' => '3–6 months…', 'b' => 'Production-ready in 14 days' ),
 *       // …
 *     ),
 *   ) );
 *
 * @package ethora-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cmp = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'eyebrow'          => '',
		'title'            => '',
		'lead'             => '',
		'capability_label' => 'Capability',
		'col_a'            => array(),
		'col_b'            => array(),
		'rows'             => array(),
	)
);

if ( empty( $cmp['rows'] ) || ! is_array( $cmp['rows'] ) ) {
	return;
}

$col_a = wp_parse_args( (array) $cmp['col_a'], array( 'title' => 'Custom build', 'subtitle' => '', 'icon' => '' ) );
$col_b = wp_parse_args( (array) $cmp['col_b'], array( 'title' => 'Ethora SDK', 'subtitle' => '', 'icon' => '', 'recommended' => false ) );

$cmp_x   = '<svg class="cmp-mark cmp-x" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6M9 9l6 6"/></svg>';
$cmp_chk = '<svg class="cmp-mark cmp-check" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="m8.5 12.5 2.5 2.5 4.5-5"/></svg>';

$cmp_assets = empty( $GLOBALS['shs_cmp_assets'] );
if ( $cmp_assets ) {
	$GLOBALS['shs_cmp_assets'] = true;
}
?>
<?php if ( $cmp_assets ) : ?>
<style>
  /* COMPARISON — capability + negative column + highlighted recommended column. Tokens only.
     Desktop = 3-col grid (rows use display:contents); mobile = per-row cards. */
  .cmp-section { padding: var(--section-y) var(--section-x); }
  .cmp-section, .cmp-section *, .cmp-section *::before, .cmp-section *::after { box-sizing: border-box; }
  .cmp-wrap { max-width: var(--content-max); margin: 0 auto; }
  .cmp-eyebrow { font-family: var(--font-mono); font-weight: var(--fw-medium); font-size: var(--fs-eyebrow); letter-spacing: var(--tracking-wide); text-transform: uppercase; color: var(--primary); margin: 0 0 var(--space-8); }
  .cmp-h2 { font-family: var(--font-serif); font-weight: var(--fw-medium); font-size: var(--fs-h2); line-height: 1.08; letter-spacing: -.01em; color: var(--ink); margin: 0; }
  .cmp-lead { font-size: var(--fs-lg); line-height: var(--lh-relaxed); color: var(--text-body); margin: var(--space-16) 0 0; max-width: 640px; }

  .cmp-card { margin-top: var(--space-48); background: #fff; border: 1px solid var(--border); border-radius: var(--radius-3xl); box-shadow: var(--shadow-card); padding: clamp(var(--space-16),1.6vw,var(--space-32)); }
  .cmp-grid { display: grid; grid-template-columns: minmax(0,1fr) minmax(0,1.25fr) minmax(0,1.25fr); position: relative; }
  .cmp-row { display: contents; }
  .cmp-cell { padding: clamp(var(--space-16),2.2vw,var(--space-32)); }
  .cmp-cell-label { display: none; }   /* shown only on mobile cards */

  /* column heads */
  .cmp-head { display: flex; flex-direction: column; justify-content: flex-end; }
  .cmp-head-cap { font-family: var(--font-mono); font-weight: var(--fw-semibold); font-size: var(--fs-xs); letter-spacing: var(--tracking-wide); text-transform: uppercase; color: var(--text-caption); }
  .cmp-col-ico { width: 44px; height: 44px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; margin-bottom: var(--space-16); }
  .cmp-col-ico svg { width: 22px; height: 22px; }
  .cmp-col-a .cmp-col-ico { background: rgba(244,89,82,.12); color: var(--red); }
  .cmp-col-title { font-family: var(--font-serif); font-weight: var(--fw-semibold); font-size: var(--fs-3xl); color: var(--ink); margin: 0; line-height: 1.15; }
  .cmp-col-sub { font-size: var(--fs-sm); font-weight: var(--fw-semibold); color: var(--text-caption); margin: var(--space-8) 0 0; }

  /* highlighted (recommended) column — continuous blue panel down column 3 */
  .cmp-col-b { position: relative; background: var(--primary); border-radius: var(--radius-2xl) var(--radius-2xl) 0 0; color: #fff; }
  .cmp-col-b .cmp-col-ico { background: rgba(255,255,255,.18); color: #fff; }
  .cmp-col-b .cmp-col-title { color: #fff; }
  .cmp-col-b .cmp-col-sub { color: rgba(255,255,255,.82); }
  .cmp-rec { position: absolute; top: var(--space-16); right: var(--space-16); background: #fff; color: var(--primary);
    font-family: var(--font-mono); font-weight: var(--fw-bold); font-size: var(--fs-xs); letter-spacing: var(--tracking-wide); text-transform: uppercase;
    padding: var(--space-8) var(--space-16); border-radius: var(--radius-pill); }
  .cmp-b { background: var(--primary-light); }
  .cmp-row:last-child .cmp-b { border-radius: 0 0 var(--radius-2xl) var(--radius-2xl); }

  /* body cells */
  .cmp-cap { display: flex; align-items: center; font-weight: var(--fw-semibold); font-size: var(--fs-lg); color: var(--ink); border-top: 1px solid var(--hairline); }
  .cmp-opt { display: flex; gap: var(--space-16); align-items: flex-start; font-size: var(--fs-md); line-height: var(--lh-relaxed); }
  .cmp-a { color: var(--text-body); border-top: 1px solid var(--hairline); }
  .cmp-b { color: var(--ink); }
  .cmp-mark { flex: none; margin-top: 1px; }
  .cmp-x { color: var(--red); }
  .cmp-check { color: var(--green); }

  /* MOBILE: stack each row into a card; repeat the column name inside each option */
  @media (max-width: 820px) {
    .cmp-card { background: transparent; border: 0; box-shadow: none; padding: 0; }
    .cmp-grid { display: block; }
    .cmp-row { display: block; border: 1px solid var(--border); border-radius: var(--radius-2xl); padding: var(--space-32); margin-top: var(--space-16); }
    .cmp-row.cmp-row--head { display: none; }
    .cmp-cell { padding: 0; }
    .cmp-cap { display: block; border-top: 0; font-size: var(--fs-xl); margin-bottom: var(--space-16); }
    .cmp-a { border-top: 0; }
    .cmp-b { background: var(--primary-light); border-radius: var(--radius-md); padding: var(--space-16); }
    .cmp-opt { margin-top: var(--space-16); flex-wrap: wrap; }
    .cmp-cell-label { display: block; flex-basis: 100%; font-family: var(--font-mono); font-weight: var(--fw-semibold); font-size: var(--fs-xs); letter-spacing: var(--tracking-wide); text-transform: uppercase; color: var(--text-caption); margin-bottom: var(--space-8); }
    .cmp-b .cmp-cell-label { color: var(--primary); }
  }
</style>
<?php endif; ?>

<section class="cmp-section">
  <div class="cmp-wrap">
    <?php if ( $cmp['eyebrow'] || $cmp['title'] || $cmp['lead'] ) : ?>
    <div class="cmp-head-block">
      <?php if ( $cmp['eyebrow'] ) : ?><p class="cmp-eyebrow"><?php echo esc_html( $cmp['eyebrow'] ); ?></p><?php endif; ?>
      <?php if ( $cmp['title'] ) : ?><h2 class="cmp-h2"><?php echo wp_kses_post( $cmp['title'] ); ?></h2><?php endif; ?>
      <?php if ( $cmp['lead'] ) : ?><p class="cmp-lead"><?php echo wp_kses_post( $cmp['lead'] ); ?></p><?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="cmp-card">
    <div class="cmp-grid">
      <!-- header row -->
      <div class="cmp-row cmp-row--head">
        <div class="cmp-cell cmp-head cmp-head-capcol"><span class="cmp-head-cap"><?php echo esc_html( $cmp['capability_label'] ); ?></span></div>
        <div class="cmp-cell cmp-head cmp-col-a">
          <?php if ( $col_a['icon'] ) : ?><span class="cmp-col-ico" aria-hidden="true"><?php echo $col_a['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><?php endif; ?>
          <p class="cmp-col-title"><?php echo wp_kses_post( $col_a['title'] ); ?></p>
          <?php if ( $col_a['subtitle'] ) : ?><p class="cmp-col-sub"><?php echo esc_html( $col_a['subtitle'] ); ?></p><?php endif; ?>
        </div>
        <div class="cmp-cell cmp-head cmp-col-b">
          <?php if ( $col_b['recommended'] ) : ?><span class="cmp-rec">Recommended</span><?php endif; ?>
          <?php if ( $col_b['icon'] ) : ?><span class="cmp-col-ico" aria-hidden="true"><?php echo $col_b['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><?php endif; ?>
          <p class="cmp-col-title"><?php echo wp_kses_post( $col_b['title'] ); ?></p>
          <?php if ( $col_b['subtitle'] ) : ?><p class="cmp-col-sub"><?php echo esc_html( $col_b['subtitle'] ); ?></p><?php endif; ?>
        </div>
      </div>
      <!-- rows -->
      <?php foreach ( $cmp['rows'] as $row ) : $row = wp_parse_args( (array) $row, array( 'capability' => '', 'a' => '', 'b' => '' ) ); ?>
      <div class="cmp-row">
        <div class="cmp-cell cmp-cap"><?php echo wp_kses_post( $row['capability'] ); ?></div>
        <div class="cmp-cell cmp-a cmp-opt">
          <span class="cmp-cell-label"><?php echo esc_html( $col_a['title'] ); ?></span>
          <?php echo $cmp_x; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
          <span><?php echo wp_kses_post( $row['a'] ); ?></span>
        </div>
        <div class="cmp-cell cmp-b cmp-opt">
          <span class="cmp-cell-label"><?php echo esc_html( $col_b['title'] ); ?></span>
          <?php echo $cmp_chk; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
          <span><?php echo wp_kses_post( $row['b'] ); ?></span>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    </div>
  </div>
</section>
