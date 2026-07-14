<?php
/**
 * Reusable feature rows — a vertical stack of "row cards": a soft-blue icon tile on the
 * left, a heading + description in the middle, and one or more status pills on the right
 * (e.g. a green "✓ Available" + a blue "⚙ Customizable"). Self-contained (CSS once per
 * request), tokens only, responsive (pills wrap below the text on narrow screens).
 *
 * Pass props via the 3rd arg of get_template_part():
 *
 *   get_template_part( 'template-parts/section-feature-rows', null, array(
 *     'eyebrow' => '', 'title' => 'Feature availability', 'lead' => '',   // optional header
 *     'shade'   => false,                                                  // tint the section bg
 *     'items'   => array(                                                  // REQUIRED
 *       array(
 *         'icon'  => '<svg …>…</svg>',                 // line icon (stroke="currentColor")
 *         'title' => 'Chat / Messaging',
 *         'text'  => 'Real-time communication…',        // inline HTML allowed
 *         'tags'  => array(                             // right-side pills (optional, any number)
 *           array( 'label' => 'Available',    'variant' => 'success', 'icon' => '<svg …>…</svg>' ),
 *           array( 'label' => 'Customizable', 'variant' => 'brand',   'icon' => '<svg …>…</svg>' ),
 *         ),
 *       ),
 *     ),
 *   ) );
 *
 * @package ethora-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$fr = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'eyebrow' => '',
		'title'   => '',
		'lead'    => '',
		'shade'   => false,
		'items'   => array(),
	)
);

if ( empty( $fr['items'] ) || ! is_array( $fr['items'] ) ) {
	return;
}

$fr_assets = empty( $GLOBALS['ethora_fr_assets'] );
if ( $fr_assets ) {
	$GLOBALS['ethora_fr_assets'] = true;
}
?>
<?php if ( $fr_assets ) : ?>
<style>
  /* FEATURE ROWS — stacked row cards: icon tile + heading/text + status pills. Tokens only. */
  .fr-section { padding: var(--section-y) var(--section-x); }
  .fr-section, .fr-section *, .fr-section *::before, .fr-section *::after { box-sizing: border-box; }
  .fr-section.is-shaded { background: var(--surface-alt); border-top: 1px solid var(--hairline); border-bottom: 1px solid var(--hairline); }
  .fr-wrap { max-width: var(--content-max); margin: 0 auto; }
  .fr-head { text-align: center; max-width: var(--measure); margin: 0 auto var(--space-48); }
  .fr-eyebrow { font-family: var(--font-mono); font-weight: var(--fw-medium); font-size: var(--fs-eyebrow); letter-spacing: var(--tracking-wide); text-transform: uppercase; color: var(--primary); margin: 0 0 var(--space-8); }
  .fr-h2 { font-family: var(--font-serif); font-weight: var(--fw-medium); font-size: var(--fs-h2); line-height: 1.08; letter-spacing: -.01em; color: var(--ink); margin: 0; }
  .fr-lead { font-size: var(--fs-lg); line-height: var(--lh-relaxed); color: var(--text-body); margin: var(--space-16) 0 0; }
  .fr-list { display: flex; flex-direction: column; gap: var(--space-16); }
  .fr-card { display: flex; align-items: center; gap: var(--space-16);
    background: var(--white); border: 1px solid var(--border); border-radius: var(--radius-2xl);
    padding: var(--space-16) var(--space-32); box-shadow: var(--shadow-card);
    transition: box-shadow .25s ease, transform .25s ease, border-color .25s ease; }
  .fr-card:hover { box-shadow: var(--shadow-lift); border-color: var(--border-strong); }
  .fr-icon { flex: none; width: 56px; height: 56px; border-radius: var(--radius-lg); background: var(--tint);
    display: flex; align-items: center; justify-content: center; color: var(--primary); }
  .fr-icon svg { width: 26px; height: 26px; }
  .fr-body { flex: 1 1 auto; min-width: 0; }
  .fr-title { font-family: var(--font-serif); font-weight: var(--fw-semibold); font-size: var(--fs-xl); color: var(--ink); line-height: var(--lh-snug); margin: 0; }
  .fr-text { font-size: var(--fs-sm); line-height: var(--lh-relaxed); color: var(--text-body-soft); margin: var(--space-8) 0 0; }
  .fr-tags { flex: none; display: flex; align-items: center; gap: var(--space-8); flex-wrap: wrap; }
  .fr-tag { display: inline-flex; align-items: center; gap: var(--space-8); padding: var(--space-4) var(--space-8);
    border-radius: var(--radius-pill); font-size: var(--fs-sm); font-weight: var(--fw-medium); white-space: nowrap; border: 1px solid transparent; }
  .fr-tag svg { width: 15px; height: 15px; flex: none; }
  .fr-tag.is-success { background: var(--green-tint); color: var(--green-text); border-color: var(--green-border); }
  .fr-tag.is-brand { background: var(--primary-light); color: var(--brand-400); }
  .fr-tag.is-muted { background: var(--surface-alt); color: var(--text-caption); border-color: var(--border); }
  @media (max-width: 720px) {
    .fr-card { flex-wrap: wrap; padding: var(--space-16); }
    .fr-body { flex-basis: calc(100% - 56px - var(--space-16)); }
    .fr-tags { flex-basis: 100%; margin-top: var(--space-8); }
  }
</style>
<?php endif; ?>

<section class="fr-section<?php echo $fr['shade'] ? ' is-shaded' : ''; ?>">
  <div class="fr-wrap">
    <?php if ( $fr['eyebrow'] || $fr['title'] || $fr['lead'] ) : ?>
    <div class="fr-head">
      <?php if ( $fr['eyebrow'] ) : ?><p class="fr-eyebrow"><?php echo esc_html( $fr['eyebrow'] ); ?></p><?php endif; ?>
      <?php if ( $fr['title'] ) : ?><h2 class="fr-h2"><?php echo wp_kses_post( $fr['title'] ); ?></h2><?php endif; ?>
      <?php if ( $fr['lead'] ) : ?><p class="fr-lead"><?php echo wp_kses_post( $fr['lead'] ); ?></p><?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="fr-list">
      <?php foreach ( $fr['items'] as $fr_it ) : $fr_it = wp_parse_args( (array) $fr_it, array( 'icon' => '', 'title' => '', 'text' => '', 'tags' => array() ) ); ?>
      <div class="fr-card">
        <?php if ( $fr_it['icon'] ) : ?><div class="fr-icon" aria-hidden="true"><?php echo $fr_it['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted inline SVG ?></div><?php endif; ?>
        <div class="fr-body">
          <?php if ( $fr_it['title'] ) : ?><h3 class="fr-title"><?php echo wp_kses_post( $fr_it['title'] ); ?></h3><?php endif; ?>
          <?php if ( $fr_it['text'] ) : ?><p class="fr-text"><?php echo wp_kses_post( $fr_it['text'] ); ?></p><?php endif; ?>
        </div>
        <?php if ( ! empty( $fr_it['tags'] ) && is_array( $fr_it['tags'] ) ) : ?>
        <div class="fr-tags">
          <?php
          foreach ( $fr_it['tags'] as $fr_tag ) :
				$fr_tag  = wp_parse_args( (array) $fr_tag, array( 'label' => '', 'variant' => 'brand', 'icon' => '' ) );
				$fr_vari = in_array( $fr_tag['variant'], array( 'success', 'brand', 'muted' ), true ) ? $fr_tag['variant'] : 'brand';
				?>
          <span class="fr-tag is-<?php echo esc_attr( $fr_vari ); ?>"><?php echo $fr_tag['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted inline SVG ?><?php echo esc_html( $fr_tag['label'] ); ?></span>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
