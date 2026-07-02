<?php
/**
 * Reusable "Choose App" feature-cards section.
 *
 * Renders the choose-app section in either the white or blue variant.
 * Content is fully passed in, so the same block is reused across the site.
 *
 * Usage:
 *   get_template_part( 'template-parts/section', 'choose-app', array(
 *     'variant'     => 'blue-usual',            // 'white' (default) | 'blue' | 'blue-usual'
 *                                              //   white      → <section class="choose-app">, plain cards
 *                                              //   blue       → <section class="choose-app blue">, cards get .blue
 *                                              //   blue-usual → <section class="choose-app feature-card-section-blue-usual">, plain cards
 *     'title'       => 'Key Benefits…',
 *     'description' => 'Ethora’s Chat SDK…',
 *     'cards'       => array(
 *       array(
 *         'icon'  => 'scalability.svg',        // bare file → images/chooseApp/<file>,
 *                                              // or a path with "/" → relative to theme root
 *         'title' => 'Pre-built components',
 *         'text'  => 'Ethora’s SDK has…',
 *         'alt'   => 'Pre-built components',   // optional, defaults to card title
 *       ),
 *       // …
 *     ),
 *   ) );
 *
 * @param array $args Passed through get_template_part().
 */

$variant     = isset( $args['variant'] ) ? $args['variant'] : 'white';
$title       = isset( $args['title'] ) ? $args['title'] : '';
$description = isset( $args['description'] ) ? $args['description'] : '';
$cards       = isset( $args['cards'] ) && is_array( $args['cards'] ) ? $args['cards'] : array();
$modifier    = isset( $args['modifier'] ) ? $args['modifier'] : '';

$classes    = 'choose-app';
$card_class = 'block-choose-app';
if ( 'blue' === $variant ) {
	$classes   .= ' blue';
	$card_class = 'block-choose-app blue';
} elseif ( 'blue-usual' === $variant ) {
	$classes .= ' feature-card-section-blue-usual';
}
if ( $modifier ) {
	$classes .= ' ' . $modifier;
}

// Optional presentation tweaks some pages need.
$icon_style   = isset( $args['icon_style'] ) ? $args['icon_style'] : '';   // inline style on each card <img> (e.g. 'width: 38px; height: 38px;')
$header_align = isset( $args['header_align'] ) ? $args['header_align'] : ''; // align-items value on each .header-choose-app (e.g. 'stretch')

$tpl = get_template_directory_uri();
?>
<section class="<?php echo esc_attr( $classes ); ?>">
	<div class="container">
		<?php if ( $title ) : ?>
			<h2><?php echo esc_html( $title ); ?></h2>
		<?php endif; ?>

		<?php if ( $description ) : ?>
			<p class="description-choose-app"><?php echo wp_kses_post( $description ); ?></p>
		<?php endif; ?>

		<?php if ( $cards ) : ?>
			<div class="blocks-choose-app">
				<?php
				foreach ( $cards as $card ) :
					$icon       = isset( $card['icon'] ) ? $card['icon'] : '';
					$card_title = isset( $card['title'] ) ? $card['title'] : '';
					$card_text  = isset( $card['text'] ) ? $card['text'] : '';
					$alt        = isset( $card['alt'] ) ? $card['alt'] : $card_title;

					// Bare filename → images/chooseApp/<file>; any path with "/" → relative to theme root.
					if ( $icon && false === strpos( $icon, '/' ) ) {
						$icon = 'images/chooseApp/' . $icon;
					}
					$icon_url = $icon ? $tpl . '/' . ltrim( $icon, '/' ) : '';
					?>
					<div class="<?php echo esc_attr( $card_class ); ?>">
						<div class="header-choose-app"<?php echo $header_align ? ' style="align-items: ' . esc_attr( $header_align ) . ';"' : ''; ?>>
							<?php if ( $icon_url ) : ?>
								<img src="<?php echo esc_url( $icon_url ); ?>" alt="<?php echo esc_attr( $alt ); ?>"<?php echo $icon_style ? ' style="' . esc_attr( $icon_style ) . '"' : ''; ?> />
							<?php endif; ?>
							<?php if ( $card_title ) : ?>
								<h3><?php echo esc_html( $card_title ); ?></h3>
							<?php endif; ?>
						</div>
						<?php if ( $card_text ) : ?>
							<p><?php echo wp_kses_post( $card_text ); ?></p>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
