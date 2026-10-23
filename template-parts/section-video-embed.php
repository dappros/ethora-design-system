<?php
/**
 * Reusable VIDEO EMBED section (explainer video for a page) + VideoObject JSON-LD.
 * Inserted by the video content factory (ethora-bdsm scripts/glossary_video) on
 * static pages; safe to add by hand too.
 *
 * Usage:
 *   get_template_part( 'template-parts/section', 'video-embed', array(
 *     'video'       => 'GghYyhBSgS0',                 // YouTube id
 *     'title'       => 'What is RCS? ...',            // video title (schema + iframe title)
 *     'description' => 'One-sentence summary',        // schema description
 *     'upload_date' => '2026-08-21T20:15:00+00:00',   // ISO 8601 WITH timezone (GSC requirement)
 *     'duration'    => 'PT2M6S',
 *     'eyebrow'     => 'Watch',                        // optional
 *     'heading'     => 'Prefer to watch? ...',         // optional
 *   ) );
 */
$v = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'video' => '', 'title' => 'Video', 'description' => '', 'upload_date' => '',
		'duration' => '', 'eyebrow' => 'Watch', 'heading' => 'Prefer to watch? The two minute version',
	)
);
if ( empty( $v['video'] ) ) { return; }
$vid = esc_attr( $v['video'] );
$schema = array(
	'@context'     => 'https://schema.org',
	'@type'        => 'VideoObject',
	'name'         => $v['title'],
	'description'  => $v['description'] ?: $v['title'],
	'thumbnailUrl' => "https://i.ytimg.com/vi/{$vid}/maxresdefault.jpg",
	'uploadDate'   => $v['upload_date'],
	'duration'     => $v['duration'],
	'contentUrl'   => "https://www.youtube.com/watch?v={$vid}",
	'embedUrl'     => "https://www.youtube-nocookie.com/embed/{$vid}",
	'publisher'    => array( '@type' => 'Organization', 'name' => 'Ethora', 'url' => 'https://ethora.com/' ),
);
?>
<section class="video-embed-section" aria-label="<?php echo esc_attr( $v['title'] ); ?>">
	<style>
	  .video-embed-section { padding: var(--space-48, 48px) var(--section-x, 24px); }
	  .video-embed-section .vei { max-width: 960px; margin: 0 auto; }
	  .video-embed-section .vei-eyebrow { font-weight: 700; font-size: 13px; letter-spacing: .12em; text-transform: uppercase; color: var(--primary, #0052cd); margin: 0 0 8px; }
	  .video-embed-section .vei-heading { font-weight: 800; font-size: clamp(22px, 3vw, 30px); line-height: 1.15; color: var(--ink, #0e1a33); margin: 0 0 20px; }
	  .video-embed-section .vei-frame { position: relative; width: 100%; aspect-ratio: 16 / 9; border: 1px solid var(--border, #e4e9f2); border-radius: var(--radius-2xl, 24px); overflow: hidden; box-shadow: var(--shadow-card, 0 10px 30px rgba(14,26,51,.08)); background: var(--ink, #0e1a33); }
	  .video-embed-section .vei-frame iframe { position: absolute; inset: 0; width: 100%; height: 100%; border: 0; }
	</style>
	<div class="vei">
		<?php if ( ! empty( $v['eyebrow'] ) ) : ?><p class="vei-eyebrow"><?php echo esc_html( $v['eyebrow'] ); ?></p><?php endif; ?>
		<?php if ( ! empty( $v['heading'] ) ) : ?><h2 class="vei-heading"><?php echo esc_html( $v['heading'] ); ?></h2><?php endif; ?>
		<div class="vei-frame">
			<iframe src="https://www.youtube-nocookie.com/embed/<?php echo $vid; ?>" title="<?php echo esc_attr( $v['title'] ); ?>" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
		</div>
	</div>
	<script type="application/ld+json"><?php echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
</section>
