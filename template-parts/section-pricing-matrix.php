<?php
/**
 * Reusable pricing / feature comparison matrix.
 *
 * A white rounded card holding a features × plans table: plan columns across the top
 * (one flagged "Most popular" → filled brand-blue header + a tinted highlight column),
 * feature rows grouped under mono section labels, and per-cell values that are a green
 * "included" check, an em-dash "not available", or free text (numbers, "Unlimited", …).
 * Closes with an optional Included / Not-available legend. Self-contained (ships its own
 * CSS once per request), tokens only, responsive (horizontal scroll on narrow screens).
 *
 * Pass props via the 3rd arg of get_template_part():
 *
 *   get_template_part( 'template-parts/section-pricing-matrix', null, array(
 *     'eyebrow' => '', 'title' => 'Feature matrix', 'lead' => '',   // optional centred header
 *     'shade'   => false,                                            // tint the section bg
 *     'plans'   => array(                                            // REQUIRED — the columns
 *       array( 'name' => 'Free',     'price' => '$0/mo' ),
 *       array( 'name' => 'Business', 'price' => '$99/mo', 'popular' => true, 'badge' => 'Most popular' ),
 *       array( 'name' => 'Enterprise','price' => 'Custom' ),
 *     ),
 *     'groups'  => array(                                            // REQUIRED — grouped rows
 *       array( 'label' => 'Messaging & core features', 'rows' => array(
 *         array( 'feature' => '1:1 and group messaging', 'values' => array( true, true, true ) ),
 *         // value === true → green check · false → em-dash · string → text (e.g. '1,000', 'Unlimited')
 *       ) ),
 *     ),
 *     'legend'  => true,                                             // show the Included/Not-available legend
 *     'note'    => '',                                               // small centred footnote (HTML, optional)
 *   ) );
 *
 * @package ethora-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$pm = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'eyebrow' => '',
		'title'   => '',
		'lead'    => '',
		'shade'   => false,
		'plans'        => array(),
		'groups'       => array(),
		'group_labels' => true,   // render the mono group-label separator rows
		'legend'       => true,
		'note'         => '',
	)
);

if ( empty( $pm['plans'] ) || empty( $pm['groups'] ) || ! is_array( $pm['plans'] ) || ! is_array( $pm['groups'] ) ) {
	return;
}

// Which column is highlighted.
$pm_pop = -1;
foreach ( $pm['plans'] as $pm_i => $pm_pl ) {
	if ( ! empty( $pm_pl['popular'] ) ) {
		$pm_pop = $pm_i;
		break;
	}
}

$pm_check = '<span class="pm-yes"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg><span class="pm-sr">Included</span></span>';
$pm_dash  = '<span class="pm-no"><span aria-hidden="true">&mdash;</span><span class="pm-sr">Not available</span></span>';

$pm_cell = function ( $v ) use ( $pm_check, $pm_dash ) {
	if ( true === $v ) {
		return $pm_check;
	}
	if ( false === $v ) {
		return $pm_dash;
	}
	return '<span class="pm-txt">' . wp_kses_post( $v ) . '</span>';
};

$pm_assets = empty( $GLOBALS['ethora_pm_assets'] );
if ( $pm_assets ) {
	$GLOBALS['ethora_pm_assets'] = true;
}
?>
<?php if ( $pm_assets ) : ?>
<style>
  /* PRICING / FEATURE MATRIX — white card, highlighted "popular" column, grouped rows. Tokens only. */
  .pm-section { padding: var(--section-y) var(--section-x); }
  .pm-section, .pm-section *, .pm-section *::before, .pm-section *::after { box-sizing: border-box; }
  .pm-section.is-shaded { background: var(--surface-alt); border-top: 1px solid var(--hairline); border-bottom: 1px solid var(--hairline); }
  .pm-wrap { max-width: var(--content-max); margin: 0 auto; }
  .pm-head { text-align: center; max-width: var(--measure); margin: 0 auto var(--space-48); }
  .pm-eyebrow { font-family: var(--font-mono); font-weight: var(--fw-medium); font-size: var(--fs-eyebrow); letter-spacing: var(--tracking-wide); text-transform: uppercase; color: var(--primary); margin: 0 0 var(--space-8); }
  .pm-h2 { font-family: var(--font-serif); font-weight: var(--fw-medium); font-size: var(--fs-h2); line-height: 1.08; letter-spacing: -.01em; color: var(--ink); margin: 0; }
  .pm-lead { font-size: var(--fs-lg); line-height: var(--lh-relaxed); color: var(--text-body); margin: var(--space-16) 0 0; }
  .pm-card { background: var(--white); border: 1px solid var(--border); border-radius: var(--radius-3xl); box-shadow: var(--shadow-card); }
  .pm-scroll { overflow-x: auto; }
  table.pm { width: 100%; min-width: 720px; border-collapse: collapse; table-layout: fixed; }
  .pm thead { background: transparent; height: auto; color: inherit; }   /* reset the theme's global <thead> band + fixed height */
  .pm-sr { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; border: 0; }
  /* header / plan columns — the header sits on the plain white card (no tint band) */
  .pm-corner { width: 34%; }
  .pm-plan { width: 22%; text-align: center; vertical-align: bottom; padding: var(--space-32) var(--space-16) var(--space-16); }
  .pm-plan-name { display: block; font-family: var(--font-serif); font-weight: var(--fw-semibold); font-size: var(--fs-2xl); color: var(--ink); line-height: var(--lh-snug); }
  .pm-plan-price { display: block; font-size: var(--fs-sm); color: var(--text-caption); margin-top: var(--space-8); }
  .pm-plan.pm-pop { background: var(--primary); border-radius: var(--radius-xl) var(--radius-xl) 0 0; padding-top: var(--space-16); }
  .pm-pop .pm-plan-name { color: var(--white); }
  .pm-pop .pm-plan-price { color: var(--accent-on-dark); }
  .pm-pop-tag { display: block; font-family: var(--font-mono); font-weight: var(--fw-semibold); font-size: var(--fs-eyebrow); letter-spacing: var(--tracking-wide); text-transform: uppercase; color: var(--accent-on-dark); margin-bottom: var(--space-8); }
  /* body */
  .pm tbody th[scope="row"] { text-align: left; font-weight: var(--fw-semibold); color: var(--ink); font-size: var(--fs-sm); line-height: var(--lh-snug); padding: var(--space-16); }
  .pm tbody td { text-align: center; color: var(--text-body); font-size: var(--fs-sm); padding: var(--space-16); }
  .pm tbody td, .pm tbody th[scope="row"] { border-top: 1px solid var(--hairline); }
  /* zebra striping — alternating rows white / soft grey, all the way down */
  .pm tbody tr.pm-row-alt th[scope="row"],
  .pm tbody tr.pm-row-alt td:not(.pm-pop-col) { background: var(--surface-alt); }
  .pm-pop-col { background: var(--tint-blue); }
  .pm-pop-col .pm-txt { color: var(--primary); font-weight: var(--fw-semibold); }
  .pm tbody tr:last-child td.pm-pop-col { border-radius: 0 0 var(--radius-xl) var(--radius-xl); }
  /* group label rows */
  .pm-group td, .pm-group .pm-group-label { border-top: none; background: var(--surface-alt); }
  .pm-group td.pm-pop-col { background: var(--tint-blue); }
  .pm-group .pm-group-label { text-align: left; font-family: var(--font-mono); font-weight: var(--fw-semibold); font-size: var(--fs-eyebrow); letter-spacing: var(--tracking-wide); text-transform: uppercase; color: var(--primary); padding: var(--space-32) var(--space-16) var(--space-8); }
  /* value marks */
  .pm-yes { display: inline-flex; width: 22px; height: 22px; border-radius: var(--radius-pill); background: var(--green-tint); align-items: center; justify-content: center; vertical-align: middle; }
  .pm-yes svg { width: 13px; height: 13px; color: var(--green); }
  .pm-no { color: var(--text-muted); }
  /* legend + note */
  .pm-legend { display: flex; flex-wrap: wrap; gap: var(--space-16) var(--space-32); justify-content: center; margin-top: var(--space-32); font-size: var(--fs-sm); color: var(--text-caption); }
  .pm-legend > span { display: inline-flex; align-items: center; gap: var(--space-8); }
  .pm-note { font-size: var(--fs-sm); line-height: var(--lh-relaxed); color: var(--text-caption); text-align: center; margin: var(--space-16) auto 0; max-width: var(--measure); }
  .pm-note a { color: var(--primary); text-decoration: underline; text-underline-offset: 2px; }
  @media (max-width: 640px) { .pm-card { padding: var(--space-8); } }
</style>
<?php endif; ?>

<section class="pm-section<?php echo $pm['shade'] ? ' is-shaded' : ''; ?>">
  <div class="pm-wrap">
    <?php if ( $pm['eyebrow'] || $pm['title'] || $pm['lead'] ) : ?>
    <div class="pm-head">
      <?php if ( $pm['eyebrow'] ) : ?><p class="pm-eyebrow"><?php echo esc_html( $pm['eyebrow'] ); ?></p><?php endif; ?>
      <?php if ( $pm['title'] ) : ?><h2 class="pm-h2"><?php echo wp_kses_post( $pm['title'] ); ?></h2><?php endif; ?>
      <?php if ( $pm['lead'] ) : ?><p class="pm-lead"><?php echo wp_kses_post( $pm['lead'] ); ?></p><?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="pm-card">
      <div class="pm-scroll">
        <table class="pm">
          <thead>
            <tr>
              <th class="pm-corner" scope="col"><span class="pm-sr">Feature</span></th>
              <?php foreach ( $pm['plans'] as $pm_i => $pm_pl ) : $pm_is_pop = ( $pm_i === $pm_pop ); ?>
              <th class="pm-plan<?php echo $pm_is_pop ? ' pm-pop' : ''; ?>" scope="col">
                <?php if ( $pm_is_pop ) : ?><span class="pm-pop-tag"><?php echo esc_html( ! empty( $pm_pl['badge'] ) ? $pm_pl['badge'] : 'Most popular' ); ?></span><?php endif; ?>
                <span class="pm-plan-name"><?php echo esc_html( $pm_pl['name'] ); ?></span>
                <?php if ( ! empty( $pm_pl['price'] ) ) : ?><span class="pm-plan-price"><?php echo esc_html( $pm_pl['price'] ); ?></span><?php endif; ?>
              </th>
              <?php endforeach; ?>
            </tr>
          </thead>
          <tbody>
            <?php $pm_ri = 0; ?>
            <?php foreach ( $pm['groups'] as $pm_g ) : ?>
              <?php if ( $pm['group_labels'] && ! empty( $pm_g['label'] ) ) : ?>
            <tr class="pm-group">
              <td class="pm-group-label"><?php echo esc_html( $pm_g['label'] ); ?></td>
              <?php foreach ( $pm['plans'] as $pm_i => $pm_pl ) : ?>
              <td class="<?php echo ( $pm_i === $pm_pop ) ? 'pm-pop-col' : ''; ?>"></td>
              <?php endforeach; ?>
            </tr>
              <?php endif; ?>
            <?php
            $pm_rows = isset( $pm_g['rows'] ) && is_array( $pm_g['rows'] ) ? $pm_g['rows'] : array();
            foreach ( $pm_rows as $pm_row ) :
				$pm_vals = isset( $pm_row['values'] ) && is_array( $pm_row['values'] ) ? $pm_row['values'] : array();
				$pm_ri++;
				?>
            <tr class="<?php echo ( 0 === $pm_ri % 2 ) ? 'pm-row-alt' : ''; ?>">
              <th scope="row"><?php echo wp_kses_post( $pm_row['feature'] ); ?></th>
              <?php
              foreach ( $pm['plans'] as $pm_i => $pm_pl ) :
					$pm_v = array_key_exists( $pm_i, $pm_vals ) ? $pm_vals[ $pm_i ] : '';
					?>
              <td class="<?php echo ( $pm_i === $pm_pop ) ? 'pm-pop-col' : ''; ?>"><?php echo $pm_cell( $pm_v ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside $pm_cell ?></td>
              <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <?php if ( $pm['legend'] ) : ?>
    <div class="pm-legend">
      <span><?php echo $pm_check; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted inline SVG ?> Included</span>
      <span><span class="pm-no" aria-hidden="true">&mdash;</span> Not available</span>
    </div>
    <?php endif; ?>

    <?php if ( $pm['note'] ) : ?><p class="pm-note"><?php echo wp_kses_post( $pm['note'] ); ?></p><?php endif; ?>
  </div>
</section>
