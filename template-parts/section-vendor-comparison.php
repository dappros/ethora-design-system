<?php
/**
 * Reusable multi-vendor comparison ("us vs the competitors").
 *
 * A capability-per-row grid across a featured vendor + N competitor columns. The featured
 * vendor is a raised brand-blue card (logo + name + subtitle, a check/value per row, and a
 * "wins" footer); competitors are plain columns whose weak rows get a red ✕ + muted text.
 * Closes with an optional "where we win" row and a footnote paragraph. Built on CSS Grid so
 * every row aligns across columns. Self-contained (CSS once per request), tokens only,
 * horizontal-scroll on narrow screens.
 *
 * Pass props via the 3rd arg of get_template_part():
 *
 *   get_template_part( 'template-parts/section-vendor-comparison', null, array(
 *     'eyebrow' => '', 'title' => 'How we compare', 'lead' => '',   // optional header
 *     'shade'   => false,                                            // tint the section bg
 *     'capabilities' => array( 'Android SDK language', 'Jetpack Compose', … ),  // REQUIRED — row labels
 *     'featured' => array(                                           // REQUIRED — the highlighted column
 *       'name' => 'Ethora', 'subtitle' => 'Open-source · self-hostable',
 *       'logo' => 'images/Logo.svg',                                 // optional (theme-relative/URL)
 *       'wins' => '7 of 9 categories',
 *       'values' => array(                                           // aligned to capabilities
 *         array( 'text' => 'Kotlin (+ Java)', 'neutral' => true ),   // neutral → dot instead of check
 *         'Yes (AGPL)',                                              // plain string → green/white check + text
 *       ),
 *     ),
 *     'competitors' => array(                                        // REQUIRED — each a plain column
 *       array( 'name' => 'Stream', 'subtitle' => 'Cloud only', 'wins' => '2 / 9', 'values' => array(
 *         'Kotlin',                                                  // plain string → neutral text, no mark
 *         array( 'text' => 'No', 'bad' => true ),                    // bad → red ✕ + muted text
 *       ) ),
 *     ),
 *     'wins_label' => 'Where Ethora wins',
 *     'note' => '',                                                  // footnote paragraph (HTML, optional)
 *   ) );
 *
 * @package ethora-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vc = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'eyebrow'      => '',
		'title'        => '',
		'lead'         => '',
		'shade'        => false,
		'capabilities' => array(),
		'featured'     => array(),
		'competitors'  => array(),
		'wins_label'   => '',
		'note'         => '',
		'note_label'   => 'The bottom line',   // small kicker above the note callout (set '' to hide)
		'marks'        => true,   // false → text-only cells (no check / dot / ✕), for text-heavy comparisons
		'featured_last'=> false,  // true → put the featured (Ethora) column LAST (rightmost) instead of first
	)
);

if ( empty( $vc['capabilities'] ) || empty( $vc['featured'] ) || empty( $vc['competitors'] ) ) {
	return;
}

$vc_uri = get_template_directory_uri();
$vc_src = function ( $img ) use ( $vc_uri ) {
	if ( ! $img ) {
		return '';
	}
	return preg_match( '#^(https?:)?//#', $img ) ? $img : $vc_uri . '/' . ltrim( $img, '/' );
};

$vc_caps  = $vc['capabilities'];
$vc_feat  = wp_parse_args( $vc['featured'], array( 'name' => '', 'subtitle' => '', 'logo' => 'images/Logo.svg', 'wins' => '', 'values' => array() ) );
$vc_comps = $vc['competitors'];
$vc_ncol  = 1 + count( $vc_comps );   // featured + competitors
$vc_marks     = ! empty( $vc['marks'] );  // whether to render the check / dot / ✕ marks
$vc_has_wins  = ( $vc['wins_label'] || ! empty( $vc_feat['wins'] ) );  // render the bottom "wins" row?
$vc_feat_last = ! empty( $vc['featured_last'] );  // featured column on the right instead of first

// Marks.
$vc_check = '<span class="vc-m vc-m-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg></span>';
$vc_dot   = '<span class="vc-m vc-m-dot" aria-hidden="true"></span>';
$vc_x     = '<span class="vc-m vc-m-x"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg></span>';

// Featured cell: string → check + text; array(text, neutral) → dot|check + text. (marks off → text only)
$vc_feat_cell = function ( $v ) use ( $vc_check, $vc_dot, $vc_marks ) {
	$text    = is_array( $v ) ? ( isset( $v['text'] ) ? $v['text'] : '' ) : $v;
	$neutral = is_array( $v ) && ! empty( $v['neutral'] );
	$mark    = $vc_marks ? ( $neutral ? $vc_dot : $vc_check ) : '';
	return $mark . '<span class="vc-v">' . wp_kses_post( $text ) . '</span>';
};
// Competitor cell: string → text only; array(text, bad) → red ✕ + muted text. (marks off → no ✕)
$vc_comp_cell = function ( $v ) use ( $vc_x, $vc_marks ) {
	$text = is_array( $v ) ? ( isset( $v['text'] ) ? $v['text'] : '' ) : $v;
	$bad  = is_array( $v ) && ! empty( $v['bad'] );
	return ( $vc_marks && $bad ? $vc_x : '' ) . '<span class="vc-v">' . wp_kses_post( $text ) . '</span>';
};

$vc_assets = empty( $GLOBALS['ethora_vc_assets'] );
if ( $vc_assets ) {
	$GLOBALS['ethora_vc_assets'] = true;
}
?>
<?php if ( $vc_assets ) : ?>
<style>
  /* VENDOR COMPARISON — CSS-grid; featured column is a raised brand-blue card overlay. Tokens only. */
  .vc-section { padding: var(--section-y) var(--section-x); }
  .vc-section, .vc-section *, .vc-section *::before, .vc-section *::after { box-sizing: border-box; }
  .vc-section.is-shaded { background: var(--surface-alt); border-top: 1px solid var(--hairline); border-bottom: 1px solid var(--hairline); }
  .vc-wrap { max-width: var(--content-max); margin: 0 auto; }
  .vc-head { text-align: center; max-width: var(--measure); margin: 0 auto var(--space-48); }
  .vc-eyebrow { font-family: var(--font-mono); font-weight: var(--fw-medium); font-size: var(--fs-eyebrow); letter-spacing: var(--tracking-wide); text-transform: uppercase; color: var(--primary); margin: 0 0 var(--space-8); }
  .vc-h2 { font-family: var(--font-serif); font-weight: var(--fw-medium); font-size: var(--fs-h2); line-height: 1.08; letter-spacing: -.01em; color: var(--ink); margin: 0; }
  .vc-lead { font-size: var(--fs-lg); line-height: var(--lh-relaxed); color: var(--text-body); margin: var(--space-16) 0 0; }
  .vc-scroll { overflow-x: auto; padding: 28px 4px 40px; }
  .vc-grid { position: relative; display: grid;
    grid-template-columns: minmax(150px, 1.5fr) minmax(160px, 1.15fr) repeat(var(--vc-comps, 3), minmax(130px, 1fr));
    grid-template-rows: repeat(var(--vc-rows, 11), auto);   /* explicit rows so the featured card can span 1 / -1 */
    min-width: 860px; align-items: stretch; }
  /* the raised featured card sits BEHIND the featured column cells. It is absolutely
     positioned into grid column 2 (so it overlays without consuming a grid track — the
     content cells still auto-flow normally). top/bottom extend it into the scroll padding. */
  .vc-featured-card { position: absolute; grid-column: 2 / 3; grid-row: 1 / -1; z-index: 0;
    top: -16px; bottom: 0; left: 0; right: 0; border-radius: var(--radius-2xl);
    background: var(--tint-blue); box-shadow: var(--shadow-lift); }
  /* featured column moved to the far right: last grid track + raised card over it */
  .vc-grid.vc-eth-last { grid-template-columns: minmax(150px, 1.5fr) repeat(var(--vc-comps, 3), minmax(130px, 1fr)) minmax(160px, 1.15fr); }
  .vc-grid.vc-eth-last .vc-featured-card { grid-column: -2 / -1; }
  /* base cell */
  .vc-cell { position: relative; z-index: 1; display: flex; align-items: center; gap: var(--space-8);
    padding: var(--space-16); font-size: var(--fs-sm); line-height: var(--lh-snug); min-width: 0; }
  .vc-cap, .vc-comp { border-bottom: 1px solid var(--hairline); }
  .vc-cap { color: var(--text-body); font-weight: var(--fw-semibold); }
  .vc-comp { color: var(--text-body); }
  .vc-comp.is-bad { color: var(--text-caption); }
  .vc-eth { color: var(--ink); }                                   /* light body → dark text */
  .vc-eth.vc-row { border-top: 1px solid var(--primary-tint-10); }  /* subtle divider between light rows */
  .vc-v { min-width: 0; }
  /* header row — bottom-aligned so competitor names line up with the featured subtitle */
  .vc-hrow { align-items: flex-end; padding-top: var(--space-32); padding-bottom: var(--space-16); }
  .vc-cap.vc-hrow, .vc-comp.vc-hrow { border-bottom: 1px solid var(--border); }
  .vc-comp-head { flex-direction: column; align-items: flex-start; gap: 2px; }
  .vc-comp-name { font-size: var(--fs-lg); font-weight: var(--fw-semibold); color: var(--ink); }
  .vc-comp-sub { font-size: var(--fs-xs); color: var(--text-caption); }
  /* featured header (brand) */
  .vc-eth-head { flex-direction: column; align-items: flex-start; gap: var(--space-8); color: var(--white);
    background: var(--primary); margin-top: -16px; padding-top: calc(var(--space-32) + 16px);
    border-radius: var(--radius-2xl) var(--radius-2xl) 0 0; }
  .vc-eth-brand { display: flex; align-items: center; gap: var(--space-8); }
  .vc-eth-logo { width: 28px; height: 28px; border-radius: var(--radius-xs); background: rgba(255,255,255,.16); display: flex; align-items: center; justify-content: center; flex: none; }
  .vc-eth-logo img { width: 18px; height: 18px; filter: brightness(0) invert(1); }
  .vc-eth-name { font-size: var(--fs-xl); font-weight: var(--fw-bold); color: var(--white); }
  .vc-eth-sub { font-size: var(--fs-xs); color: var(--accent-on-dark); line-height: var(--lh-snug); }
  /* value marks */
  .vc-m { flex: none; display: inline-flex; align-items: center; justify-content: center; }
  .vc-m-check { width: 20px; height: 20px; border-radius: var(--radius-pill); background: var(--primary-tint-10); color: var(--primary); }
  .vc-m-check svg { width: 12px; height: 12px; }
  .vc-m-dot { width: 7px; height: 7px; margin: 0 6px; border-radius: var(--radius-pill); background: var(--text-muted); }
  .vc-m-x { color: var(--red); }
  .vc-m-x svg { width: 15px; height: 15px; }
  /* wins row */
  .vc-wrow { padding-top: var(--space-16); padding-bottom: var(--space-16); }
  .vc-cap.vc-wrow { color: var(--text-caption); font-weight: var(--fw-medium); font-size: var(--fs-xs); border-bottom: none; }
  .vc-comp.vc-wrow { justify-content: center; color: var(--text-muted); font-size: var(--fs-sm); border-bottom: none; }
  .vc-eth.vc-wrow { background: var(--primary); color: var(--white); border-radius: 0 0 var(--radius-2xl) var(--radius-2xl); border-top: none; font-weight: var(--fw-semibold); }
  .vc-eth.vc-wrow .vc-m-check { background: rgba(255,255,255,.22); color: var(--white); }
  /* footnote */
  /* note → a soft-blue "takeaway" callout: icon tile + optional kicker + text, centred under the grid */
  .vc-note { display: flex; gap: var(--space-16); align-items: flex-start;
    padding: var(--space-32); background: var(--tint-blue);
    border: 1px solid var(--border); border-radius: var(--radius-2xl); }
  .vc-note-ico { flex: none; width: 2.75rem; height: 2.75rem; border-radius: var(--radius-btn);
    background: var(--primary); color: var(--white); display: flex; align-items: center; justify-content: center; }
  .vc-note-ico svg { width: 22px; height: 22px; }
  .vc-note-body { min-width: 0; }
  .vc-note-label { font-family: var(--font-mono); font-weight: var(--fw-semibold); font-size: var(--fs-xs);
    letter-spacing: var(--tracking-wide); text-transform: uppercase; color: var(--primary); margin: 0 0 var(--space-8); }
  .vc-note-text { font-size: var(--fs-md); line-height: var(--lh-relaxed); color: var(--text-body); margin: 0; }
  .vc-note-text strong { color: var(--ink); font-weight: var(--fw-semibold); }
  .vc-note-text a { color: var(--primary); text-decoration: underline; text-underline-offset: 2px; }
  @media (max-width: 600px) { .vc-note { flex-direction: column; gap: var(--space-16); padding: var(--space-32) var(--space-16); } }
</style>
<?php endif; ?>

<section class="vc-section<?php echo $vc['shade'] ? ' is-shaded' : ''; ?>">
  <div class="vc-wrap">
    <?php if ( $vc['eyebrow'] || $vc['title'] || $vc['lead'] ) : ?>
    <div class="vc-head">
      <?php if ( $vc['eyebrow'] ) : ?><p class="vc-eyebrow"><?php echo esc_html( $vc['eyebrow'] ); ?></p><?php endif; ?>
      <?php if ( $vc['title'] ) : ?><h2 class="vc-h2"><?php echo wp_kses_post( $vc['title'] ); ?></h2><?php endif; ?>
      <?php if ( $vc['lead'] ) : ?><p class="vc-lead"><?php echo wp_kses_post( $vc['lead'] ); ?></p><?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="vc-scroll">
      <div class="vc-grid<?php echo $vc_feat_last ? ' vc-eth-last' : ''; ?>" style="--vc-comps: <?php echo count( $vc_comps ); ?>; --vc-rows: <?php echo count( $vc_caps ) + 1 + ( $vc_has_wins ? 1 : 0 ); ?>;" role="table" aria-label="<?php echo esc_attr( $vc['title'] ? $vc['title'] : 'Vendor comparison' ); ?>">

        <!-- header row -->
        <div class="vc-cell vc-cap vc-hrow" role="columnheader"></div>
        <?php ob_start(); ?>
        <div class="vc-cell vc-eth vc-eth-head vc-hrow" role="columnheader">
          <div class="vc-eth-brand">
            <?php if ( ! empty( $vc_feat['logo'] ) ) : ?><span class="vc-eth-logo"><img src="<?php echo esc_url( $vc_src( $vc_feat['logo'] ) ); ?>" width="18" height="18" alt="" /></span><?php endif; ?>
            <span class="vc-eth-name"><?php echo esc_html( $vc_feat['name'] ); ?></span>
          </div>
          <?php if ( ! empty( $vc_feat['subtitle'] ) ) : ?><span class="vc-eth-sub"><?php echo wp_kses_post( $vc_feat['subtitle'] ); ?></span><?php endif; ?>
        </div>
        <?php
        $vc_eth_head_html = ob_get_clean();
        if ( ! $vc_feat_last ) { echo $vc_eth_head_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped when built above
        }
        foreach ( $vc_comps as $vc_c ) :
            echo '<div class="vc-cell vc-comp vc-comp-head vc-hrow" role="columnheader"><span class="vc-comp-name">' . esc_html( isset( $vc_c['name'] ) ? $vc_c['name'] : '' ) . '</span>' . ( ! empty( $vc_c['subtitle'] ) ? '<span class="vc-comp-sub">' . esc_html( $vc_c['subtitle'] ) . '</span>' : '' ) . '</div>';
        endforeach;
        if ( $vc_feat_last ) { echo $vc_eth_head_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped when built above
        }
        ?>

        <!-- capability rows -->
        <?php foreach ( $vc_caps as $vc_i => $vc_cap ) : ?>
        <div class="vc-cell vc-cap" role="rowheader"><?php echo wp_kses_post( $vc_cap ); ?></div>
        <?php
        $vc_eth_cell = '<div class="vc-cell vc-eth vc-row" role="cell">' . $vc_feat_cell( isset( $vc_feat['values'][ $vc_i ] ) ? $vc_feat['values'][ $vc_i ] : '' ) . '</div>';
        if ( ! $vc_feat_last ) { echo $vc_eth_cell; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside closure
        }
        foreach ( $vc_comps as $vc_c ) :
			$vc_val = isset( $vc_c['values'][ $vc_i ] ) ? $vc_c['values'][ $vc_i ] : '';
			$vc_bad = is_array( $vc_val ) && ! empty( $vc_val['bad'] );
			echo '<div class="vc-cell vc-comp' . ( $vc_bad ? ' is-bad' : '' ) . '" role="cell">' . $vc_comp_cell( $vc_val ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside closure
        endforeach;
        if ( $vc_feat_last ) { echo $vc_eth_cell; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside closure
        }
        ?>
        <?php endforeach; ?>

        <!-- wins row (only when a wins label/value is provided) -->
        <?php if ( $vc_has_wins ) : ?>
        <div class="vc-cell vc-cap vc-wrow" role="rowheader"><?php echo esc_html( $vc['wins_label'] ); ?></div>
        <?php
        $vc_eth_win = '<div class="vc-cell vc-eth vc-wrow" role="cell">' . ( ( $vc_marks && ! empty( $vc_feat['wins'] ) ) ? $vc_check : '' ) . '<span class="vc-v">' . esc_html( $vc_feat['wins'] ) . '</span></div>';
        if ( ! $vc_feat_last ) { echo $vc_eth_win; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped when built above
        }
        foreach ( $vc_comps as $vc_c ) {
            echo '<div class="vc-cell vc-comp vc-wrow" role="cell">' . esc_html( isset( $vc_c['wins'] ) ? $vc_c['wins'] : '' ) . '</div>';
        }
        if ( $vc_feat_last ) { echo $vc_eth_win; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped when built above
        }
        ?>
        <?php endif; ?>

        <!-- raised featured card (behind the featured column) -->
        <div class="vc-featured-card" aria-hidden="true"></div>
      </div>
    </div>

    <?php if ( $vc['note'] ) : ?>
    <div class="vc-note">
      <span class="vc-note-ico" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18h6"/><path d="M10 22h4"/><path d="M15.09 14c.18-.98.65-1.74 1.41-2.5A4.65 4.65 0 0 0 18 8 6 6 0 0 0 6 8c0 1 .23 2.23 1.5 3.5A4.6 4.6 0 0 1 8.91 14"/></svg></span>
      <div class="vc-note-body">
        <?php if ( $vc['note_label'] ) : ?><p class="vc-note-label"><?php echo esc_html( $vc['note_label'] ); ?></p><?php endif; ?>
        <p class="vc-note-text"><?php echo wp_kses_post( $vc['note'] ); ?></p>
      </div>
    </div>
    <?php endif; ?>
  </div>
</section>
