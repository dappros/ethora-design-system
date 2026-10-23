<?php
/**
 * Reusable "honest short answer" block for comparison / Alternative pages (AEO).
 *
 * A compact, quotable summary placed right under the hero: a definitional paragraph or two,
 * a two-column "Stay with X if / Choose Ethora if" list, and a one-line "Best for" verdict.
 * Emits a WebPage `speakable` JSON-LD pointing at the summary + verdict so assistants and
 * voice surfaces can lift them. Self-contained (CSS once per request), tokens only.
 *
 *   get_template_part( 'template-parts/section-short-answer', null, array(
 *     'eyebrow'    => 'The honest short answer',
 *     'title'      => 'Ethora vs Stream in 30 seconds',
 *     'summary'    => array( 'Paragraph one.', 'Paragraph two.' ),   // inline HTML allowed
 *     'stay_title' => 'Stay with Stream if',
 *     'stay'       => array( '…', '…' ),
 *     'choose_title' => 'Choose Ethora if',
 *     'choose'     => array( '…', '…' ),
 *     'verdict'    => 'Best for: …',                                   // inline HTML allowed
 *     'links'      => array( array( 'label' => 'See all comparisons', 'url' => '/alternatives/' ) ),
 *   ) );
 *
 * @package ethora-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sa = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'eyebrow'      => 'The honest short answer',
		'title'        => '',
		'summary'      => array(),
		'stay_title'   => '',
		'stay'         => array(),
		'choose_title' => 'Choose Ethora if',
		'choose'       => array(),
		'verdict'      => '',
		'links'        => array(),
	)
);

if ( empty( $sa['summary'] ) ) {
	return;
}

static $shs_sa_css_done = false;
if ( ! $shs_sa_css_done ) :
	$shs_sa_css_done = true;
?>
<style>
  .shs-sa { padding: var(--section-y-sm) 0; background: var(--white); }
  .shs-sa-box { max-width: var(--content-max); margin: 0 auto; padding: 0 var(--space-16); }
  .shs-sa-card { border: 1px solid var(--border); border-left: 4px solid var(--primary); border-radius: var(--radius-2xl); background: var(--white); padding: var(--space-32); box-shadow: 0 8px 30px rgba(0, 82, 205, 0.06); }
  .shs-sa-eyebrow { font-family: var(--font-mono, monospace); font-size: var(--fs-xs); letter-spacing: .12em; text-transform: uppercase; color: var(--primary); margin: 0 0 var(--space-8); }
  .shs-sa h2 { font-size: var(--fs-2xl); line-height: var(--lh-tight); margin: 0 0 var(--space-16); color: var(--text-dark); }
  .shs-sa-summary p { font-size: var(--fs-lg); line-height: var(--lh-relaxed); color: var(--text-body); margin: 0 0 var(--space-8); }
  .shs-sa-cols { display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-16); margin: var(--space-16) 0; }
  .shs-sa-col h3 { font-size: var(--fs-md); margin: 0 0 var(--space-8); color: var(--text-dark); }
  .shs-sa-col ul { list-style: none; margin: 0; padding: 0; display: grid; gap: var(--space-8); }
  .shs-sa-col li { display: flex; gap: var(--space-8); align-items: flex-start; font-size: var(--fs-md); line-height: var(--lh-snug); color: var(--text-body); }
  .shs-sa-col li svg { flex: 0 0 auto; width: 18px; height: 18px; margin-top: 2px; }
  .shs-sa-col.is-stay li svg { color: var(--text-muted); }
  .shs-sa-col.is-choose li svg { color: var(--green); }
  .shs-sa-verdict { font-size: var(--fs-md); line-height: var(--lh-snug); color: var(--text-dark); border-top: 1px solid var(--border); padding-top: var(--space-16); margin: 0; }
  .shs-sa-links { margin: var(--space-8) 0 0; font-size: var(--fs-sm); }
  .shs-sa-links a { color: var(--primary); text-decoration: underline; margin-right: var(--space-16); }
  @media (max-width: 720px) { .shs-sa-cols { grid-template-columns: 1fr; } .shs-sa-card { padding: var(--space-16); } }
</style>
<?php endif; ?>

<section class="shs-sa" aria-label="<?php echo esc_attr( wp_strip_all_tags( $sa['eyebrow'] ) ); ?>">
  <div class="shs-sa-box">
    <div class="shs-sa-card">
      <?php if ( $sa['eyebrow'] ) : ?><p class="shs-sa-eyebrow"><?php echo wp_kses_post( $sa['eyebrow'] ); ?></p><?php endif; ?>
      <?php if ( $sa['title'] ) : ?><h2><?php echo wp_kses_post( $sa['title'] ); ?></h2><?php endif; ?>
      <div class="shs-sa-summary">
        <?php foreach ( (array) $sa['summary'] as $sa_p ) : ?><p><?php echo wp_kses_post( $sa_p ); ?></p><?php endforeach; ?>
      </div>
      <?php if ( ! empty( $sa['stay'] ) || ! empty( $sa['choose'] ) ) : ?>
      <div class="shs-sa-cols">
        <?php if ( ! empty( $sa['stay'] ) ) : ?>
        <div class="shs-sa-col is-stay">
          <h3><?php echo wp_kses_post( $sa['stay_title'] ); ?></h3>
          <ul>
            <?php foreach ( (array) $sa['stay'] as $sa_i ) : ?>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 8v4l2.5 2.5"/></svg><span><?php echo wp_kses_post( $sa_i ); ?></span></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>
        <?php if ( ! empty( $sa['choose'] ) ) : ?>
        <div class="shs-sa-col is-choose">
          <h3><?php echo wp_kses_post( $sa['choose_title'] ); ?></h3>
          <ul>
            <?php foreach ( (array) $sa['choose'] as $sa_i ) : ?>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg><span><?php echo wp_kses_post( $sa_i ); ?></span></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>
      </div>
      <?php endif; ?>
      <?php if ( $sa['verdict'] ) : ?><p class="shs-sa-verdict"><?php echo wp_kses_post( $sa['verdict'] ); ?></p><?php endif; ?>
      <?php if ( ! empty( $sa['links'] ) ) : ?>
      <p class="shs-sa-links">
        <?php foreach ( (array) $sa['links'] as $sa_l ) : ?><a href="<?php echo esc_url( $sa_l['url'] ); ?>"><?php echo esc_html( $sa_l['label'] ); ?></a><?php endforeach; ?>
      </p>
      <?php endif; ?>
    </div>
  </div>
</section>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "speakable": { "@type": "SpeakableSpecification", "cssSelector": [ ".shs-sa-summary", ".shs-sa-verdict" ] }
}
</script>
