<?php
/**
 * Reusable link cards ("Explore Related Solutions" / industry-card design).
 *
 * A responsive grid of cards, each with an icon tile, a heading, a short
 * description and a "Read more →" link. On hover the brand blue fills in from the
 * bottom-right corner and the text/icon turn white. Self-contained: ships its own
 * CSS once per request; works with ANY number of cards.
 *
 * Pass props via the 3rd arg of get_template_part():
 *
 *   get_template_part( 'template-parts/section-link-cards', null, array(
 *     'eyebrow'      => '',                       // optional mono kicker
 *     'title'        => 'Explore Related Solutions',
 *     'lead'         => 'Optional intro paragraph.',
 *     'shade'        => false,                     // tint the section bg (optional)
 *     'card_as_link' => true,                      // whole card is the link (default).
 *                                                  // set FALSE if a card's text contains
 *                                                  // its own <a> (avoids nested anchors)
 *     'footnote'     => '',                        // small paragraph under the grid (HTML, optional)
 *     'cards'        => array(                      // REQUIRED — any length
 *       array(
 *         'title'      => 'React Chat SDK',
 *         'text'       => 'Add messaging to your web app.',  // inline HTML allowed
 *         'icon'       => '<svg …>…</svg>',                  // line icon (optional)
 *         'url'        => '/chat-sdk/…',
 *         'link_label' => 'Read more',                       // optional (default "Read more")
 *       ),
 *       // …
 *     ),
 *   ) );
 *
 * @package ethora-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$lc = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'eyebrow'      => '',
		'title'        => '',
		'lead'         => '',
		'shade'        => false,
		'card_as_link' => true,
		'footnote'     => '',
		'cards'        => array(),
	)
);

if ( empty( $lc['cards'] ) || ! is_array( $lc['cards'] ) ) {
	return;
}

// Emit the shared CSS only once per request, no matter how many instances.
$lc_assets = empty( $GLOBALS['shs_lc_assets'] );
if ( $lc_assets ) {
	$GLOBALS['shs_lc_assets'] = true;
}
?>
<?php if ( $lc_assets ) : ?>
<style>
  /* LINK CARDS — icon + heading + text + "Read more", hover fills brand blue from the corner. Tokens only. */
  .shs-lc-section { padding: var(--section-y) var(--section-x); }
  .shs-lc-section, .shs-lc-section *, .shs-lc-section *::before, .shs-lc-section *::after { box-sizing: border-box; }
  .shs-lc-section.is-shaded { background: var(--surface-alt); border-top: 1px solid var(--hairline); border-bottom: 1px solid var(--hairline); }
  .shs-lc-wrap { max-width: var(--content-max); margin: 0 auto; }
  .shs-lc-eyebrow { font-family: var(--font-mono); font-weight: var(--fw-medium); font-size: var(--fs-eyebrow); letter-spacing: var(--tracking-wide); text-transform: uppercase; color: var(--primary); margin: 0 0 var(--space-8); }
  .shs-lc-h2 { font-family: var(--font-serif); font-weight: var(--fw-medium); font-size: var(--fs-h2); line-height: 1.08; letter-spacing: -.01em; color: var(--ink); margin: 0; max-width: 640px; }
  .shs-lc-lead { font-size: var(--fs-lg); line-height: var(--lh-relaxed); color: var(--text-body); margin: var(--space-16) 0 0; max-width: 560px; }
  .shs-lc-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: var(--space-16); margin-top: var(--space-48); }
  .shs-lc-card { position: relative; overflow: hidden; display: flex; flex-direction: column;
    background: #fff; border: 1px solid var(--border); border-radius: var(--radius-2xl); padding: var(--space-32);
    text-decoration: none; color: inherit; transition: transform .3s ease, box-shadow .3s ease, border-color .3s ease; }
  .shs-lc-card::before { content: ""; position: absolute; inset: 0; z-index: 0; background: var(--primary);
    clip-path: circle(0% at 100% 100%); transition: clip-path .45s cubic-bezier(.4,0,.2,1); }
  .shs-lc-card > * { position: relative; z-index: 1; }
  .shs-lc-card:hover { transform: scale(1.03); box-shadow: var(--shadow-lift); border-color: var(--primary); }
  .shs-lc-card:hover::before { clip-path: circle(150% at 100% 100%); }
  .shs-lc-card:focus-visible { outline: 2px solid var(--primary); outline-offset: 2px; }
  .shs-lc-ico { width: 44px; height: 44px; border-radius: var(--radius-md); background: var(--tint);
    display: flex; align-items: center; justify-content: center; margin-bottom: var(--space-16); transition: background .35s ease; }
  .shs-lc-ico svg { width: 22px; height: 22px; transition: stroke .35s ease; }
  .shs-lc-card h3 { font-family: var(--font-serif); font-weight: var(--fw-semibold); font-size: var(--fs-2xl); color: var(--ink); margin: 0 0 var(--space-8); transition: color .35s ease; }
  .shs-lc-card p { font-size: var(--fs-base); line-height: var(--lh-relaxed); color: var(--text-body-soft); margin: 0 0 var(--space-16); transition: color .35s ease; }
  .shs-lc-card p a { color: var(--primary); text-decoration: underline; text-underline-offset: 2px; transition: color .35s ease; }
  .shs-lc-more { margin-top: auto; align-self: flex-start; display: inline-flex; align-items: center; gap: var(--space-8);
    font-size: var(--fs-sm); font-weight: var(--fw-semibold); color: var(--primary); text-decoration: none; transition: color .35s ease, gap .25s ease; }
  .shs-lc-card:hover h3, .shs-lc-card:hover p, .shs-lc-card:hover p a, .shs-lc-card:hover .shs-lc-more { color: #fff; }
  .shs-lc-card:hover .shs-lc-ico { background: rgba(255,255,255,.18); }
  .shs-lc-card:hover .shs-lc-ico svg { stroke: #fff; }
  .shs-lc-card:hover .shs-lc-more { gap: var(--space-16); }
  .shs-lc-footnote { font-size: var(--fs-sm); line-height: var(--lh-relaxed); color: var(--text-body); margin: var(--space-32) 0 0; }
  .shs-lc-footnote a { color: var(--primary); font-weight: var(--fw-semibold); text-decoration: underline; text-underline-offset: 2px; }
  @media (prefers-reduced-motion: reduce) { .shs-lc-card, .shs-lc-card::before { transition: none; } .shs-lc-card:hover { transform: none; } }
</style>
<?php endif; ?>

<section class="shs-lc-section<?php echo $lc['shade'] ? ' is-shaded' : ''; ?>">
  <div class="shs-lc-wrap">
    <?php if ( $lc['eyebrow'] || $lc['title'] || $lc['lead'] ) : ?>
    <div>
      <?php if ( $lc['eyebrow'] ) : ?><p class="shs-lc-eyebrow"><?php echo esc_html( $lc['eyebrow'] ); ?></p><?php endif; ?>
      <?php if ( $lc['title'] ) : ?><h2 class="shs-lc-h2"><?php echo wp_kses_post( $lc['title'] ); ?></h2><?php endif; ?>
      <?php if ( $lc['lead'] ) : ?><p class="shs-lc-lead"><?php echo wp_kses_post( $lc['lead'] ); ?></p><?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="shs-lc-grid">
      <?php
      foreach ( $lc['cards'] as $card ) :
		$card = wp_parse_args( (array) $card, array( 'title' => '', 'text' => '', 'icon' => '', 'url' => '', 'link_label' => 'Read more' ) );
		$as_link = ( $lc['card_as_link'] && $card['url'] );
		$tag     = $as_link ? 'a' : 'div';
		$href    = $as_link ? ' href="' . esc_url( $card['url'] ) . '"' : '';
		?>
      <<?php echo $tag . $href; ?> class="shs-lc-card">
        <?php if ( $card['icon'] ) : ?><div class="shs-lc-ico" aria-hidden="true"><?php echo $card['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted inline SVG markup ?></div><?php endif; ?>
        <?php if ( $card['title'] ) : ?><h3><?php echo wp_kses_post( $card['title'] ); ?></h3><?php endif; ?>
        <?php if ( $card['text'] ) : ?><p><?php echo wp_kses_post( $card['text'] ); ?></p><?php endif; ?>
        <?php if ( $card['url'] ) : ?>
          <?php if ( $as_link ) : ?>
          <span class="shs-lc-more"><?php echo esc_html( $card['link_label'] ); ?> <span aria-hidden="true">&rarr;</span></span>
          <?php else : ?>
          <a class="shs-lc-more" href="<?php echo esc_url( $card['url'] ); ?>"><?php echo esc_html( $card['link_label'] ); ?> <span aria-hidden="true">&rarr;</span></a>
          <?php endif; ?>
        <?php endif; ?>
      </<?php echo $tag; ?>>
      <?php endforeach; ?>
    </div>

    <?php if ( $lc['footnote'] ) : ?>
    <p class="shs-lc-footnote"><?php echo wp_kses_post( $lc['footnote'] ); ?></p>
    <?php endif; ?>
  </div>
</section>
