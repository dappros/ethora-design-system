<?php
/**
 * Reusable "Customers" testimonials CAROUSEL.
 * Shows 3 cards (2 on tablet, 1 on mobile); auto-advances one card every few
 * seconds, infinite loop (after the last comes the first), with prev/next buttons.
 * Editorial style, fully tokenised (css/tokens.css).
 *
 * Params ($args): 'eyebrow', 'heading', 'interval' (ms).
 */

$tc = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'eyebrow'  => 'Customers',
		'heading'  => 'Trusted by teams shipping regulated communication',
		'interval' => 5000,
	)
);
$u = get_template_directory_uri();

$tc_items = array(
	array( 'Taras and the Ethora team are a reliable partner in messaging, tokenization and AI. Great communication and products.', 'Joe Sticca', 'Digital Product &amp; Technology Leader, Trust Industries', 'Joe_Sticca.svg' ),
	array( 'I have worked with Ethora and team since 2018. They have been absolutely amazing and life-saving across multiple of my portfolio projects.', 'Adam Palmer', 'Co-founder, SIAD Ventures', 'Adam_Palmer.svg' ),
	array( 'We partnered with Dappros/Ethora on an EU project and they successfully delivered the technology component. Highly recommended.', 'Peter Fearon', 'CEO, Remade Group', 'Peter_Fearon.svg' ),
	array( 'Ethora has successfully helped us ideate and deliver multiple digital transformation projects in Qatar and the MENA region. A reliable and knowledgable partner.', 'Tariq Gulrez', 'Ministry of Communications &amp; IT', 'Tariq_Gulrez.svg' ),
	array( 'Ethora technology and expertise were instrumental in delivering Atom Connect, our product for streamlined communication in the workers&rsquo; comp market.', 'Carol Valentic', 'CEO, Candollar', 'Carol_Valentic.jpeg' ),
	array( 'Ethora helped us deliver our AI agent solution, letting subscribers access context-specific insights from our vast library of expert content. Their LLM and chat-bot expertise is an excellent match for our AI strategy.', 'Bret Gregory', 'CEO, DrTalks', 'Brett_Gregory.jpeg' ),
	array( 'Ethora has been an exceptional strategic development partner for Preshent. Not just a vendor, but a core contributor to the future of our platform and company.', 'John Richardson', 'Founder &amp; CEO, Preshent Corporation', 'John_Richardson.jpeg' ),
	array( 'Ethora helped us ideate and deliver multiple digital transformation projects across Qatar and the MENA region. A reliable, knowledgable partner.', 'Howaida Nadim', 'CEO, Foresight Communications', 'Howaida_Nadim.svg' ),
);

$tc_arrow = function ( $dir ) {
	$d = ( 'prev' === $dir ) ? 'M15 18l-6-6 6-6' : 'M9 18l6-6-6-6';
	return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="' . $d . '"/></svg>';
};

static $tc_assets = false;
if ( ! $tc_assets ) :
	$tc_assets = true;
	?>
	<style>
	  .tcar { padding: var(--section-y) var(--section-x); font-family: var(--font-sans); }
	  .tcar-inner { max-width: var(--content-max); margin: 0 auto; }
	  .tcar-head { display: flex; align-items: flex-end; justify-content: space-between; gap: var(--space-16); margin-bottom: var(--space-48); }
	  .tcar-head .eb { font-family: var(--font-mono); font-weight: 500; font-size: var(--fs-eyebrow); letter-spacing: var(--tracking-wider); text-transform: uppercase; color: var(--primary); }
	  .tcar-head h2 { font-family: var(--font-serif); font-weight: 500; font-size: var(--fs-h2); line-height: var(--lh-tight); letter-spacing: var(--tracking-tight); color: var(--ink); margin: var(--space-16) 0 0; max-width: 640px; }
	  .tcar-ctrls { display: flex; gap: var(--space-8); flex: none; }
	  .tcar-btn { width: 44px; height: 44px; border-radius: var(--radius-pill); border: 1px solid var(--border-strong); background: var(--white); color: var(--ink); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background .2s ease, color .2s ease, border-color .2s ease; }
	  .tcar-btn:hover { background: var(--primary); color: var(--white); border-color: var(--primary); }
	  .tcar-viewport { overflow: hidden; }
	  .tcar-track { display: flex; gap: var(--space-32); will-change: transform; }
	  .tcar-card { flex: 0 0 calc((100% - 2 * var(--space-32)) / 3); box-sizing: border-box; background: var(--white); border: 1px solid var(--border); border-radius: var(--radius-2xl); padding: var(--space-32); box-shadow: var(--shadow-card); display: flex; flex-direction: column; }
	  .tcar-card .tc-quote { width: 32px; opacity: .35; margin-bottom: var(--space-16); }
	  .tcar-card .tc-text { font-size: var(--fs-md); line-height: var(--lh-relaxed); color: var(--text-body); margin: 0 0 var(--space-32); flex: 1; }
	  .tcar-card .tc-author { display: flex; align-items: center; gap: var(--space-16); }
	  .tcar-card .tc-author img { width: 48px; height: 48px; border-radius: var(--radius-pill); flex: none; object-fit: cover; }
	  .tcar-card .tc-nm { font-weight: var(--fw-bold); font-size: var(--fs-sm); color: var(--ink); }
	  .tcar-card .tc-ro { font-size: 13px; color: var(--text-caption); margin-top: 2px; }
	  @media (max-width: 900px) { .tcar-card { flex-basis: calc((100% - var(--space-32)) / 2); } }
	  @media (max-width: 600px) { .tcar-card { flex-basis: 100%; } .tcar-head { flex-wrap: wrap; } }
	</style>
	<?php
endif;
?>
<section class="tcar" data-interval="<?php echo (int) $tc['interval']; ?>">
  <div class="tcar-inner">
    <div class="tcar-head">
      <div>
        <span class="eb"><?php echo esc_html( $tc['eyebrow'] ); ?></span>
        <h2><?php echo esc_html( $tc['heading'] ); ?></h2>
      </div>
      <div class="tcar-ctrls">
        <button type="button" class="tcar-btn tcar-prev" aria-label="Previous testimonials"><?php echo $tc_arrow( 'prev' ); ?></button>
        <button type="button" class="tcar-btn tcar-next" aria-label="Next testimonials"><?php echo $tc_arrow( 'next' ); ?></button>
      </div>
    </div>
    <div class="tcar-viewport">
      <div class="tcar-track">
        <?php foreach ( $tc_items as $t ) : ?>
        <div class="tcar-card">
          <img class="tc-quote" src="<?php echo $u; ?>/images/vector/commas.svg" alt="" width="32" height="24" />
          <p class="tc-text"><?php echo $t[0]; ?></p>
          <div class="tc-author">
            <img src="<?php echo $u; ?>/images/avatar/<?php echo esc_attr( $t[3] ); ?>" alt="<?php echo esc_attr( $t[1] ); ?>" width="48" height="48" loading="lazy" />
            <div>
              <div class="tc-nm"><?php echo esc_html( $t[1] ); ?></div>
              <div class="tc-ro"><?php echo $t[2]; ?></div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
<script>
  (function () {
    var sec = document.currentScript.previousElementSibling;
    while ( sec && ! ( sec.classList && sec.classList.contains('tcar') ) ) { sec = sec.previousElementSibling; }
    if ( ! sec ) { return; }
    var track = sec.querySelector('.tcar-track');
    var prev = sec.querySelector('.tcar-prev');
    var next = sec.querySelector('.tcar-next');
    if ( ! track || track.children.length < 2 ) { return; }
    var interval = parseInt( sec.getAttribute('data-interval'), 10 ) || 5000;
    var animating = false, timer = null;
    function gap() { return parseFloat( getComputedStyle( track ).columnGap || getComputedStyle( track ).gap ) || 32; }
    function step() { var c = track.children[0]; return c ? c.getBoundingClientRect().width + gap() : 0; }
    function goNext() {
      if ( animating ) { return; }
      animating = true;
      track.style.transition = 'transform .5s ease';
      track.style.transform = 'translateX(' + ( -step() ) + 'px)';
      window.setTimeout( function () {
        track.style.transition = 'none';
        track.appendChild( track.children[0] );
        track.style.transform = 'translateX(0)';
        void track.offsetWidth;
        animating = false;
      }, 540 );
    }
    function goPrev() {
      if ( animating ) { return; }
      animating = true;
      track.insertBefore( track.children[ track.children.length - 1 ], track.children[0] );
      track.style.transition = 'none';
      track.style.transform = 'translateX(' + ( -step() ) + 'px)';
      void track.offsetWidth;
      track.style.transition = 'transform .5s ease';
      track.style.transform = 'translateX(0)';
      window.setTimeout( function () { animating = false; }, 540 );
    }
    function start() { stop(); timer = window.setInterval( goNext, interval ); }
    function stop() { if ( timer ) { window.clearInterval( timer ); timer = null; } }
    next.addEventListener('click', function () { goNext(); start(); });
    prev.addEventListener('click', function () { goPrev(); start(); });
    sec.addEventListener('mouseenter', stop);
    sec.addEventListener('mouseleave', start);
    start();
  })();
</script>
