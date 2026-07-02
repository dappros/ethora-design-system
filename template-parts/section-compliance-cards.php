<?php
/**
 * Reusable compliance / trust cards.
 *
 * A responsive grid of white cards (default 4-up), each with a soft-blue rounded
 * icon tile and a green "✓ STATUS" tag on the top row, then a heading and a short
 * description. For trust/compliance strips (GDPR, HIPAA, SOC 2 …) or any
 * "capability + status" grid. Self-contained: ships its own CSS once per request.
 *
 * Pass props via the 3rd arg of get_template_part():
 *
 *   get_template_part( 'template-parts/section-compliance-cards', null, array(
 *     'eyebrow' => '', 'title' => '', 'lead' => '',   // optional header
 *     'shade'   => false,                              // tint the section bg
 *     'cols'    => 4,                                  // desktop columns (2–4)
 *     'cards'   => array(                               // REQUIRED — any length
 *       array(
 *         'icon'   => '<svg …>…</svg>',                // line icon (stroke="currentColor")
 *         'title'  => 'GDPR',
 *         'text'   => 'EU data protection…',            // inline HTML allowed
 *         'status' => 'Compliant',                      // optional green ✓ tag (uppercased in CSS)
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

$cmpl = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'eyebrow' => '',
		'title'   => '',
		'lead'    => '',
		'shade'   => false,
		'cols'    => 4,
		'cards'   => array(),
	)
);

if ( empty( $cmpl['cards'] ) ) {
	return;
}

$cmpl_cols  = (int) $cmpl['cols'];
$cmpl_cols  = ( $cmpl_cols >= 2 && $cmpl_cols <= 4 ) ? $cmpl_cols : 4;
$cmpl_check = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>';

$cmpl_assets = empty( $GLOBALS['cmpl_assets'] );
if ( $cmpl_assets ) {
	$GLOBALS['cmpl_assets'] = true;
}
?>
<?php if ( $cmpl_assets ) : ?>
<style>
  /* COMPLIANCE CARDS — white cards, soft-blue icon tile + green status tag. Tokens only. */
  .cmpl-section { padding: var(--section-y) var(--section-x); }
  .cmpl-section, .cmpl-section *, .cmpl-section *::before, .cmpl-section *::after { box-sizing: border-box; }
  .cmpl-section.is-shaded { background: var(--surface-alt); border-top: 1px solid var(--hairline); border-bottom: 1px solid var(--hairline); }
  .cmpl-wrap { max-width: var(--content-max); margin: 0 auto; }
  .cmpl-head { max-width: var(--container-md); margin: 0 auto var(--space-48); text-align: center; }
  .cmpl-eyebrow { font-weight: var(--fw-semibold); font-size: var(--fs-eyebrow); letter-spacing: var(--tracking-wide); text-transform: uppercase; color: var(--primary); margin: 0 0 var(--space-16); }
  .cmpl-head h2 { font-weight: var(--fw-bold); font-size: var(--fs-h2); line-height: var(--lh-heading); letter-spacing: var(--tracking-snug); color: var(--ink); margin: 0; }
  .cmpl-lead { font-size: var(--fs-lg); line-height: var(--lh-relaxed); color: var(--text-body); margin: var(--space-16) auto 0; max-width: var(--measure); }

  .cmpl-grid { display: grid; grid-template-columns: repeat(var(--cmpl-cols, 4), minmax(0, 1fr)); gap: var(--space-16); }
  .cmpl-card { position: relative; overflow: hidden; display: flex; flex-direction: column; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-2xl); box-shadow: var(--shadow-card); padding: var(--space-32);
    transition: transform .3s ease, box-shadow .3s ease, border-color .3s ease; }
  /* hover: brand blue fills in from the bottom-right corner (same as link-cards) */
  .cmpl-card::before { content: ""; position: absolute; inset: 0; z-index: 0; background: var(--primary);
    clip-path: circle(0% at 100% 100%); transition: clip-path .45s cubic-bezier(.4, 0, .2, 1); }
  .cmpl-card > * { position: relative; z-index: 1; }
  .cmpl-card:hover { transform: scale(1.03); box-shadow: var(--shadow-lift); border-color: var(--primary); }
  .cmpl-card:hover::before { clip-path: circle(150% at 100% 100%); }
  .cmpl-card-top { display: flex; align-items: center; justify-content: space-between; gap: var(--space-16); }
  .cmpl-ico { flex: none; width: 48px; height: 48px; border-radius: var(--radius-lg); background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; transition: background .35s ease, color .35s ease; }
  .cmpl-ico svg { width: 24px; height: 24px; }
  .cmpl-status { display: inline-flex; align-items: center; gap: var(--space-8); color: var(--success-strong); font-size: var(--fs-eyebrow); font-weight: var(--fw-semibold); letter-spacing: var(--tracking-wide); text-transform: uppercase; white-space: nowrap; transition: color .35s ease; }
  .cmpl-status svg { width: 14px; height: 14px; flex: none; }
  .cmpl-card h3 { font-weight: var(--fw-bold); font-size: var(--fs-3xl); line-height: var(--lh-snug); color: var(--ink); margin: var(--space-32) 0 0; transition: color .35s ease; }
  .cmpl-card p { font-size: var(--fs-md); line-height: var(--lh-relaxed); color: var(--text-body); margin: var(--space-16) 0 0; transition: color .35s ease; }
  /* hover: contents turn white over the blue fill */
  .cmpl-card:hover h3, .cmpl-card:hover p, .cmpl-card:hover .cmpl-status { color: #fff; }
  .cmpl-card:hover .cmpl-ico { background: rgba(255, 255, 255, .18); color: #fff; }

  @media (max-width: 900px) { .cmpl-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
  @media (max-width: 560px) { .cmpl-grid { grid-template-columns: 1fr; } }
  @media (prefers-reduced-motion: reduce) { .cmpl-card, .cmpl-card::before { transition: none; } .cmpl-card:hover { transform: none; } }
</style>
<?php endif; ?>

<section class="cmpl-section<?php echo $cmpl['shade'] ? ' is-shaded' : ''; ?>">
  <div class="cmpl-wrap">
    <?php if ( $cmpl['eyebrow'] || $cmpl['title'] || $cmpl['lead'] ) : ?>
    <div class="cmpl-head">
      <?php if ( $cmpl['eyebrow'] ) : ?><p class="cmpl-eyebrow"><?php echo esc_html( $cmpl['eyebrow'] ); ?></p><?php endif; ?>
      <?php if ( $cmpl['title'] ) : ?><h2><?php echo wp_kses_post( $cmpl['title'] ); ?></h2><?php endif; ?>
      <?php if ( $cmpl['lead'] ) : ?><p class="cmpl-lead"><?php echo wp_kses_post( $cmpl['lead'] ); ?></p><?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="cmpl-grid" style="--cmpl-cols: <?php echo esc_attr( $cmpl_cols ); ?>;">
      <?php foreach ( $cmpl['cards'] as $c ) : ?>
      <div class="cmpl-card">
        <div class="cmpl-card-top">
          <?php if ( ! empty( $c['icon'] ) ) : ?>
          <span class="cmpl-ico"><?php echo $c['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted inline SVG ?></span>
          <?php endif; ?>
          <?php if ( ! empty( $c['status'] ) ) : ?>
          <span class="cmpl-status"><?php echo $cmpl_check; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted inline SVG ?><?php echo esc_html( $c['status'] ); ?></span>
          <?php endif; ?>
        </div>
        <?php if ( ! empty( $c['title'] ) ) : ?><h3><?php echo wp_kses_post( $c['title'] ); ?></h3><?php endif; ?>
        <?php if ( ! empty( $c['text'] ) ) : ?><p><?php echo wp_kses_post( $c['text'] ); ?></p><?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
