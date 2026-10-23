<?php
/**
 * Reusable "migration steps" block for comparison / Alternative pages (MAR-314).
 *
 * A short lead plus a numbered list of steps (big numeral, title, text) in a two-column
 * grid, closing with an optional CTA row. Self-contained (CSS once per request), tokens only.
 *
 *   get_template_part( 'template-parts/section-migration-steps', null, array(
 *     'eyebrow' => 'Migration',
 *     'title'   => 'Migrating from Stream to Ethora',
 *     'lead'    => 'Switching sounds heavy. It does not have to be.',
 *     'steps'   => array( array( 'title' => 'Run them side by side', 'text' => '…' ), … ),
 *     'buttons' => array( array( 'label' => 'Book a Call', 'style' => 'primary', 'modal' => true ), array( 'label' => '…', 'url' => '/…' ) ),
 *   ) );
 *
 * @package ethora-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ms = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'eyebrow' => 'Migration',
		'title'   => '',
		'lead'    => '',
		'steps'   => array(),
		'buttons' => array(),
	)
);

if ( empty( $ms['steps'] ) ) {
	return;
}

static $shs_ms_css_done = false;
if ( ! $shs_ms_css_done ) :
	$shs_ms_css_done = true;
?>
<style>
  .shs-ms { padding: var(--section-y-sm) 0; background: var(--white); }
  .shs-ms-box { max-width: var(--content-max); margin: 0 auto; padding: 0 var(--space-16); }
  .shs-ms-eyebrow { font-family: var(--font-mono, monospace); font-size: var(--fs-xs); letter-spacing: .12em; text-transform: uppercase; color: var(--primary); margin: 0 0 var(--space-8); }
  .shs-ms h2 { font-size: var(--fs-2xl); line-height: var(--lh-tight); margin: 0 0 var(--space-8); color: var(--text-dark); max-width: var(--measure); }
  .shs-ms-lead { font-size: var(--fs-lg); line-height: var(--lh-relaxed); color: var(--text-body); margin: 0 0 var(--space-32); max-width: var(--measure); }
  .shs-ms-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: var(--space-16); margin: 0; padding: 0; list-style: none; counter-reset: shs-ms; }
  .shs-ms-grid li { counter-increment: shs-ms; display: grid; grid-template-columns: 48px 1fr; gap: var(--space-16); align-items: start; border: 1px solid var(--border); border-radius: var(--radius-2xl); padding: var(--space-16); background: var(--white); }
  .shs-ms-grid li::before { content: counter(shs-ms, decimal-leading-zero); font-family: var(--font-mono, monospace); font-size: var(--fs-lg); font-weight: 700; color: var(--primary); background: rgba(0, 82, 205, 0.08); border-radius: 12px; width: 48px; height: 48px; display: grid; place-items: center; }
  .shs-ms-grid h3 { font-size: var(--fs-md); margin: 0 0 var(--space-4); color: var(--text-dark); }
  .shs-ms-grid p { font-size: var(--fs-md); line-height: var(--lh-snug); color: var(--text-body); margin: 0; }
  .shs-ms-cta { display: flex; flex-wrap: wrap; gap: var(--space-8); margin-top: var(--space-32); }
</style>
<?php endif; ?>

<section class="shs-ms" aria-label="<?php echo esc_attr( wp_strip_all_tags( $ms['title'] ) ); ?>">
  <div class="shs-ms-box">
    <?php if ( $ms['eyebrow'] ) : ?><p class="shs-ms-eyebrow"><?php echo wp_kses_post( $ms['eyebrow'] ); ?></p><?php endif; ?>
    <?php if ( $ms['title'] ) : ?><h2><?php echo wp_kses_post( $ms['title'] ); ?></h2><?php endif; ?>
    <?php if ( $ms['lead'] ) : ?><p class="shs-ms-lead"><?php echo wp_kses_post( $ms['lead'] ); ?></p><?php endif; ?>
    <ol class="shs-ms-grid">
      <?php foreach ( (array) $ms['steps'] as $ms_s ) : $ms_s = wp_parse_args( (array) $ms_s, array( 'title' => '', 'text' => '' ) ); ?>
      <li><div><h3><?php echo wp_kses_post( $ms_s['title'] ); ?></h3><p><?php echo wp_kses_post( $ms_s['text'] ); ?></p></div></li>
      <?php endforeach; ?>
    </ol>
    <?php if ( ! empty( $ms['buttons'] ) ) : ?>
    <div class="shs-ms-cta">
      <?php foreach ( (array) $ms['buttons'] as $ms_b ) : $ms_b = wp_parse_args( (array) $ms_b, array( 'label' => '', 'url' => '#book-a-call', 'style' => 'primary', 'modal' => false ) ); ?>
      <a class="btn <?php echo 'outline' === $ms_b['style'] ? 'btn-outline' : 'btn-primary'; ?><?php echo $ms_b['modal'] ? ' book-demo-button' : ''; ?>" href="<?php echo esc_url( $ms_b['url'] ); ?>"><?php echo esc_html( $ms_b['label'] ); ?></a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>
