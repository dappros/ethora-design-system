<?php
/**
 * Reusable FAQ accordion (design from the healthcare page).
 *
 * A list of question/answer items in bordered cards; clicking a question expands its
 * answer (one open at a time). Styling is the theme's global `.faq*` CSS (in
 * `css/index.css`) and the toggle behaviour is the global handler in `main.js` — this
 * partial only emits the markup, so it matches every other FAQ on the site.
 *
 * Pass props via the 3rd arg of get_template_part():
 *
 *   get_template_part( 'template-parts/section-faq', null, array(
 *     'title'      => 'FAQ',                 // heading (optional, default 'FAQ')
 *     'subheader'  => '',                    // intro line under the heading (optional, HTML ok)
 *     'id'         => 'faq',                 // section id for anchor links (optional)
 *     'open_first' => true,                  // expand the first item by default (optional, default true)
 *     'schema'     => false,                 // also emit FAQPage JSON-LD from the items (optional)
 *     'items'      => array(                  // REQUIRED — any length
 *       array( 'q' => 'Question?', 'a' => 'Answer paragraph (HTML ok).' ),
 *       array( 'q' => 'Multi-paragraph?', 'a' => array( 'First para.', 'Second para.' ), 'open' => true ),
 *     ),
 *   ) );
 *
 * @package ethora-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$faq = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'title'      => 'FAQ',
		'subheader'  => '',
		'id'         => '',
		'open_first' => true,
		'schema'     => false,
		'items'      => array(),
	)
);

if ( empty( $faq['items'] ) || ! is_array( $faq['items'] ) ) {
	return;
}
?>
<section class="faq"<?php echo $faq['id'] ? ' id="' . esc_attr( $faq['id'] ) . '"' : ''; ?>>
  <div class="container">
    <div class="faq-container">
      <?php if ( $faq['title'] ) : ?>
      <div class="faq-header"><h2><?php echo wp_kses_post( $faq['title'] ); ?></h2></div>
      <?php endif; ?>
      <?php if ( $faq['subheader'] ) : ?><p class="faq-subheader"><?php echo wp_kses_post( $faq['subheader'] ); ?></p><?php endif; ?>

      <div class="faq-list">
        <?php
        foreach ( $faq['items'] as $faq_i => $item ) :
			$item = wp_parse_args( (array) $item, array( 'q' => '', 'a' => '', 'open' => null ) );
			if ( '' === $item['q'] ) {
				continue;
			}
			$open  = is_null( $item['open'] ) ? ( $faq['open_first'] && 0 === $faq_i ) : (bool) $item['open'];
			$paras = is_array( $item['a'] ) ? $item['a'] : array( $item['a'] );
			?>
        <div class="faq-item<?php echo $open ? ' expanded' : ''; ?>">
          <div class="faq-question">
            <h3><?php echo wp_kses_post( $item['q'] ); ?></h3>
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="m6 9 6 6 6-6" />
            </svg>
          </div>
          <div class="faq-answer">
            <?php foreach ( $paras as $p ) : if ( '' === $p ) { continue; } ?>
            <p><?php echo wp_kses_post( $p ); ?></p>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<?php
if ( ! empty( $faq['schema'] ) ) :
	$faq_schema = array(
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'mainEntity' => array(),
	);
	foreach ( $faq['items'] as $item ) {
		$item = wp_parse_args( (array) $item, array( 'q' => '', 'a' => '' ) );
		if ( '' === $item['q'] ) {
			continue;
		}
		$paras = is_array( $item['a'] ) ? $item['a'] : array( $item['a'] );
		$faq_schema['mainEntity'][] = array(
			'@type'          => 'Question',
			'name'           => wp_strip_all_tags( $item['q'] ),
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => wp_strip_all_tags( implode( ' ', $paras ) ),
			),
		);
	}
	?>
<script type="application/ld+json"><?php echo wp_json_encode( $faq_schema ); ?></script>
	<?php
endif;
