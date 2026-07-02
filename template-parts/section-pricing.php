<?php
/**
 * Shared pricing SECTION for other pages (self-hosted, SDK pages, …).
 * Renders the reusable Variant-A cards with the compact "Save Months…" header.
 * The full /pricing/ page uses the same cards but with its own hero + Why + CTA + FAQ,
 * so the section and the page look distinct.
 */
get_template_part(
	'template-parts/section',
	'pricing-cards',
	array(
		'eyebrow'     => 'Pricing',
		'heading'     => 'Save Months of Work with Ethora',
		'subheading'  => 'Build any chat use case into your product — in hours.',
		'show_header' => true,
		'bg'          => 'alt',
	)
);
