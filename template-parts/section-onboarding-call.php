<?php
/**
 * "An Onboarding Call, Not a Sales Pitch" section.
 *
 * Three steps (book, get instant access, build) beside a white card that sends
 * the visitor to the one-step booking page (/get-access/). Part of the
 * book-a-call-first experiment: see page-home-v2.php and page-get-access.php.
 * Self-contained (tokens only, style emitted once per request) so it can be
 * dropped onto any page with get_template_part().
 *
 * Usage:
 *   get_template_part( 'template-parts/section', 'onboarding-call', array(
 *     'booking_url' => home_url( '/get-access/' ),
 *     'signup_url'  => 'https://app.chat.ethora.com/register',
 *   ) );
 *
 * @param array $args  eyebrow, heading, lead, steps (array of [title, text]),
 *                     card_title, card_bullets, cta_label, booking_url,
 *                     signup_url, micro.
 */

$u  = get_template_directory_uri();
$oc = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'eyebrow'      => 'How it works',
		'heading'      => 'An Onboarding Call, Not a Sales Pitch',
		'lead'         => 'Free 15 minutes with the founder to get you up and running on Ethora.',
		'steps'        => array(
			array( 'title' => 'Book a call', 'text' => 'Pick a time for a 15 minute call with the Ethora CEO. One step, right on the next screen.' ),
			array( 'title' => 'Get instant access', 'text' => 'As soon as the call is booked you continue to signup and the admin panel. No waiting for the call.' ),
			array( 'title' => 'Build your first prototype', 'text' => 'On the call we walk through your use case and help you ship a first prototype for your stack.' ),
		),
		'card_title'   => 'Book your onboarding call',
		'card_bullets' => array( 'Full access for free', '15 minutes, not a pitch', 'No credit card required', 'Skip the call any time and sign up directly' ),
		'cta_label'    => 'Book a Call',
		'booking_url'  => home_url( '/get-access/' ),
		'signup_url'   => 'https://app.chat.ethora.com/register',
		'micro'        => 'Access is granted when you book, not when we talk.',
	)
);

$oc_icons = array(
	'<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><line x1="8" y1="3" x2="8" y2="7"/><line x1="16" y1="3" x2="16" y2="7"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
	'<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0"/></svg>',
	'<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 8 4 12 9 16"/><polyline points="15 8 20 12 15 16"/></svg>',
);
$oc_check = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>';

if ( empty( $GLOBALS['ethora_onboarding_call_assets'] ) ) :
	$GLOBALS['ethora_onboarding_call_assets'] = true;
?>
<style>
  .obc { background: var(--surface-alt); padding: var(--section-y) var(--section-x); font-family: var(--font-body); }
  .obc-inner { max-width: var(--content-max); margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-64); align-items: start; }
  .obc-eyebrow { display: inline-block; font-size: var(--fs-eyebrow); font-weight: var(--fw-semibold); letter-spacing: var(--tracking-wide); text-transform: uppercase; color: var(--primary); background: var(--tint); border-radius: var(--radius-pill); padding: var(--space-8) var(--space-16); margin: 0 0 var(--space-16); }
  .obc h2 { font-size: var(--fs-h2); line-height: var(--lh-heading); font-weight: var(--fw-bold); color: var(--ink); margin: 0 0 var(--space-16); text-wrap: balance; }
  .obc-lead { font-size: var(--fs-lg); line-height: var(--lh-relaxed); color: var(--text-body); margin: 0 0 var(--space-32); }
  .obc-steps { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: var(--space-16); }
  .obc-step { display: flex; gap: var(--space-16); align-items: flex-start; background: var(--white); border: 1px solid var(--border); border-radius: var(--radius-2xl); padding: var(--space-16); }
  .obc-ic { flex: none; width: 48px; height: 48px; border-radius: var(--radius-md); background: var(--tint); color: var(--primary); display: flex; align-items: center; justify-content: center; }
  .obc-step b { display: block; font-size: var(--fs-md); font-weight: var(--fw-semibold); color: var(--ink); margin: 0 0 var(--space-4); }
  .obc-step p { font-size: var(--fs-sm); line-height: var(--lh-base); color: var(--text-body-soft); margin: 0; }
  .obc-card { background: var(--white); border: 1px solid var(--border); border-radius: var(--radius-3xl); padding: var(--space-32); box-shadow: var(--shadow-card); }
  .obc-card h3 { font-size: var(--fs-2xl); font-weight: var(--fw-semibold); color: var(--ink); margin: 0 0 var(--space-16); }
  .obc-card ul { list-style: none; margin: 0 0 var(--space-32); padding: 0; display: flex; flex-direction: column; gap: var(--space-8); }
  .obc-card li { display: flex; gap: var(--space-8); align-items: flex-start; font-size: var(--fs-base); line-height: var(--lh-base); color: var(--text-body); }
  .obc-card li svg { flex: none; color: var(--green-text); margin-top: var(--space-4); }
  .obc-card .btn { width: 100%; justify-content: center; }
  .obc-micro { font-size: var(--fs-sm); color: var(--text-caption); text-align: center; margin: var(--space-16) 0 0; }
  .obc-alt { font-size: var(--fs-sm); color: var(--text-caption); text-align: center; margin: var(--space-8) 0 0; }
  .obc-alt a { color: var(--primary); text-decoration: underline; font-weight: var(--fw-semibold); }
  @media (max-width: 900px) {
    .obc-inner { grid-template-columns: 1fr; gap: var(--space-32); }
    .obc-card { padding: var(--space-16); }
  }
</style>
<?php endif; ?>

<section class="obc" id="onboarding-call" aria-labelledby="obc-heading">
  <div class="obc-inner">
    <div class="obc-text">
      <?php if ( $oc['eyebrow'] ) : ?><span class="obc-eyebrow"><?php echo esc_html( $oc['eyebrow'] ); ?></span><?php endif; ?>
      <h2 id="obc-heading"><?php echo esc_html( $oc['heading'] ); ?></h2>
      <?php if ( $oc['lead'] ) : ?><p class="obc-lead"><?php echo esc_html( $oc['lead'] ); ?></p><?php endif; ?>
      <ol class="obc-steps">
        <?php foreach ( array_values( $oc['steps'] ) as $i => $s ) : ?>
        <li class="obc-step">
          <span class="obc-ic"><?php echo isset( $oc_icons[ $i ] ) ? $oc_icons[ $i ] : $oc_icons[0]; ?></span>
          <div>
            <b><?php echo esc_html( ( $i + 1 ) . '. ' . $s['title'] ); ?></b>
            <p><?php echo esc_html( $s['text'] ); ?></p>
          </div>
        </li>
        <?php endforeach; ?>
      </ol>
    </div>

    <div class="obc-card">
      <h3><?php echo esc_html( $oc['card_title'] ); ?></h3>
      <ul>
        <?php foreach ( $oc['card_bullets'] as $b ) : ?>
        <li><?php echo $oc_check; ?><span><?php echo esc_html( $b ); ?></span></li>
        <?php endforeach; ?>
      </ul>
      <a href="<?php echo esc_url( $oc['booking_url'] ); ?>" class="btn btn-primary hv2-cta" data-ev="cta_get_access_clicked" data-ev-label="onboarding-section"><?php echo esc_html( $oc['cta_label'] ); ?></a>
      <p class="obc-micro"><?php echo esc_html( $oc['micro'] ); ?></p>
      <p class="obc-alt">Prefer to explore on your own? <a href="<?php echo esc_url( $oc['signup_url'] ); ?>" class="hv2-skip" data-ev="explore_yourself_clicked">Sign up directly</a></p>
    </div>
  </div>
</section>
