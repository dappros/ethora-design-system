<?php
/**
 * Reusable "Book a Call" modal (HubSpot form) with a configurable left panel.
 *
 * Keeps the site-wide hooks intact: id="demo-modal", trigger class .book-demo-button,
 * close button #closeModal — so the existing open/close JS works on any page. The
 * HubSpot embed script is lazy-loaded on first .book-demo-button click. Uses the
 * site's own fonts (no external font dependency) so it looks consistent everywhere.
 *
 * Usage:
 *   get_template_part( 'template-parts/section', 'book-call-modal', array(
 *     'eyebrow'    => 'Book a call',
 *     'title'      => 'Talk to our team about your project',
 *     'text'       => 'Tell us your use case and we will scope the right setup.',
 *     'bullets'    => array( 'Enterprise SLA', 'HIPAA, SOC 2, GDPR-ready', 'Deploy in hours' ),
 *     'image'      => 'hero-chat.svg',   // bare filename (theme /images/) or full URL; optional
 *     'image_alt'  => 'Ethora chat dashboard',
 *     'form_title' => 'Book a call',     // right-column heading (optional)
 *   ) );
 *
 * @param array $args
 */

// Render only once per request (one #demo-modal). A page that includes its own
// book-call-modal earlier (in the body) wins over the site-wide wp_footer default.
if ( ! empty( $GLOBALS['shs_book_modal_rendered'] ) ) {
	return;
}
$GLOBALS['shs_book_modal_rendered'] = true;

$u  = get_template_directory_uri();
$bc = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'eyebrow'    => 'Book a call',
		'title'      => 'Talk to our team',
		'text'       => '',
		'bullets'    => array(),
		'image'      => '',
		'image_alt'  => '',
		'form_title' => 'Book a call',
	)
);

// Resolve image: bare filename -> theme /images/, otherwise use as given.
$bc_img = '';
if ( ! empty( $bc['image'] ) ) {
	$bc_img = ( preg_match( '#^(https?:)?/#', $bc['image'] ) ) ? $bc['image'] : $u . '/images/' . ltrim( $bc['image'], '/' );
}
$has_aside = ( $bc['title'] || $bc['text'] || ! empty( $bc['bullets'] ) || $bc_img );
$check = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#9bc0ff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>';
?>
<style>
  /* ===== Reusable "Book a Call" modal (scoped to #demo-modal) ===== */
  #demo-modal.modal { align-items: center; overflow: hidden; padding: 20px; }
  #demo-modal .modal-content {
    max-width: 880px; width: 100%; max-height: 90vh; padding: 0; overflow: hidden;
    border-radius: 18px; display: flex; box-shadow: 0 30px 80px -24px rgba(8,20,45,.55);
  }
  #demo-modal .bcm-aside {
    flex: 0 0 320px; background: linear-gradient(165deg, #0052CD 0%, #022a73 100%);
    color: #fff; padding: 36px 30px; display: flex; flex-direction: column; gap: 22px; overflow-y: auto;
  }
  #demo-modal .bcm-aside .eb { font-size: 12px; font-weight: 600; letter-spacing: .12em; text-transform: uppercase; color: #9bc0ff; }
  #demo-modal .bcm-aside h3 { font-weight: 700; font-size: 24px; line-height: 1.2; margin: 8px 0 0; color: #fff; }
  #demo-modal .bcm-aside p { font-size: 14.5px; line-height: 1.6; color: #cdd8ee; margin: 10px 0 0; }
  #demo-modal .bcm-aside ul { list-style: none; margin: 4px 0 0; padding: 0; display: flex; flex-direction: column; gap: 12px; }
  #demo-modal .bcm-aside li { display: flex; gap: 10px; align-items: flex-start; font-size: 14.5px; line-height: 1.45; color: #e5edfb; }
  #demo-modal .bcm-aside li svg { flex: none; margin-top: 1px; }
  #demo-modal .bcm-illu { margin-top: auto; width: 100%; height: auto; border-radius: 12px; box-shadow: 0 18px 40px -20px rgba(0,0,0,.55); }
  #demo-modal .bcm-form { flex: 1 1 auto; min-width: 0; padding: 34px 30px; overflow-y: auto; max-height: 90vh; background: #fff; }
  #demo-modal .bcm-form .eb { font-size: 12px; font-weight: 600; letter-spacing: .12em; text-transform: uppercase; color: #0052CD; }
  #demo-modal .bcm-form h3 { font-weight: 700; font-size: 22px; color: #0E1A33; margin: 8px 0 18px; line-height: 1.2; }
  #demo-modal .modal-close { top: 14px; right: 16px; z-index: 5; line-height: 1; color: #8A93A6; }
  #demo-modal .modal-close:hover { color: #0E1A33; }
  @media (max-width: 760px) {
    #demo-modal .bcm-aside { display: none; }
    #demo-modal .modal-content { max-width: 460px; }
    #demo-modal .bcm-form { padding: 30px 22px; }
  }
</style>

<!-- Reusable Book a Call modal -->
<div id="demo-modal" class="modal hidden">
  <div class="modal-content">
    <span class="modal-close" id="closeModal">&times;</span>
    <?php if ( $has_aside ) : ?>
    <div class="bcm-aside">
      <div>
        <?php if ( $bc['eyebrow'] ) : ?><div class="eb"><?php echo esc_html( $bc['eyebrow'] ); ?></div><?php endif; ?>
        <?php if ( $bc['title'] ) : ?><h3><?php echo esc_html( $bc['title'] ); ?></h3><?php endif; ?>
        <?php if ( $bc['text'] ) : ?><p><?php echo wp_kses_post( $bc['text'] ); ?></p><?php endif; ?>
      </div>
      <?php if ( ! empty( $bc['bullets'] ) ) : ?>
      <ul>
        <?php foreach ( $bc['bullets'] as $b ) : ?>
        <li><?php echo $check; ?><span><?php echo esc_html( $b ); ?></span></li>
        <?php endforeach; ?>
      </ul>
      <?php endif; ?>
      <?php if ( $bc_img ) : ?><img class="bcm-illu" src="<?php echo esc_url( $bc_img ); ?>" alt="<?php echo esc_attr( $bc['image_alt'] ); ?>" loading="lazy" /><?php endif; ?>
    </div>
    <?php endif; ?>
    <div class="bcm-form">
      <div class="eb">Get in touch</div>
      <?php if ( $bc['form_title'] ) : ?><h3><?php echo esc_html( $bc['form_title'] ); ?></h3><?php endif; ?>
      <div id="hubspot-form-wrapper">
        <!-- HubSpot embed is lazy-loaded on first .book-demo-button click -->
        <div class="hs-form-frame"
             data-region="na1"
             data-form-id="86cdc2e8-2221-44b0-a926-02bec92c1bed"
             data-portal-id="4732608">
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  /* Lazy-load the HubSpot form embed on first "Book a Call" click (keeps it off the initial page load). */
  (function () {
    var loaded = false;
    function loadHubSpot() {
      if ( loaded ) { return; }
      loaded = true;
      var s = document.createElement('script');
      s.src = 'https://js.hsforms.net/forms/embed/4732608.js';
      s.defer = true;
      document.body.appendChild(s);
    }
    document.querySelectorAll('.book-demo-button').forEach(function ( btn ) {
      btn.addEventListener('click', loadHubSpot, { once: true });
    });
  })();
</script>
