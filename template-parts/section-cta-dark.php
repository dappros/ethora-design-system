<?php
/**
 * Reusable DARK CTA / "Book a Call" panel.
 *
 * RULE: every dark panel of this kind must use the brand ".shs-dark" colour treatment —
 * brand-deep #002398 (var(--primary-dark)) tinted over images/start-free.png — NOT a
 * near-black fill. This partial encapsulates that treatment so it's consistent everywhere.
 *
 * Usage:
 *   get_template_part( 'template-parts/section', 'cta-dark', array(
 *     'eyebrow' => 'Book a call',
 *     'heading' => 'Save months and launch faster',
 *     'text'    => '…',
 *     'id'      => 'book-a-call',
 *     'buttons' => array(
 *       array( 'label' => 'Start Free Trial', 'url' => 'https://app.chat.ethora.com/register', 'style' => 'light' ),
 *       array( 'label' => 'Book a Call', 'url' => 'https://ethora.com/contact', 'style' => 'ghost' ),
 *       // 'modal' => true  → renders a .book-demo-button instead of a link
 *     ),
 *   ) );
 */

$cta = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'eyebrow' => 'Book a call',
		'heading' => 'Save months and launch faster',
		'text'    => '',
		'id'      => '',
		'buttons' => array(),
		'wide'    => false, // true → full-bleed panel (spans the section width, only the --section-x gutter), not capped at --content-max
		'image'   => '',    // override the .shs-dark background image (theme-relative/URL); default = start-free.png (from CSS)
		'fade'    => false, // true → the self-hosted .shs-dark gradient (rgba .85 → .5) so the image texture shows; default = flat .85
		'trust'   => array(), // green-check items shown under the buttons (strings)
	)
);
$u = get_template_directory_uri();

// Optional per-instance background override — mirrors the self-hosted page's .shs-dark treatment.
$cta_bg = '';
if ( $cta['image'] ) {
	$cta_img = preg_match( '#^(https?:)?//#', $cta['image'] ) ? $cta['image'] : $u . '/' . ltrim( $cta['image'], '/' );
	$cta_bot = $cta['fade'] ? 'rgba(0,35,152,.5)' : 'rgba(0,35,152,.85)';
	$cta_bg  = ' style="background-image: linear-gradient(rgba(0,35,152,.85), ' . $cta_bot . "), url('" . esc_url( $cta_img ) . "');\"";
}

// Print the shared dark-CTA CSS once per request.
static $cta_dark_assets = false;
if ( ! $cta_dark_assets ) :
	$cta_dark_assets = true;
	?>
	<style>
	  .cta-dark { padding: 0 var(--section-x) var(--section-y); font-family: var(--font-sans); }
	  .cta-dark.is-wide .cta-dark-inner { max-width: none; }
	  .cta-dark-inner {
	    max-width: var(--content-max); margin: 0 auto; border-radius: var(--radius-3xl);
	    padding: clamp(var(--space-48),6vw,var(--space-96)) clamp(var(--space-32),5vw,var(--space-64)); text-align: center; overflow: hidden;
	    /* brand .shs-dark treatment: #002398 tinted over start-free.png (never near-black) */
	    background-color: var(--gradient-brand);
	    background-image: var(--gradient-brand);
	    background-size: cover; background-position: center; background-repeat: no-repeat;
	  }
	  .cta-dark .cta-dark-eyebrow { font-family: var(--font-mono); font-weight: var(--fw-medium); font-size: var(--fs-eyebrow); letter-spacing: var(--tracking-wider); text-transform: uppercase; color: var(--accent-on-dark); }
	  .cta-dark h2 { font-family: var(--font-serif); font-weight: var(--fw-medium); font-size: var(--fs-cta); line-height: var(--lh-tight); letter-spacing: var(--tracking-tight); color: var(--white); }
	  .cta-dark p { font-size: var(--fs-md); line-height: var(--lh-relaxed); color: var(--text-on-dark); margin: var(--space-16) auto 0; max-width: var(--measure); }
	  .cta-dark-btns { display: flex; gap: var(--space-16); justify-content: center; flex-wrap: wrap; margin-top: var(--space-32); }
	  .cta-dark-btn { display: inline-flex; font-family: var(--font-body); font-size: var(--fs-md); font-weight: var(--fw-semibold); padding: var(--space-16) var(--space-32); border-radius: var(--radius-btn); text-decoration: none; border: 2px solid transparent; cursor: pointer; transition: background .2s ease, transform .2s ease; }
	  .cta-dark-btn:hover { transform: translateY(-2px); }
	  .cta-dark-btn.light { background: var(--white); color: var(--ink); }
	  .cta-dark-btn.light:hover { background: #eef2f9; }
	  .cta-dark-btn.ghost { background: rgba(255,255,255,.12); color: var(--white); border-color: rgba(255,255,255,.3); }
	  .cta-dark-btn.ghost:hover { background: rgba(255,255,255,.2); }
	  .cta-dark-trust { display: flex; flex-wrap: wrap; align-items: center; justify-content: center; gap: var(--space-16) var(--space-32); margin-top: var(--space-32); }
	  .cta-dark-trust span { display: inline-flex; align-items: center; gap: var(--space-8); font-size: var(--fs-sm); color: var(--text-on-dark); }
	  .cta-dark-trust svg { width: 15px; height: 15px; flex: none; color: var(--green); }
	</style>
	<?php
endif;
?>
<section class="cta-dark<?php echo $cta['wide'] ? ' is-wide' : ''; ?>"<?php echo $cta['id'] ? ' id="' . esc_attr( $cta['id'] ) . '"' : ''; ?>>
  <div class="cta-dark-inner"<?php echo $cta_bg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- URL escaped above ?>>
    <?php if ( $cta['eyebrow'] ) : ?><div class="cta-dark-eyebrow"><?php echo esc_html( $cta['eyebrow'] ); ?></div><?php endif; ?>
    <?php if ( $cta['heading'] ) : ?><h2><?php echo esc_html( $cta['heading'] ); ?></h2><?php endif; ?>
    <?php if ( $cta['text'] ) : ?><p><?php echo wp_kses_post( $cta['text'] ); ?></p><?php endif; ?>
    <?php if ( ! empty( $cta['buttons'] ) ) : ?>
    <div class="cta-dark-btns">
      <?php foreach ( $cta['buttons'] as $b ) :
        $style = isset( $b['style'] ) ? $b['style'] : 'light';
        if ( ! empty( $b['modal'] ) ) : ?>
        <button class="book-demo-button cta-dark-btn <?php echo esc_attr( $style ); ?>"><?php echo esc_html( $b['label'] ); ?></button>
        <?php else : ?>
        <a href="<?php echo esc_url( $b['url'] ); ?>"<?php echo ! empty( $b['new_tab'] ) ? ' target="_blank" rel="noopener"' : ''; ?> class="cta-dark-btn <?php echo esc_attr( $style ); ?>"><?php echo esc_html( $b['label'] ); ?></a>
        <?php endif;
      endforeach; ?>
    </div>
    <?php endif; ?>
    <?php if ( ! empty( $cta['trust'] ) ) : ?>
    <div class="cta-dark-trust">
      <?php foreach ( $cta['trust'] as $t ) : ?>
      <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg><?php echo esc_html( $t ); ?></span>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>
