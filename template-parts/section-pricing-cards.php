<?php
/**
 * Reusable pricing cards — "Variant A" (3 cards, middle highlighted, Monthly/Yearly toggle).
 * Editorial style, fully tokenised (css/tokens.css). Used by the Pricing page AND as the
 * shared pricing section on other pages (each wraps it with its own heading/context).
 *
 * Params ($args):
 *   'eyebrow'     => 'Pricing'
 *   'heading'     => 'Save Months of Work with Ethora'
 *   'subheading'  => 'Build any chat use case into your product — in hours.'
 *   'show_header' => true   // set false when the page already has a hero heading
 *   'bg'          => 'alt'  // 'alt' (#F4F7FD) | 'white' | 'none'
 *
 * Plan data lives in the $pp_plans array below — edit prices in one place.
 */

$pp = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'eyebrow'     => 'Pricing',
		'heading'     => 'Save Months of Work with Ethora',
		'subheading'  => 'Build any chat use case into your product — in hours.',
		'show_header' => true,
		'bg'          => 'alt',
	)
);
$u = get_template_directory_uri();

$pp_plans = array(
	array(
		'name' => 'Free', 'svg' => '<svg viewBox="0 0 24 24" width="22" height="22"><circle cx="12" cy="12" r="3.2"/><path d="M12 2.5v3.5M12 18v3.5M2.5 12H6M18 12h3.5"/></svg>', 'badge' => '',
		'monthly' => '$0', 'yearly' => '$0', 'per' => '/ mo',
		'sub_m' => 'free forever', 'sub_y' => 'free forever',
		'mau' => '1,000 MAU', 'conn' => '100 Concurrent Connections',
		'btn' => 'Sign Up', 'btn_url' => 'https://app.chat.ethora.com/register', 'btn_style' => 'outline', 'btn_modal' => false,
		'feat_title' => 'All Chat & AI features, including:',
		'feats' => array( 'Community Support', '30 Days of Free Support', 'No Credit Card Required' ),
		'popular' => false,
	),
	array(
		'name' => 'Small Business', 'svg' => '<svg viewBox="0 0 24 24" width="22" height="22"><line x1="4" y1="8" x2="20" y2="8"/><line x1="4" y1="16" x2="20" y2="16"/><circle cx="9" cy="8" r="2.4"/><circle cx="15" cy="16" r="2.4"/></svg>', 'badge' => 'Most Popular',
		'monthly' => '$99', 'yearly' => '$84', 'per' => '/ mo',
		'sub_m' => 'billed monthly', 'sub_y' => 'billed annually',
		'mau' => '5,000 MAU', 'conn' => '250 Concurrent Connections',
		'btn' => 'Free Trial', 'btn_url' => 'https://app.chat.ethora.com/register?business=true', 'btn_style' => 'solid', 'btn_modal' => false,
		'feat_title' => 'All Free, plus:',
		'feats' => array( 'Tech Support', 'SLA 99.9%', 'AI Allowance', 'Custom Domain' ),
		'popular' => true,
	),
	array(
		'name' => 'Enterprise', 'svg' => '<svg viewBox="0 0 24 24" width="22" height="22"><path d="M5 19V11M12 19V5M19 19V8"/></svg>', 'badge' => 'Recommended',
		'monthly' => '$599+', 'yearly' => '$509+', 'per' => '/ mo',
		'sub_m' => 'billed monthly', 'sub_y' => 'billed annually',
		'mau' => 'Unlimited MAU', 'conn' => 'Unlimited Connections',
		'btn' => 'Book a Call', 'btn_url' => '/contact', 'btn_style' => 'outline', 'btn_modal' => true,
		'feat_title' => 'All Business, plus:',
		'feats' => array( '24/7 Phone Support', 'Self-hosted AI', 'Custom Configuration', 'Dedicated / On-prem hosting' ),
		'popular' => false,
	),
);

// Print CSS + editorial fonts once per request.
static $pp_assets = false;
if ( ! $pp_assets ) :
	$pp_assets = true;
	?>
	<style>
	  .ppc { padding: var(--section-y) var(--section-x); font-family: var(--font-sans); }
	  .ppc.bg-alt { background: var(--surface-alt); }
	  .ppc.bg-white { background: var(--white); }
	  .ppc-wrap { max-width: var(--content-max); margin: 0 auto; }
	  .ppc-head { text-align: center; max-width: var(--container-sm); margin: 0 auto; }
	  .ppc-eyebrow { font-family: var(--font-mono); font-weight: 500; font-size: var(--fs-eyebrow); letter-spacing: var(--tracking-wider); text-transform: uppercase; color: var(--primary); }
	  .ppc-head h2 { font-family: var(--font-serif); font-weight: 500; font-size: var(--fs-h2); line-height: var(--lh-tight); letter-spacing: var(--tracking-tight); color: var(--ink); margin: 12px 0 0; }
	  .ppc-head p { font-size: var(--fs-lg); line-height: var(--lh-relaxed); color: var(--text-body); margin: 12px 0 0; }
	  /* toggle */
	  .ppc-toggle { display: inline-flex; align-items: center; gap: 4px; margin: 28px auto 0; background: var(--white); border: 1px solid var(--border); border-radius: var(--radius-pill); padding: 5px; }
	  .ppc-wrap > .ppc-toggle { display: flex; width: max-content; }
	  .ppc-tg { font-family: var(--font-sans); font-size: var(--fs-sm); font-weight: var(--fw-semibold); color: var(--text-body); background: none; border: 0; cursor: pointer; padding: 9px 20px; border-radius: var(--radius-pill); display: inline-flex; align-items: center; gap: 8px; transition: background .2s ease, color .2s ease; }
	  .ppc-tg.active { background: var(--ink); color: var(--white); }
	  .ppc-off { font-size: 11px; font-weight: var(--fw-bold); color: var(--primary); }
	  .ppc-tg.active .ppc-off { color: var(--accent-on-dark); }
	  /* grid */
	  .ppc-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 44px; align-items: start; }
	  .ppc-card { position: relative; background: var(--white); border: 1px solid var(--border); border-radius: var(--radius-xl); padding: 30px; display: flex; flex-direction: column; }
	  .ppc-card.is-popular { border: 1.5px solid var(--primary); box-shadow: 0 30px 60px -34px rgba(31, 106, 255, .55); transform: translateY(-8px); }
	  .ppc-badge { position: absolute; top: -12px; left: 30px; font-family: var(--font-sans); font-size: 11px; font-weight: var(--fw-bold); letter-spacing: .04em; text-transform: uppercase; color: var(--white); background: var(--primary); padding: 5px 12px; border-radius: var(--radius-pill); }
	  .ppc-badge.ghost { left: auto; right: 30px; color: var(--primary); background: var(--tint); }
	  .ppc-card-head { display: flex; align-items: center; gap: 12px; }
	  .ppc-icon { flex: none; width: 44px; height: 44px; border-radius: var(--radius-md); background: var(--tint); display: flex; align-items: center; justify-content: center; }
	  .ppc-icon svg { width: 22px; height: 22px; stroke: var(--primary); fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }
	  .ppc-card-head h3 { font-family: var(--font-sans); font-size: var(--fs-xl); font-weight: var(--fw-bold); color: var(--ink); margin: 0; }
	  .ppc-price { margin: 20px 0 0; display: flex; align-items: baseline; gap: 4px; }
	  .ppc-amount { font-family: var(--font-serif); font-weight: 500; font-size: 40px; line-height: 1; letter-spacing: var(--tracking-tight); color: var(--ink); }
	  .ppc-per { font-size: var(--fs-md); color: var(--text-caption); }
	  .ppc-sub { font-size: var(--fs-sm); color: var(--text-caption); margin-top: 6px; }
	  .ppc-meta { margin-top: 16px; }
	  .ppc-mau { font-size: var(--fs-md); font-weight: var(--fw-semibold); color: var(--ink); }
	  .ppc-conn { font-size: var(--fs-sm); color: var(--text-caption); margin-top: 2px; }
	  .ppc-btn { display: block; text-align: center; margin-top: var(--space-16); font-family: var(--font-body); font-size: var(--fs-md); font-weight: var(--fw-semibold); padding: 12px; border-radius: var(--radius-btn); text-decoration: none; border: 2px solid transparent; cursor: pointer; transition: background .2s ease, color .2s ease, border-color .2s ease; }
	  .ppc-btn.solid { background: var(--primary); color: var(--white); }
	  .ppc-btn.solid:hover { background: var(--primary-hover); }
	  .ppc-btn.outline { background: var(--white); color: var(--primary); border-color: var(--primary); }
	  .ppc-btn.outline:hover { background: var(--primary-light); }
	  .ppc-divider { height: 1px; background: var(--border-hair); margin: 22px 0; }
	  .ppc-feat-title { font-size: var(--fs-sm); font-weight: var(--fw-semibold); color: var(--ink); margin-bottom: 14px; }
	  .ppc-feats { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 11px; }
	  .ppc-feats li { display: flex; gap: 10px; align-items: flex-start; font-size: var(--fs-sm); color: var(--text-body-soft); line-height: 1.45; }
	  .ppc-feats li svg { flex: none; margin-top: 1px; }
	  @media (max-width: 900px) {
	    .ppc-grid { grid-template-columns: 1fr; max-width: 440px; margin-left: auto; margin-right: auto; }
	    .ppc-card.is-popular { transform: none; }
	  }
	</style>
	<?php
endif;

$pp_check = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="' . '#0052cd' . '" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>';
$pp_bg = ( 'none' === $pp['bg'] ) ? '' : ' bg-' . $pp['bg'];
?>
<section class="ppc<?php echo $pp_bg; ?>" id="pricing">
  <div class="ppc-wrap">
    <?php if ( $pp['show_header'] ) : ?>
    <div class="ppc-head">
      <?php if ( $pp['eyebrow'] ) : ?><div class="ppc-eyebrow"><?php echo esc_html( $pp['eyebrow'] ); ?></div><?php endif; ?>
      <h2><?php echo esc_html( $pp['heading'] ); ?></h2>
      <?php if ( $pp['subheading'] ) : ?><p><?php echo esc_html( $pp['subheading'] ); ?></p><?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="ppc-toggle" role="group" aria-label="Billing period">
      <button type="button" class="ppc-tg active" data-bill="monthly">Monthly</button>
      <button type="button" class="ppc-tg" data-bill="yearly">Yearly <span class="ppc-off">15% OFF</span></button>
    </div>

    <div class="ppc-grid">
      <?php foreach ( $pp_plans as $p ) : ?>
      <div class="ppc-card<?php echo $p['popular'] ? ' is-popular' : ''; ?>">
        <?php if ( $p['badge'] ) : ?><span class="ppc-badge<?php echo $p['popular'] ? '' : ' ghost'; ?>"><?php echo esc_html( $p['badge'] ); ?></span><?php endif; ?>
        <div class="ppc-card-head">
          <span class="ppc-icon"><?php echo $p['svg']; ?></span>
          <h3><?php echo esc_html( $p['name'] ); ?></h3>
        </div>
        <div class="ppc-price">
          <span class="ppc-amount" data-monthly="<?php echo esc_attr( $p['monthly'] ); ?>" data-yearly="<?php echo esc_attr( $p['yearly'] ); ?>"><?php echo esc_html( $p['monthly'] ); ?></span>
          <span class="ppc-per"><?php echo esc_html( $p['per'] ); ?></span>
        </div>
        <div class="ppc-sub" data-monthly="<?php echo esc_attr( $p['sub_m'] ); ?>" data-yearly="<?php echo esc_attr( $p['sub_y'] ); ?>"><?php echo esc_html( $p['sub_m'] ); ?></div>
        <div class="ppc-meta">
          <div class="ppc-mau"><?php echo esc_html( $p['mau'] ); ?></div>
          <div class="ppc-conn"><?php echo esc_html( $p['conn'] ); ?></div>
        </div>
        <?php if ( $p['btn_modal'] ) : ?>
        <button class="book-demo-button ppc-btn <?php echo esc_attr( $p['btn_style'] ); ?>"><?php echo esc_html( $p['btn'] ); ?></button>
        <?php else : ?>
        <a href="<?php echo esc_url( $p['btn_url'] ); ?>" class="ppc-btn <?php echo esc_attr( $p['btn_style'] ); ?>"><?php echo esc_html( $p['btn'] ); ?></a>
        <?php endif; ?>
        <div class="ppc-divider"></div>
        <div class="ppc-feat-title"><?php echo esc_html( $p['feat_title'] ); ?></div>
        <ul class="ppc-feats">
          <?php foreach ( $p['feats'] as $feat ) : ?>
          <li><?php echo $pp_check; ?><span><?php echo esc_html( $feat ); ?></span></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<script>
  (function () {
    var sec = document.currentScript.previousElementSibling;
    if ( ! sec || ! sec.classList.contains('ppc') ) { sec = document.querySelector('.ppc'); }
    if ( ! sec ) { return; }
    var tgs = sec.querySelectorAll('.ppc-tg');
    function setBill( mode ) {
      tgs.forEach(function ( t ) { t.classList.toggle('active', t.getAttribute('data-bill') === mode); });
      sec.querySelectorAll('.ppc-amount, .ppc-sub').forEach(function ( el ) {
        var v = el.getAttribute('data-' + mode);
        if ( v !== null ) { el.textContent = v; }
      });
    }
    tgs.forEach(function ( t ) {
      t.addEventListener('click', function () { setBill( t.getAttribute('data-bill') ); });
    });
  })();
</script>
