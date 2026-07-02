<?php
/**
 * Reusable page HERO.
 *
 * Two-column hero on the brand diagonal gradient: eyebrow + h1 + lead + CTA
 * buttons + a trust row on the left, a product visual on the right, decorative
 * rhombus shapes in the corners, and an optional compliance strip below.
 * Clears the fixed header via --hero-pt. Self-contained (ships its own CSS once).
 *
 * Pass props via the 3rd arg of get_template_part():
 *
 *   get_template_part( 'template-parts/section-hero', null, array(
 *     'eyebrow'    => '',                          // optional mono kicker
 *     'title'      => 'Page H1',                   // <h1> (inline HTML allowed)
 *     'lead'       => 'Intro paragraph.',           // (inline HTML allowed)
 *     'buttons'    => array(                         // CTAs (optional)
 *       array( 'label' => 'Book a Call', 'style' => 'primary', 'modal' => true ),
 *       array( 'label' => 'Get started', 'style' => 'outline', 'url' => 'https://…', 'new_tab' => true, 'id' => 'accregred' ),
 *     ),
 *     'trust'      => array( '100% data ownership', 'Enterprise SLA' ), // green-check items (optional)
 *     'media'      => 'images/hero-chat.svg',        // theme-relative path or URL. .svg is inlined; raster → <img>
 *     'media_alt'  => '', 'media_width' => 0, 'media_height' => 0,      // for a raster media
 *     'media_html' => '',                            // raw markup, overrides 'media' (optional)
 *     'rhombus'     => true,                         // decorative corner shapes (optional, default true)
 *     'full_height' => true,                         // min-height 100vh, content vertically centred (optional, default true)
 *     'compliance' => array(                          // bottom strip (optional)
 *       'label' => 'Compliance, built in at every layer.',
 *       'items' => array( 'HIPAA', 'SOC 2', 'GDPR' ),
 *     ),
 *   ) );
 *
 * @package ethora-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hero = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'eyebrow'      => '',
		'title'        => '',
		'lead'         => '',
		'buttons'      => array(),
		'trust'        => array(),
		'media'        => '',
		'media_alt'    => '',
		'media_width'  => '',
		'media_height' => '',
		'media_html'   => '',
		'rhombus'      => true,
		'compliance'   => array(),
		'full_height'  => true,
	)
);

$hero_uri = get_template_directory_uri();

// Build the media (inline an SVG for smooth GPU compositing; otherwise an <img>).
$hero_media = '';
if ( $hero['media_html'] ) {
	$hero_media = $hero['media_html'];
} elseif ( $hero['media'] ) {
	$mp  = ltrim( $hero['media'], '/' );
	$abs = get_template_directory() . '/' . $mp;
	if ( preg_match( '/\.svg$/i', $mp ) && is_readable( $abs ) ) {
		$hero_media = file_get_contents( $abs ); // phpcs:ignore -- local theme asset, inlined on purpose
	} else {
		$src = preg_match( '#^(https?:)?//#', $hero['media'] ) ? $hero['media'] : $hero_uri . '/' . $mp;
		$dim = ( $hero['media_width'] ? ' width="' . esc_attr( $hero['media_width'] ) . '"' : '' )
			. ( $hero['media_height'] ? ' height="' . esc_attr( $hero['media_height'] ) . '"' : '' );
		$hero_media = '<img src="' . esc_url( $src ) . '"' . $dim . ' alt="' . esc_attr( $hero['media_alt'] ) . '" />';
	}
}

$hero_assets = empty( $GLOBALS['shs_hero_assets'] );
if ( $hero_assets ) {
	$GLOBALS['shs_hero_assets'] = true;
}
?>
<?php if ( $hero_assets ) : ?>
<style>
  /* HERO — brand diagonal gradient, 2-column (text + visual), decorative rhombus. Tokens only. */
  .ehero { position: relative; overflow: hidden; padding: var(--hero-pt) var(--section-x) clamp(48px,5vw,72px);
    background: linear-gradient(-124deg, rgba(255,255,255,1) 0%, rgba(0,82,205,.156) 71%, rgba(255,255,255,1) 100%); }
  .ehero, .ehero *, .ehero *::before, .ehero *::after { box-sizing: border-box; }
  /* full-viewport hero — content vertically centred below the fixed header */
  .ehero.is-full { min-height: 100vh; min-height: 100svh; display: flex; align-items: center; }
  .ehero-wrap { position: relative; z-index: 1; width: 100%; max-width: var(--content-max); margin: 0 auto; }
  .ehero-rh { position: absolute; right: 0; z-index: 0; pointer-events: none; height: auto; width: clamp(360px,46vw,760px); }
  .ehero-rh.is-top { top: 0; }
  .ehero-rh.is-bottom { bottom: 0; }
  @media (max-width: 768px) { .ehero-rh { width: 78vw; opacity: .7; } }
  .ehero-grid { display: grid; grid-template-columns: 0.92fr 1.08fr; gap: clamp(var(--space-32),4vw,var(--space-48)); align-items: center; }
  .ehero-text { min-width: 0; }
  .ehero-eyebrow { font-family: var(--font-mono); font-weight: var(--fw-medium); font-size: var(--fs-eyebrow); letter-spacing: var(--tracking-wide); text-transform: uppercase; color: var(--primary); margin: 0 0 var(--space-16); }
  .ehero-text h1 { font-family: var(--font-serif); font-weight: var(--fw-medium); font-size: var(--fs-h1); line-height: 1.08; letter-spacing: -.02em; color: var(--ink); margin: 0; }
  .ehero-lead { font-size: clamp(16px,1.5vw,19px); line-height: var(--lh-relaxed); color: var(--text-body); margin: var(--space-16) 0 0; max-width: 540px; }
  .ehero-btns { display: flex; gap: var(--space-16); flex-wrap: wrap; margin-top: var(--space-32); }
  /* brand CTA buttons (radius-btn, Open Sans 600) */
  .ehero-btn { display: inline-block; font-family: var(--font-body); font-size: var(--fs-md); font-weight: var(--fw-semibold);
    padding: 14px 28px; border-radius: var(--radius-btn); text-decoration: none; cursor: pointer; border: 2px solid transparent;
    transition: background .2s ease, color .2s ease, border-color .2s ease, transform .2s ease, box-shadow .2s ease; line-height: 1.1; }
  .ehero-btn--primary { background: var(--primary); color: #fff; }
  .ehero-btn--primary:hover { background: var(--primary-dark); transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,82,205,.2); }
  .ehero-btn--outline { background: transparent; color: var(--primary); border-color: var(--primary); }
  .ehero-btn--outline:hover { background: var(--primary-light); transform: translateY(-2px); }
  .ehero-btn--light { background: #fff; color: var(--ink); }
  .ehero-btn--light:hover { background: var(--tint); }
  .ehero-btn--ghost { background: rgba(255,255,255,.12); color: #fff; border-color: rgba(255,255,255,.3); }
  .ehero-btn--ghost:hover { background: rgba(255,255,255,.2); }
  /* trust-inline (green check items) */
  .ehero-trust { display: flex; flex-wrap: wrap; align-items: center; gap: var(--space-16) var(--space-32); margin-top: var(--space-16); }
  .ehero-trust .ti { display: inline-flex; align-items: center; gap: var(--space-8); font-size: var(--fs-sm); font-weight: var(--fw-semibold); color: var(--text-body); }
  .ehero-trust .ti svg { color: var(--green); flex: none; }
  /* product visual — inline SVG carries its own shadows; or a raster image */
  .ehero-media svg, .ehero-media img { display: block; width: 100%; height: auto; }
  .ehero-media svg { overflow: visible; }   /* let inline-SVG shadows/decorations bleed out */
  .ehero-media img { border-radius: var(--radius-2xl); overflow: hidden; }   /* clip raster corners to the radius */
  /* compliance strip */
  .ehero-compliance { display: flex; flex-wrap: wrap; align-items: center; gap: var(--space-16) var(--space-32); margin-top: clamp(40px,5vw,56px); padding-top: var(--space-32); border-top: 1px solid var(--hairline); }
  .ehero-compliance-label { font-family: var(--font-serif); font-style: italic; font-size: var(--fs-lg); color: var(--ink); }
  .ehero-compliance-items { display: flex; flex-wrap: wrap; gap: var(--space-16) var(--space-32); font-family: var(--font-mono); font-size: var(--fs-sm); font-weight: var(--fw-medium); color: var(--text-caption); margin-left: auto; }
  @media (max-width: 900px) {
    .ehero-grid { grid-template-columns: 1fr; gap: var(--space-32); }
    .ehero-media { order: 2; }
  }
</style>
<?php endif; ?>

<section class="ehero<?php echo $hero['full_height'] ? ' is-full' : ''; ?>">
  <?php if ( $hero['rhombus'] ) : ?>
  <img src="<?php echo esc_url( $hero_uri . '/images/rhombus.svg' ); ?>" alt="" aria-hidden="true" class="ehero-rh is-top" width="910" height="810" />
  <img src="<?php echo esc_url( $hero_uri . '/images/rhombus-2.svg' ); ?>" alt="" aria-hidden="true" class="ehero-rh is-bottom" width="910" height="810" />
  <?php endif; ?>
  <div class="ehero-wrap">
    <div class="ehero-grid">
      <div class="ehero-text">
        <?php if ( $hero['eyebrow'] ) : ?><p class="ehero-eyebrow"><?php echo esc_html( $hero['eyebrow'] ); ?></p><?php endif; ?>
        <?php if ( $hero['title'] ) : ?><h1><?php echo wp_kses_post( $hero['title'] ); ?></h1><?php endif; ?>
        <?php if ( $hero['lead'] ) : ?><p class="ehero-lead"><?php echo wp_kses_post( $hero['lead'] ); ?></p><?php endif; ?>

        <?php if ( ! empty( $hero['buttons'] ) ) : ?>
        <div class="ehero-btns">
          <?php
          foreach ( $hero['buttons'] as $b ) :
			$b      = wp_parse_args( (array) $b, array( 'label' => '', 'url' => '', 'style' => 'primary', 'modal' => false, 'new_tab' => false, 'id' => '' ) );
			$class  = 'ehero-btn ehero-btn--' . preg_replace( '/[^a-z]/', '', $b['style'] );
			$id     = $b['id'] ? ' id="' . esc_attr( $b['id'] ) . '"' : '';
			$target = $b['new_tab'] ? ' target="_blank" rel="noopener"' : '';
			if ( $b['modal'] ) :
				?>
          <button type="button"<?php echo $id; ?> class="book-demo-button <?php echo esc_attr( $class ); ?>"><?php echo esc_html( $b['label'] ); ?></button>
			<?php else : ?>
          <a href="<?php echo esc_url( $b['url'] ); ?>"<?php echo $id . $target; ?> class="<?php echo esc_attr( $class ); ?>"><?php echo esc_html( $b['label'] ); ?></a>
			<?php endif; ?>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if ( ! empty( $hero['trust'] ) ) : ?>
        <div class="ehero-trust">
          <?php foreach ( $hero['trust'] as $t ) : ?>
          <span class="ti"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg> <?php echo esc_html( $t ); ?></span>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

      <?php if ( $hero_media ) : ?>
      <div class="ehero-media"><?php echo $hero_media; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted inline SVG / pre-built img markup ?></div>
      <?php endif; ?>
    </div>

    <?php if ( ! empty( $hero['compliance']['items'] ) ) : ?>
    <div class="ehero-compliance">
      <?php if ( ! empty( $hero['compliance']['label'] ) ) : ?><span class="ehero-compliance-label"><?php echo esc_html( $hero['compliance']['label'] ); ?></span><?php endif; ?>
      <div class="ehero-compliance-items">
        <?php foreach ( $hero['compliance']['items'] as $c ) : ?><span><?php echo esc_html( $c ); ?></span><?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
</section>
