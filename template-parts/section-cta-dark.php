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
	)
);
$u = get_template_directory_uri();

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
	    padding: clamp(48px,6vw,88px) clamp(28px,5vw,64px); text-align: center; overflow: hidden;
	    /* brand .shs-dark treatment: #002398 tinted over start-free.png (never near-black) */
	    background-color: var(--primary-dark);
	    background-image: linear-gradient(rgba(0,35,152,.85), rgba(0,35,152,.85)), url('<?php echo $u; ?>/images/start-free.png');
	    background-size: cover; background-position: center; background-repeat: no-repeat;
	  }
	  .cta-dark .cta-dark-eyebrow { font-family: var(--font-mono); font-weight: 500; font-size: var(--fs-eyebrow); letter-spacing: var(--tracking-wider); text-transform: uppercase; color: var(--accent-on-dark); }
	  .cta-dark h2 { font-family: var(--font-serif); font-weight: 500; font-size: var(--fs-cta); line-height: var(--lh-tight); letter-spacing: var(--tracking-tight); color: var(--white); margin: 12px 0 0; }
	  .cta-dark p { font-size: var(--fs-md); line-height: var(--lh-relaxed); color: var(--text-on-dark); margin: 14px auto 0; }
	  .cta-dark-btns { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; margin-top: 28px; }
	  .cta-dark-btn { display: inline-flex; font-family: var(--font-body); font-size: var(--fs-md); font-weight: var(--fw-semibold); padding: 14px 28px; border-radius: var(--radius-btn); text-decoration: none; border: 2px solid transparent; cursor: pointer; transition: background .2s ease, transform .2s ease; }
	  .cta-dark-btn:hover { transform: translateY(-2px); }
	  .cta-dark-btn.light { background: var(--white); color: var(--ink); }
	  .cta-dark-btn.light:hover { background: #eef2f9; }
	  .cta-dark-btn.ghost { background: rgba(255,255,255,.12); color: var(--white); border-color: rgba(255,255,255,.3); }
	  .cta-dark-btn.ghost:hover { background: rgba(255,255,255,.2); }
	</style>
	<?php
endif;
?>
<section class="cta-dark<?php echo $cta['wide'] ? ' is-wide' : ''; ?>"<?php echo $cta['id'] ? ' id="' . esc_attr( $cta['id'] ) . '"' : ''; ?>>
  <div class="cta-dark-inner">
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
        <a href="<?php echo esc_url( $b['url'] ); ?>" class="cta-dark-btn <?php echo esc_attr( $style ); ?>"><?php echo esc_html( $b['label'] ); ?></a>
        <?php endif;
      endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>
