<?php
/**
 * Reusable "Use Kit" section.
 *
 * Sticky illustration on the left; content on the right with an OPTIONAL eyebrow
 * label, a heading, an intro paragraph (<p>), and a benefits list (icon + h3 + <p>).
 *
 * Usage:
 *   get_template_part( 'template-parts/section', 'use-kit', array(
 *     'label'       => 'Ready-to-Use Kit',   // OPTIONAL — omit/empty hides the .section-label
 *     'label_icon'  => 'star.svg',           // optional; bare → images/vector/<file> (default star.svg)
 *     'title'       => 'Integrate. Customize. Go live. Effortlessly',
 *     'description' => 'All components you need…', // rendered as <p>
 *     'image'       => 'use-kit.svg',         // foreground illustration; bare → images/<file>
 *     'image_alt'   => 'Use kit',
 *     'bg_image'    => 'rhombus-features-demo.svg', // background rhombus; bare → images/<file>
 *     'sticky'      => true,                  // OPTIONAL — illustration sticks while the list scrolls (like .shs-whatis), desktop only
 *     'benefits'    => array(
 *       array( 'icon' => 'chat-widget.svg', 'title' => 'React Chat Widget', 'text' => 'Minimal setup…' ),
 *       // icon: bare filename → images/use-kit/<file>; path with "/" → theme-root relative;
 *       //       OR a raw inline '<svg…>' (white stroke="#fff" on the blue tile) for a custom icon
 *     ),
 *     'outro'       => 'Closing note.',       // OPTIONAL — small <p> after the list
 *     'outro_icon'  => '<svg…>',              // OPTIONAL — inline SVG → renders 'outro' as a soft-blue
 *                                             //   callout box with the icon; wrap the lead in <strong> for a bold kicker
 *     'modifier'    => 'use-kit-cards',       // extra class(es) on <section>; 'use-kit-cards' → benefit rows
 *                                             //   become white cards on hover (elevation + shadow)
 *   ) );
 *
 * @param array $args Passed through get_template_part().
 */

$label       = isset( $args['label'] ) ? $args['label'] : '';
$label_icon  = isset( $args['label_icon'] ) ? $args['label_icon'] : 'star.svg';
$title       = isset( $args['title'] ) ? $args['title'] : '';
$description = isset( $args['description'] ) ? $args['description'] : '';
$outro       = isset( $args['outro'] ) ? $args['outro'] : ''; // optional closing <p> rendered after the benefits list
$outro_icon  = isset( $args['outro_icon'] ) ? $args['outro_icon'] : ''; // optional inline SVG → renders the outro as a soft-blue callout box with the icon
$image       = isset( $args['image'] ) ? $args['image'] : 'use-kit.svg';
$image_alt   = isset( $args['image_alt'] ) ? $args['image_alt'] : '';
$bg_image    = isset( $args['bg_image'] ) ? $args['bg_image'] : 'rhombus-features-demo.svg';
$benefits    = isset( $args['benefits'] ) && is_array( $args['benefits'] ) ? $args['benefits'] : array();
$modifier    = isset( $args['modifier'] ) ? $args['modifier'] : '';
$sticky      = ! empty( $args['sticky'] ); // true → the illustration sticks while the list scrolls (like .shs-whatis)

// Layout options.
$reverse = ! empty( $args['reverse'] );                              // true → illustration on the right
$align   = isset( $args['align'] ) ? $args['align'] : '';            // vertical alignment of the two columns: 'top' | 'center' | 'bottom'

// Optional inline-style overrides some pages need (e.g. smaller icon chips).
$grid_style_raw     = isset( $args['grid_style'] ) ? $args['grid_style'] : '';                 // raw escape hatch appended to .why-ethora-grid
$benefit_icon_style = isset( $args['benefit_icon_style'] ) ? $args['benefit_icon_style'] : ''; // on each .benefit-icon (e.g. 'width: 2rem; height: 2rem;')

// Build the .why-ethora-grid inline style from the semantic options (+ raw escape hatch).
$grid_style_parts = array();
$align_map        = array(
	'top'    => 'flex-start',
	'center' => 'center',
	'bottom' => 'flex-end',
);
if ( $align && isset( $align_map[ $align ] ) ) {
	$grid_style_parts[] = 'align-items: ' . $align_map[ $align ] . ';';
}
if ( $reverse ) {
	$grid_style_parts[] = 'flex-direction: row-reverse;';
}
if ( $grid_style_raw ) {
	$grid_style_parts[] = $grid_style_raw;
}
$grid_style = implode( '', $grid_style_parts );

$tpl = get_template_directory_uri();

/**
 * Resolve an asset path: bare filename gets $default_dir; any path with "/" is
 * treated as theme-root relative.
 */
$resolve = function ( $path, $default_dir ) use ( $tpl ) {
	if ( ! $path ) {
		return '';
	}
	if ( false === strpos( $path, '/' ) ) {
		$path = $default_dir . $path;
	}
	return $tpl . '/' . ltrim( $path, '/' );
};

$section_class = 'use-kit' . ( $sticky ? ' is-sticky' : '' ) . ( $modifier ? ' ' . $modifier : '' );
?>
<?php if ( empty( $GLOBALS['shs_usekit_assets'] ) ) : $GLOBALS['shs_usekit_assets'] = true; ?>
<style>
	/* sticky illustration (opt-in) + inline-SVG benefit icons */
	.use-kit.is-sticky .use-kit-sticky-wrapper { position: sticky; top: calc(var(--header-h) + var(--space-32)); align-self: flex-start; }
	@media (max-width: 1023px) { .use-kit.is-sticky .use-kit-sticky-wrapper { position: static; } }
	.use-kit .benefit-icon svg { display: block; width: 20px; height: 20px; }

	/* outro as a soft-blue callout box (opt-in via 'outro_icon') */
	.use-kit-outro.is-callout { display: flex; align-items: flex-start; gap: var(--space-16); background: var(--primary-light); border-radius: var(--radius-2xl); padding: var(--space-32); margin-top: var(--space-32); }
	.use-kit-outro.is-callout .use-kit-outro-icon { flex: none; line-height: 0; margin-top: 2px; color: var(--primary); }
	.use-kit-outro.is-callout .use-kit-outro-icon svg { display: block; width: 24px; height: 24px; }
	.use-kit-outro.is-callout p { margin: 0; font-size: var(--fs-lg); line-height: var(--lh-relaxed); color: var(--text-body); }
	.use-kit-outro.is-callout p strong { color: var(--ink); font-weight: var(--fw-bold); }

	/* opt-in hover cards on the benefit rows (modifier 'use-kit-cards') */
	.use-kit-cards .benefit-item { box-sizing: border-box; padding: var(--space-16); border-radius: var(--radius-2xl); transition: background .25s ease, box-shadow .25s ease; }
	.use-kit-cards .benefit-item:hover { background: var(--surface); box-shadow: var(--shadow-card); }
	@media (prefers-reduced-motion: reduce) { .use-kit-cards .benefit-item { transition: none; } }
</style>
<?php endif; ?>
<section class="<?php echo esc_attr( $section_class ); ?>">
	<div class="container">
		<div class="why-ethora-grid"<?php echo $grid_style ? ' style="' . esc_attr( $grid_style ) . '"' : ''; ?>>
			<div class="use-kit-sticky-wrapper">
				<?php if ( $bg_image ) : ?>
					<img src="<?php echo esc_url( $resolve( $bg_image, 'images/' ) ); ?>" alt="" class="use-kit-background" />
				<?php endif; ?>
				<?php if ( $image ) : ?>
					<div class="use-kit-large">
						<img src="<?php echo esc_url( $resolve( $image, 'images/' ) ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" />
					</div>
				<?php endif; ?>
			</div>

			<div class="why-ethora-content">
				<?php if ( $label ) : ?>
					<div class="section-label">
						<?php if ( $label_icon ) : ?>
							<img src="<?php echo esc_url( $resolve( $label_icon, 'images/vector/' ) ); ?>" alt="" />
						<?php endif; ?>
						<span><?php echo esc_html( $label ); ?></span>
					</div>
				<?php endif; ?>

				<?php if ( $title ) : ?>
					<h2><?php echo esc_html( $title ); ?></h2>
				<?php endif; ?>

				<?php if ( $description ) : ?>
					<p><?php echo wp_kses_post( $description ); ?></p>
				<?php endif; ?>

				<?php if ( $benefits ) : ?>
					<div class="benefits-list">
						<div class="benefit-item-container">
							<?php
							foreach ( $benefits as $b ) :
								$icon_in   = isset( $b['icon'] ) ? $b['icon'] : '';
								$is_inline = ( false !== strpos( $icon_in, '<svg' ) );  // pass raw inline SVG, or a filename
								$b_icon    = $is_inline ? '' : $resolve( $icon_in, 'images/use-kit/' );
								$b_title   = isset( $b['title'] ) ? $b['title'] : '';
								$b_text    = isset( $b['text'] ) ? $b['text'] : '';
								$b_alt     = isset( $b['alt'] ) ? $b['alt'] : $b_title;
								?>
								<div class="benefit-item">
									<?php if ( $is_inline || $b_icon ) : ?>
										<div class="benefit-icon"<?php echo $benefit_icon_style ? ' style="' . esc_attr( $benefit_icon_style ) . '"' : ''; ?>>
											<?php if ( $is_inline ) : ?>
												<?php echo $icon_in; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted inline SVG ?>
											<?php else : ?>
												<img src="<?php echo esc_url( $b_icon ); ?>" alt="<?php echo esc_attr( $b_alt ); ?>" />
											<?php endif; ?>
										</div>
									<?php endif; ?>
									<div class="benefit-content">
										<?php if ( $b_title ) : ?><h3><?php echo esc_html( $b_title ); ?></h3><?php endif; ?>
										<?php if ( $b_text ) : ?><p><?php echo wp_kses_post( $b_text ); ?></p><?php endif; ?>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( $outro ) : ?>
					<?php if ( $outro_icon ) : ?>
						<div class="use-kit-outro is-callout">
							<span class="use-kit-outro-icon"><?php echo $outro_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted inline SVG ?></span>
							<p><?php echo wp_kses_post( $outro ); ?></p>
						</div>
					<?php else : ?>
						<p class="use-kit-outro"><?php echo wp_kses_post( $outro ); ?></p>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
