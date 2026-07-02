<?php
/**
 * Reusable feature cards.
 *
 * A responsive grid of cards on the brand gradient, each with a coloured circular
 * icon, a heading, a short description and an optional "Learn more" link.
 * Self-contained: ships its own CSS once per request; works with ANY number of
 * cards (auto-fit grid).
 *
 * Pass props via the 3rd arg of get_template_part():
 *
 *   get_template_part( 'template-parts/section-feature-cards', null, array(
 *     'eyebrow' => '',                       // optional mono kicker
 *     'title'   => 'Section heading',        // h2 (inline HTML allowed, optional)
 *     'lead'    => 'Optional intro.',         // (optional)
 *     'shade'   => false,                     // tint the section bg + hairline borders (optional)
 *     'cards'   => array(                     // REQUIRED — any length
 *       array(
 *         'title'      => 'Selling',
 *         'text'       => 'Short description.',   // inline HTML allowed
 *         'icon'       => '<svg …>…</svg>',       // white line icon (stroke="currentColor"), optional
 *         'color'      => 'var(--primary)',       // icon-circle colour token (optional, default primary)
 *         'link_url'   => '/path/',               // optional — renders a "Learn more" button
 *         'link_label' => 'Learn more',           // optional (default "Learn more")
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

$fc = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'eyebrow' => '',
		'title'   => '',
		'lead'    => '',
		'shade'   => false,
		'cards'   => array(),
	)
);

if ( empty( $fc['cards'] ) || ! is_array( $fc['cards'] ) ) {
	return;
}

// Emit the shared CSS only once per request, no matter how many instances.
$fc_assets = empty( $GLOBALS['shs_fc_assets'] );
if ( $fc_assets ) {
	$GLOBALS['shs_fc_assets'] = true;
}
?>
<?php if ( $fc_assets ) : ?>
<style>
  /* FEATURE CARDS — coloured icon + heading + text on the brand gradient. Tokens only. */
  .shs-fc-section { padding: var(--section-y) var(--section-x); }
  .shs-fc-section, .shs-fc-section *, .shs-fc-section *::before, .shs-fc-section *::after { box-sizing: border-box; }
  .shs-fc-section.is-shaded { background: var(--surface-alt); border-top: 1px solid var(--hairline); border-bottom: 1px solid var(--hairline); }
  .shs-fc-wrap { max-width: var(--content-max); margin: 0 auto; }
  .shs-fc-eyebrow { font-family: var(--font-mono); font-weight: var(--fw-medium); font-size: var(--fs-eyebrow); letter-spacing: var(--tracking-wide); text-transform: uppercase; color: var(--primary); margin: 0 0 var(--space-8); }
  .shs-fc-h2 { font-family: var(--font-serif); font-weight: var(--fw-medium); font-size: var(--fs-h2); line-height: 1.08; letter-spacing: -.01em; color: var(--ink); margin: 0; max-width: 640px; }
  .shs-fc-lead { font-size: var(--fs-lg); line-height: var(--lh-relaxed); color: var(--text-body); margin: var(--space-16) 0 0; max-width: 560px; }
  .shs-fc-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: var(--space-16); margin-top: var(--space-48); }
  .shs-fc-card { display: flex; flex-direction: column;
    background: linear-gradient(-124deg, rgba(255,255,255,1) 0%, rgba(0,82,205,.16) 72%, rgba(255,255,255,1) 100%);
    border: 1px solid var(--border); border-radius: var(--radius-3xl); padding: clamp(var(--space-32),3vw,var(--space-48)); }
  .shs-fc-ico { flex: none; width: 52px; height: 52px; border-radius: var(--radius-pill); color: #fff;
    display: flex; align-items: center; justify-content: center; margin-bottom: var(--space-16); }
  .shs-fc-ico svg { width: 24px; height: 24px; }
  .shs-fc-card h3 { font-family: var(--font-serif); font-weight: var(--fw-semibold); font-size: var(--fs-h3-lg);
    line-height: var(--lh-snug); letter-spacing: -.01em; color: var(--ink); margin: 0; }
  .shs-fc-text { font-size: var(--fs-md); line-height: var(--lh-relaxed); color: var(--text-body); margin: var(--space-16) 0 0; }
  .shs-fc-text strong, .shs-fc-text b { color: var(--ink); font-weight: var(--fw-semibold); }
  .shs-fc-text a { color: var(--primary); text-decoration: underline; text-underline-offset: 2px; }
  .shs-fc-more { align-self: flex-start; margin-top: auto; display: inline-flex; align-items: center; gap: var(--space-8);
    padding: var(--space-8) var(--space-16); background: #fff; border: 1px solid var(--border); border-radius: var(--radius-pill);
    font-family: var(--font-mono); font-weight: var(--fw-semibold); font-size: var(--fs-xs); letter-spacing: var(--tracking-wide);
    text-transform: uppercase; color: var(--ink); text-decoration: none; transition: background .25s ease, gap .25s ease; }
  .shs-fc-more:hover { background: var(--tint); gap: var(--space-16); }
  .shs-fc-card .shs-fc-text + .shs-fc-more { margin-top: var(--space-16); }
  @media (max-width: 900px) { .shs-fc-grid { grid-template-columns: 1fr; } }
</style>
<?php endif; ?>

<section class="shs-fc-section<?php echo $fc['shade'] ? ' is-shaded' : ''; ?>">
  <div class="shs-fc-wrap">
    <?php if ( $fc['eyebrow'] || $fc['title'] || $fc['lead'] ) : ?>
    <div>
      <?php if ( $fc['eyebrow'] ) : ?><p class="shs-fc-eyebrow"><?php echo esc_html( $fc['eyebrow'] ); ?></p><?php endif; ?>
      <?php if ( $fc['title'] ) : ?><h2 class="shs-fc-h2"><?php echo wp_kses_post( $fc['title'] ); ?></h2><?php endif; ?>
      <?php if ( $fc['lead'] ) : ?><p class="shs-fc-lead"><?php echo wp_kses_post( $fc['lead'] ); ?></p><?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="shs-fc-grid">
      <?php
      foreach ( $fc['cards'] as $card ) :
		$card = wp_parse_args( (array) $card, array( 'title' => '', 'text' => '', 'icon' => '', 'color' => 'var(--primary)', 'link_url' => '', 'link_label' => 'Learn more' ) );
		?>
      <div class="shs-fc-card">
        <?php if ( $card['icon'] ) : ?>
        <span class="shs-fc-ico" aria-hidden="true" style="background:<?php echo esc_attr( $card['color'] ); ?>;"><?php echo $card['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted inline SVG markup ?></span>
        <?php endif; ?>
        <?php if ( $card['title'] ) : ?><h3><?php echo wp_kses_post( $card['title'] ); ?></h3><?php endif; ?>
        <?php if ( $card['text'] ) : ?><p class="shs-fc-text"><?php echo wp_kses_post( $card['text'] ); ?></p><?php endif; ?>
        <?php if ( $card['link_url'] ) : ?>
        <a class="shs-fc-more" href="<?php echo esc_url( $card['link_url'] ); ?>"><?php echo esc_html( $card['link_label'] ); ?> <span aria-hidden="true">&rarr;</span></a>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
