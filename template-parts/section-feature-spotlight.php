<?php
/**
 * Reusable feature spotlight.
 *
 * Title + lead, then a big "flagship" feature card (numbered 01) on a brand-blue
 * gradient — heading + text + chips on the left, a chat mockup (or image) on the
 * right — followed by a grid of smaller numbered white cards (02, 03 …) with a
 * tinted icon, heading and text. Self-contained (CSS once per request), responsive.
 *
 * Pass props via the 3rd arg of get_template_part():
 *
 *   get_template_part( 'template-parts/section-feature-spotlight', null, array(
 *     'eyebrow' => '', 'title' => '', 'lead' => '',     // optional header
 *     'flagship' => array(
 *       'label' => 'Flagship capability',
 *       'title' => 'Intelligent patient FAQ handling',
 *       'text'  => '…',
 *       'chips' => array( 'Trained on your docs', '24/7 answers', 'Cited sources' ),
 *       'chat'  => array( 'name' => 'Ethora Assistant', 'status' => 'Online',
 *                         'question' => '…', 'answer' => '…', 'file' => 'pre-op-guide.pdf',
 *                         'avatar_icon' => '<svg…>' ),
 *       // OR  'image' => 'images/foo.png', 'image_alt' => '…'
 *     ),
 *     'items' => array(    // the smaller numbered cards (start at 02)
 *       array( 'icon' => '<svg stroke="currentColor"…>', 'title' => '…', 'text' => '…' ),
 *       // …
 *     ),
 *   ) );
 *
 * @package ethora-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$fs = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'eyebrow'  => '',
		'title'    => '',
		'lead'     => '',
		'flagship' => array(),
		'items'    => array(),
	)
);

$flag = wp_parse_args(
	(array) $fs['flagship'],
	array( 'label' => '', 'title' => '', 'text' => '', 'chips' => array(), 'chat' => array(), 'image' => '', 'image_alt' => '' )
);
if ( empty( $flag['title'] ) ) {
	return;
}

$fs_uri = get_template_directory_uri();
$fs_src = function ( $img ) use ( $fs_uri ) {
	return ( ! $img || preg_match( '#^(https?:)?//#', $img ) ) ? $img : $fs_uri . '/' . ltrim( $img, '/' );
};

$fs_assets = empty( $GLOBALS['shs_fs_assets'] );
if ( $fs_assets ) {
	$GLOBALS['shs_fs_assets'] = true;
}
?>
<?php if ( $fs_assets ) : ?>
<style>
  /* FEATURE SPOTLIGHT — flagship blue card (chat mockup) + numbered white cards. Tokens only. */
  .fs-section { padding: var(--section-y) var(--section-x); }
  .fs-section, .fs-section *, .fs-section *::before, .fs-section *::after { box-sizing: border-box; }
  .fs-wrap { max-width: var(--content-max); margin: 0 auto; }
  .fs-eyebrow { font-family: var(--font-mono); font-weight: var(--fw-medium); font-size: var(--fs-eyebrow); letter-spacing: var(--tracking-wide); text-transform: uppercase; color: var(--primary); margin: 0 0 var(--space-8); }
  .fs-h2 { font-family: var(--font-serif); font-weight: var(--fw-medium); font-size: var(--fs-h2); line-height: 1.08; letter-spacing: -.01em; color: var(--ink); margin: 0; max-width: 720px; }
  .fs-lead { font-size: var(--fs-lg); line-height: var(--lh-relaxed); color: var(--text-body); margin: var(--space-16) 0 0; max-width: 720px; }

  /* flagship card */
  .fs-flagship { margin-top: var(--space-48); border-radius: var(--radius-3xl); overflow: hidden;
    padding: clamp(var(--space-32),4vw,var(--space-64));
    background: var(--gradient-brand); color: var(--text-on-dark);
    display: grid; grid-template-columns: minmax(0,1fr) minmax(0,1fr); gap: clamp(var(--space-32),4vw,var(--space-64)); align-items: center; }
  .fs-flag-label { font-family: var(--font-mono); font-weight: var(--fw-semibold); font-size: var(--fs-xs); letter-spacing: var(--tracking-wide); text-transform: uppercase; color: var(--accent-on-dark); margin: 0; }
  .fs-flag-title { font-family: var(--font-serif); font-weight: var(--fw-semibold); font-size: clamp(26px,3.2vw,40px); line-height: var(--lh-snug); letter-spacing: -.01em; color: #fff; margin: var(--space-16) 0 0; }
  .fs-flag-text { font-size: var(--fs-lg); line-height: var(--lh-relaxed); color: var(--text-on-dark); margin: var(--space-16) 0 0; }
  .fs-flag-text a { color: #fff; text-decoration: underline; text-underline-offset: 2px; }
  .fs-chips { display: flex; flex-wrap: wrap; gap: var(--space-8); margin-top: var(--space-32); }
  .fs-chip { font-size: var(--fs-sm); color: #fff; border: 1px solid rgba(255,255,255,.35); border-radius: var(--radius-pill); padding: var(--space-8) var(--space-16); }

  /* chat mockup */
  .fs-chat { background: #fff; border-radius: var(--radius-2xl); box-shadow: var(--shadow-lift); padding: var(--space-32); }
  .fs-chat-head { display: flex; align-items: center; gap: var(--space-16); padding-bottom: var(--space-16); border-bottom: 1px solid var(--border-hair); }
  .fs-chat-ava { flex: none; width: 40px; height: 40px; border-radius: var(--radius-md); background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center; }
  .fs-chat-ava svg { width: 20px; height: 20px; }
  .fs-chat-name { font-weight: var(--fw-semibold); color: var(--ink); font-size: var(--fs-md); }
  .fs-chat-status { display: flex; align-items: center; gap: var(--space-8); font-size: var(--fs-sm); color: var(--green); }
  .fs-chat-status::before { content: ""; width: 7px; height: 7px; border-radius: 50%; background: var(--green); }
  .fs-chat-body { display: flex; flex-direction: column; gap: var(--space-16); padding-top: var(--space-16); }
  .fs-bubble { font-size: var(--fs-sm); line-height: var(--lh-base); border-radius: var(--radius-lg); padding: var(--space-16); max-width: 88%; }
  .fs-bubble--user { align-self: flex-end; background: var(--surface-alt); color: var(--ink); border-bottom-right-radius: var(--radius-xs); }
  .fs-bubble--bot { align-self: flex-start; background: #fff; border: 1px solid var(--border); color: var(--text-body); border-bottom-left-radius: var(--radius-xs); }
  .fs-bubble-file { display: inline-flex; align-items: center; gap: var(--space-8); font-family: var(--font-mono); font-size: var(--fs-xs); color: var(--primary); margin-top: var(--space-8); }
  .fs-typing { display: flex; gap: 5px; padding-left: var(--space-8); }
  .fs-typing span { width: 6px; height: 6px; border-radius: 50%; background: var(--border-strong); }
  .fs-flag-media img { width: 100%; height: auto; display: block; border-radius: var(--radius-2xl); box-shadow: var(--shadow-lift); }

  /* numbered cards */
  .fs-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px,1fr)); gap: var(--space-16); margin-top: var(--space-16); }
  .fs-card { position: relative; background: #fff; border: 1px solid var(--border); border-radius: var(--radius-2xl); padding: clamp(var(--space-32),3vw,var(--space-48)); }
  .fs-card-num { position: absolute; top: clamp(var(--space-32),3vw,var(--space-48)); right: clamp(var(--space-32),3vw,var(--space-48)); font-family: var(--font-mono); font-size: var(--fs-sm); font-weight: var(--fw-bold); color: var(--border-strong); }
  .fs-card-ico { width: 44px; height: 44px; border-radius: var(--radius-md); background: var(--tint); color: var(--primary); display: flex; align-items: center; justify-content: center; margin-bottom: var(--space-32); }
  .fs-card-ico svg { width: 22px; height: 22px; }
  .fs-card h3 { font-family: var(--font-serif); font-weight: var(--fw-semibold); font-size: var(--fs-2xl); color: var(--ink); margin: 0; line-height: var(--lh-snug); }
  .fs-card p { font-size: var(--fs-md); line-height: var(--lh-relaxed); color: var(--text-body); margin: var(--space-16) 0 0; }
  .fs-card p a { color: var(--primary); text-decoration: underline; text-underline-offset: 2px; }

  @media (max-width: 900px) {
    .fs-flagship { grid-template-columns: 1fr; }
  }
</style>
<?php endif; ?>

<?php
$fs_chat = wp_parse_args( (array) $flag['chat'], array( 'name' => '', 'status' => 'Online', 'question' => '', 'answer' => '', 'file' => '', 'avatar_icon' => '' ) );
$fs_img  = $fs_src( $flag['image'] );
?>
<section class="fs-section">
  <div class="fs-wrap">
    <?php if ( $fs['eyebrow'] || $fs['title'] || $fs['lead'] ) : ?>
    <div class="fs-head">
      <?php if ( $fs['eyebrow'] ) : ?><p class="fs-eyebrow"><?php echo esc_html( $fs['eyebrow'] ); ?></p><?php endif; ?>
      <?php if ( $fs['title'] ) : ?><h2 class="fs-h2"><?php echo wp_kses_post( $fs['title'] ); ?></h2><?php endif; ?>
      <?php if ( $fs['lead'] ) : ?><p class="fs-lead"><?php echo wp_kses_post( $fs['lead'] ); ?></p><?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- flagship -->
    <div class="fs-flagship">
      <div class="fs-flag-body">
        <p class="fs-flag-label">01<?php echo $flag['label'] ? ' &middot; ' . esc_html( $flag['label'] ) : ''; ?></p>
        <h3 class="fs-flag-title"><?php echo wp_kses_post( $flag['title'] ); ?></h3>
        <?php if ( $flag['text'] ) : ?><p class="fs-flag-text"><?php echo wp_kses_post( $flag['text'] ); ?></p><?php endif; ?>
        <?php if ( ! empty( $flag['chips'] ) ) : ?>
        <div class="fs-chips">
          <?php foreach ( (array) $flag['chips'] as $chip ) : ?><span class="fs-chip"><?php echo esc_html( $chip ); ?></span><?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

      <?php if ( $fs_img ) : ?>
      <div class="fs-flag-media"><img src="<?php echo esc_url( $fs_img ); ?>" alt="<?php echo esc_attr( $flag['image_alt'] ); ?>" loading="lazy" /></div>
      <?php elseif ( $fs_chat['question'] || $fs_chat['answer'] ) : ?>
      <div class="fs-chat" aria-hidden="true">
        <div class="fs-chat-head">
          <span class="fs-chat-ava"><?php echo $fs_chat['avatar_icon'] ? $fs_chat['avatar_icon'] : '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="8" width="16" height="12" rx="2"/><path d="M12 8V5"/><circle cx="12" cy="3.6" r="1.2"/><circle cx="9" cy="14" r="1"/><circle cx="15" cy="14" r="1"/></svg>'; // phpcs:ignore ?></span>
          <span>
            <span class="fs-chat-name"><?php echo esc_html( $fs_chat['name'] ); ?></span><br>
            <span class="fs-chat-status"><?php echo esc_html( $fs_chat['status'] ); ?></span>
          </span>
        </div>
        <div class="fs-chat-body">
          <?php if ( $fs_chat['question'] ) : ?><div class="fs-bubble fs-bubble--user"><?php echo esc_html( $fs_chat['question'] ); ?></div><?php endif; ?>
          <?php if ( $fs_chat['answer'] ) : ?>
          <div class="fs-bubble fs-bubble--bot">
            <?php echo esc_html( $fs_chat['answer'] ); ?>
            <?php if ( $fs_chat['file'] ) : ?><span class="fs-bubble-file"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg><?php echo esc_html( $fs_chat['file'] ); ?></span><?php endif; ?>
          </div>
          <?php endif; ?>
          <div class="fs-typing"><span></span><span></span><span></span></div>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <!-- numbered cards -->
    <?php if ( ! empty( $fs['items'] ) ) : ?>
    <div class="fs-grid">
      <?php foreach ( (array) $fs['items'] as $i => $it ) : $it = wp_parse_args( (array) $it, array( 'icon' => '', 'title' => '', 'text' => '' ) ); ?>
      <div class="fs-card">
        <span class="fs-card-num"><?php echo esc_html( sprintf( '%02d', $i + 2 ) ); ?></span>
        <?php if ( $it['icon'] ) : ?><div class="fs-card-ico" aria-hidden="true"><?php echo $it['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div><?php endif; ?>
        <?php if ( $it['title'] ) : ?><h3><?php echo wp_kses_post( $it['title'] ); ?></h3><?php endif; ?>
        <?php if ( $it['text'] ) : ?><p><?php echo wp_kses_post( $it['text'] ); ?></p><?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>
