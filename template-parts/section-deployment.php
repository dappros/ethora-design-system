<?php
/**
 * Reusable DEPLOYMENT / architecture stack.
 *
 * A centred header (eyebrow + title + lead), a row of platform chips, then a
 * dashed "your infrastructure" container that lays the architecture out as native
 * cards: stacked layer groups joined by down-arrows, a highlighted "core" group,
 * and a two-column row of side-by-side groups. Closes with a legend.
 *
 * Replaces the old flat architecture PNG with an on-brand, responsive, tokenised
 * layout. Self-contained: ships its own CSS once per request; every value is a
 * design token (no hardcoded hex/px).
 *
 * Pass props via the 3rd arg of get_template_part(). ALL are optional — the
 * defaults reproduce the Ethora self-hosted stack, so a page usually overrides
 * only 'title' and 'lead':
 *
 *   get_template_part( 'template-parts/section-deployment', null, array(
 *     'eyebrow'         => 'Deployment',
 *     'title'           => 'Your servers. Your data. Your rules.',
 *     'lead'            => 'Self-hosting is the default…',
 *     'platforms_label' => 'The same stack, deployed anywhere',
 *     'platforms'       => array(
 *       array( 'label' => 'AWS', 'icon' => '<svg…>' ),  // icon optional (line SVG, stroke="currentColor")
 *       // …
 *     ),
 *     'vpc_label'       => 'Your infrastructure — data never leaves your VPC',
 *     'vpc_icon'        => '<svg…>',                     // optional (default shield)
 *     'groups'          => array(
 *       array(
 *         'label'    => 'Core services',
 *         'note'     => '— the Ethora engine',   // optional inline note after the label
 *         'badge'    => 'optional',              // optional pill after the label
 *         'tint'     => 'core',                  // '' plain white | 'core' soft-brand group bg
 *         'dashed'   => false,                   // dashed group + card borders (for optional layers)
 *         'dot'      => 'core',                  // marker colour: core | edge | data | optional
 *         'half'     => false,                   // true = share a row with the adjacent half group(s)
 *         'cols'     => 3,                       // 1–3 fixed card columns (omit = auto-fit)
 *         'cards'    => array(
 *           array( 'title' => 'API layer', 'subtitle' => 'Node.js' ),
 *           // …
 *         ),
 *       ),
 *       // …
 *     ),
 *     'legend'          => array(
 *       array( 'label' => 'Ethora core', 'dot' => 'core' ),
 *       // …
 *     ),
 *   ) );
 *
 * @package ethora-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ---- Platform icons ----
// Real provider marks are official brand SVGs (source: Simple Icons), filled with their
// vendor-colour tokens (--logo-*). Non-brand concepts (on-prem, private cloud) stay as
// tidy monochrome line icons in --primary. Colours are applied via CSS classes below.
$dep_i = 'viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"';

// Official brand logos (single-path, filled).
$dep_aws   = '<svg class="dep-logo dep-logo-aws" viewBox="0 0 24 24" aria-hidden="true"><path d="M6.763 10.036c0 .296.032.535.088.71.064.176.144.368.256.576.04.063.056.127.056.183 0 .08-.048.16-.152.24l-.503.335a.383.383 0 0 1-.208.072c-.08 0-.16-.04-.239-.112a2.47 2.47 0 0 1-.287-.375 6.18 6.18 0 0 1-.248-.471c-.622.734-1.405 1.101-2.347 1.101-.67 0-1.205-.191-1.596-.574-.391-.384-.59-.894-.59-1.533 0-.678.239-1.23.726-1.644.487-.415 1.133-.623 1.955-.623.272 0 .551.024.846.064.296.04.6.104.918.176v-.583c0-.607-.127-1.03-.375-1.277-.255-.248-.686-.367-1.3-.367-.28 0-.568.031-.863.103-.295.072-.583.16-.862.272a2.287 2.287 0 0 1-.28.104.488.488 0 0 1-.127.023c-.112 0-.168-.08-.168-.247v-.391c0-.128.016-.224.056-.28a.597.597 0 0 1 .224-.167c.279-.144.614-.264 1.005-.36a4.84 4.84 0 0 1 1.246-.151c.95 0 1.644.216 2.091.647.439.43.662 1.085.662 1.963v2.586zm-3.24 1.214c.263 0 .534-.048.822-.144.287-.096.543-.271.758-.51.128-.152.224-.32.272-.512.047-.191.08-.423.08-.694v-.335a6.66 6.66 0 0 0-.735-.136 6.02 6.02 0 0 0-.75-.048c-.535 0-.926.104-1.19.32-.263.215-.39.518-.39.917 0 .375.095.655.295.846.191.2.47.296.838.296zm6.41.862c-.144 0-.24-.024-.304-.08-.064-.048-.12-.16-.168-.311L7.586 5.55a1.398 1.398 0 0 1-.072-.32c0-.128.064-.2.191-.2h.783c.151 0 .255.025.31.08.065.048.113.16.16.312l1.342 5.284 1.245-5.284c.04-.16.088-.264.151-.312a.549.549 0 0 1 .32-.08h.638c.152 0 .256.025.32.08.063.048.12.16.151.312l1.261 5.348 1.381-5.348c.048-.16.104-.264.16-.312a.52.52 0 0 1 .311-.08h.743c.127 0 .2.065.2.2 0 .04-.009.08-.017.128a1.137 1.137 0 0 1-.056.2l-1.923 6.17c-.048.16-.104.263-.168.311a.51.51 0 0 1-.303.08h-.687c-.151 0-.255-.024-.32-.08-.063-.056-.119-.16-.15-.32l-1.238-5.148-1.23 5.14c-.04.16-.087.264-.15.32-.065.056-.177.08-.32.08zm10.256.215c-.415 0-.83-.048-1.229-.143-.399-.096-.71-.2-.918-.32-.128-.071-.215-.151-.247-.223a.563.563 0 0 1-.048-.224v-.407c0-.167.064-.247.183-.247.048 0 .096.008.144.024.048.016.12.048.2.08.271.12.566.215.878.279.319.064.63.096.95.096.502 0 .894-.088 1.165-.264a.86.86 0 0 0 .415-.758.777.777 0 0 0-.215-.559c-.144-.151-.416-.287-.807-.415l-1.157-.36c-.583-.183-1.014-.454-1.277-.813a1.902 1.902 0 0 1-.4-1.158c0-.335.073-.63.216-.886.144-.255.335-.479.575-.654.24-.184.51-.32.83-.415.32-.096.655-.136 1.006-.136.175 0 .359.008.535.032.183.024.35.056.518.088.16.04.312.08.455.127.144.048.256.096.336.144a.69.69 0 0 1 .24.2.43.43 0 0 1 .071.263v.375c0 .168-.064.256-.184.256a.83.83 0 0 1-.303-.096 3.652 3.652 0 0 0-1.532-.311c-.455 0-.815.071-1.062.223-.248.152-.375.383-.375.71 0 .224.08.416.24.567.159.152.454.304.877.44l1.134.358c.574.184.99.44 1.237.767.247.327.367.702.367 1.117 0 .343-.072.655-.207.926-.144.272-.336.511-.583.703-.248.2-.543.343-.886.447-.36.111-.734.167-1.142.167zM21.698 16.207c-2.626 1.94-6.442 2.969-9.722 2.969-4.598 0-8.74-1.7-11.87-4.526-.247-.223-.024-.527.272-.351 3.384 1.963 7.559 3.153 11.877 3.153 2.914 0 6.114-.607 9.06-1.852.439-.2.814.287.383.607zM22.792 14.961c-.336-.43-2.22-.207-3.074-.103-.255.032-.295-.192-.063-.36 1.5-1.053 3.967-.75 4.254-.399.287.36-.08 2.826-1.485 4.007-.215.184-.423.088-.327-.151.32-.79 1.03-2.57.695-2.994z"/></svg>';
$dep_gcp   = '<svg class="dep-logo dep-logo-gcp" viewBox="0 0 24 24" aria-hidden="true"><path d="M12.19 2.38a9.344 9.344 0 0 0-9.234 6.893c.053-.02-.055.013 0 0-3.875 2.551-3.922 8.11-.247 10.941l.006-.007-.007.03a6.717 6.717 0 0 0 4.077 1.356h5.173l.03.03h5.192c6.687.053 9.376-8.605 3.835-12.35a9.365 9.365 0 0 0-2.821-4.552l-.043.043.006-.05A9.344 9.344 0 0 0 12.19 2.38zm-.358 4.146c1.244-.04 2.518.368 3.486 1.15a5.186 5.186 0 0 1 1.862 4.078v.518c3.53-.07 3.53 5.262 0 5.193h-5.193l-.008.009v-.04H6.785a2.59 2.59 0 0 1-1.067-.23h.001a2.597 2.597 0 1 1 3.437-3.437l3.013-3.012A6.747 6.747 0 0 0 8.11 8.24c.018-.01.04-.026.054-.023a5.186 5.186 0 0 1 3.67-1.69z"/></svg>';
$dep_azure = '<svg class="dep-logo dep-logo-azure" viewBox="0 0 24 24" aria-hidden="true"><path d="M22.379 23.343a1.62 1.62 0 0 0 1.536-2.14v.002L17.35 1.76A1.62 1.62 0 0 0 15.816.657H8.184A1.62 1.62 0 0 0 6.65 1.76L.086 21.204a1.62 1.62 0 0 0 1.536 2.139h4.741a1.62 1.62 0 0 0 1.535-1.103l.977-2.892 4.947 3.675c.28.208.618.32.966.32m-3.084-12.531 3.624 10.739a.54.54 0 0 1-.51.713v-.001h-.03a.54.54 0 0 1-.322-.106l-9.287-6.9h4.853m6.313 7.006c.116-.326.13-.694.007-1.058L9.79 1.76a1.722 1.722 0 0 0-.007-.02h6.034a.54.54 0 0 1 .512.366l6.562 19.445a.54.54 0 0 1-.338.684"/></svg>';

// Non-brand concepts — monochrome line icons.
$dep_server = '<svg class="dep-ico" ' . $dep_i . '><rect x="3" y="4" width="18" height="7" rx="1.5"/><rect x="3" y="13" width="18" height="7" rx="1.5"/><line x1="7" y1="7.5" x2="7" y2="7.5"/><line x1="7" y1="16.5" x2="7" y2="16.5"/></svg>';
$dep_bag    = '<svg class="dep-ico dep-ico-aws" ' . $dep_i . '><path d="M6 2 3 6v13a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>';
$dep_lock   = '<svg class="dep-ico" ' . $dep_i . '><path d="M17.5 19a4.5 4.5 0 0 0 .5-8.98A6 6 0 0 0 6.34 9 4.5 4.5 0 0 0 7 19z"/><rect x="9.5" y="12.5" width="5" height="4" rx="1"/><path d="M10.5 12.5v-.8a1.5 1.5 0 0 1 3 0v.8"/></svg>';
$dep_shield = '<svg ' . $dep_i . '><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>';

$dep = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'eyebrow'         => '',
		'title'           => 'Your servers. Your data. Your rules.',
		'lead'            => '',
		'platforms_label' => '',
		'platforms'       => array(
			array( 'label' => 'AWS', 'icon' => $dep_aws ),
			array( 'label' => 'Google Cloud', 'icon' => $dep_gcp ),
			array( 'label' => 'Azure', 'icon' => $dep_azure ),
			array( 'label' => 'On-premises', 'icon' => $dep_server ),
			array( 'label' => 'AWS Marketplace', 'icon' => $dep_bag ),
			array( 'label' => 'Private cloud', 'icon' => $dep_lock ),
		),
		'vpc_label'       => 'Your infrastructure — data never leaves your VPC',
		'vpc_icon'        => $dep_shield,
		'groups'          => array(
			array(
				'label' => 'Clients',
				'dot'   => 'edge',
				'cols'  => 2,
				'cards' => array(
					array( 'title' => 'Admin Dashboard', 'subtitle' => 'React.js' ),
					array( 'title' => 'Apps &amp; SDK clients', 'subtitle' => 'Web, iOS, Android' ),
				),
			),
			array(
				'label' => 'Edge &middot; Ingress',
				'dot'   => 'edge',
				'cols'  => 1,
				'cards' => array(
					array( 'title' => 'Nginx proxy', 'subtitle' => 'TLS termination &amp; routing' ),
				),
			),
			array(
				'label' => 'Core services',
				'note'  => '&mdash; the Ethora engine',
				'tint'  => 'core',
				'dot'   => 'core',
				'cols'  => 3,
				'cards' => array(
					array( 'title' => 'API layer', 'subtitle' => 'Node.js' ),
					array( 'title' => 'XMPP server', 'subtitle' => 'Ejabberd + modules' ),
					array( 'title' => 'Realtime delivery', 'subtitle' => 'Centrifugo' ),
					array( 'title' => 'File storage', 'subtitle' => 'MinIO' ),
					array( 'title' => 'Push notifications', 'subtitle' => 'Firebase' ),
					array( 'title' => 'Background workers', 'subtitle' => 'Node.js jobs' ),
				),
			),
			array(
				'label' => 'Data &amp; infrastructure',
				'dot'   => 'data',
				'half'  => true,
				'cols'  => 2,
				'cards' => array(
					array( 'title' => 'MongoDB', 'subtitle' => 'App data' ),
					array( 'title' => 'Redis', 'subtitle' => 'Cache &amp; sessions' ),
					array( 'title' => 'MySQL / Postgres', 'subtitle' => 'XMPP data' ),
				),
			),
			array(
				'label'  => 'Advanced services',
				'badge'  => 'optional',
				'dashed' => true,
				'dot'    => 'optional',
				'half'   => true,
				'cols'   => 2,
				'cards'  => array(
					array( 'title' => 'AI service', 'subtitle' => 'RAG, MCP &middot; Node/Python' ),
					array( 'title' => 'Video calls', 'subtitle' => 'WebRTC &middot; TURN/STUN' ),
					array( 'title' => 'Bots framework', 'subtitle' => 'Node.js / Python' ),
					array( 'title' => 'Web3 module', 'subtitle' => 'L2 nodes / bridge' ),
				),
			),
		),
		'legend'          => array(
			array( 'label' => 'Ethora core', 'dot' => 'core' ),
			array( 'label' => 'Clients &amp; edge', 'dot' => 'edge' ),
			array( 'label' => 'Data stores', 'dot' => 'data' ),
			array( 'label' => 'Optional', 'dot' => 'optional' ),
		),
	)
);

// Group the layers into rows: consecutive 'half' groups share a row; the rest are full-width.
$dep_rows    = array();
$dep_pending = array();
foreach ( (array) $dep['groups'] as $dep_g ) {
	if ( ! empty( $dep_g['half'] ) ) {
		$dep_pending[] = $dep_g;
		continue;
	}
	if ( $dep_pending ) {
		$dep_rows[]  = array( 'type' => 'row', 'groups' => $dep_pending );
		$dep_pending = array();
	}
	$dep_rows[] = array( 'type' => 'full', 'group' => $dep_g );
}
if ( $dep_pending ) {
	$dep_rows[] = array( 'type' => 'row', 'groups' => $dep_pending );
}

// Renders a single group card block (guarded so multiple instances don't redeclare).
if ( ! function_exists( 'ethora_render_dep_group' ) ) {
	function ethora_render_dep_group( $g ) {
		$tint   = isset( $g['tint'] ) && 'core' === $g['tint'];
		$dashed = ! empty( $g['dashed'] );
		$dot    = isset( $g['dot'] ) ? $g['dot'] : 'edge';
		$cols   = isset( $g['cols'] ) ? (int) $g['cols'] : 0;
		$cols_c = ( $cols >= 1 && $cols <= 3 ) ? ' cols-' . $cols : '';
		$mark   = 'optional' === $dot ? ' is-optional' : ' is-' . esc_attr( $dot );
		?>
		<div class="dep-group<?php echo $tint ? ' is-core' : ''; ?><?php echo $dashed ? ' is-dashed' : ''; ?>">
			<div class="dep-group-head">
				<span class="dep-group-label"><?php echo wp_kses_post( $g['label'] ); ?></span>
				<?php if ( ! empty( $g['note'] ) ) : ?><span class="dep-group-note"><?php echo wp_kses_post( $g['note'] ); ?></span><?php endif; ?>
				<?php if ( ! empty( $g['badge'] ) ) : ?><span class="dep-badge"><?php echo esc_html( $g['badge'] ); ?></span><?php endif; ?>
			</div>
			<div class="dep-cards<?php echo esc_attr( $cols_c ); ?>">
				<?php foreach ( (array) $g['cards'] as $c ) : ?>
				<div class="dep-card<?php echo $dashed ? ' is-dashed' : ''; ?>">
					<span class="dep-mark<?php echo $mark; ?>"></span>
					<span class="dep-card-txt">
						<span class="dep-card-title"><?php echo wp_kses_post( $c['title'] ); ?></span>
						<?php if ( ! empty( $c['subtitle'] ) ) : ?><span class="dep-card-sub"><?php echo wp_kses_post( $c['subtitle'] ); ?></span><?php endif; ?>
					</span>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}

$dep_arrow = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="6 13 12 19 18 13"/></svg>';

// Emit the shared CSS only once per request.
$dep_assets = empty( $GLOBALS['dep_assets'] );
if ( $dep_assets ) {
	$GLOBALS['dep_assets'] = true;
}
?>
<?php if ( $dep_assets ) : ?>
<style>
  /* DEPLOYMENT stack — native architecture layout. Tokens only. */
  .dep-section { padding: var(--section-y) var(--section-x); }
  .dep-section, .dep-section *, .dep-section *::before, .dep-section *::after { box-sizing: border-box; }
  .dep-wrap { max-width: var(--content-max); margin: 0 auto; }
  .dep-head { max-width: var(--container-md); margin: 0 auto; text-align: center; }
  .dep-eyebrow { font-weight: var(--fw-semibold); font-size: var(--fs-eyebrow); letter-spacing: var(--tracking-wide); text-transform: uppercase; color: var(--primary); margin: 0 0 var(--space-16); }
  .dep-head h2 { font-weight: var(--fw-bold); font-size: var(--fs-h2); line-height: var(--lh-heading); letter-spacing: var(--tracking-snug); color: var(--ink); margin: 0; }
  .dep-lead { font-size: var(--fs-lg); line-height: var(--lh-relaxed); color: var(--text-body); margin: var(--space-16) auto 0; max-width: var(--measure); }

  /* platform chips */
  .dep-plats-label { text-align: center; font-weight: var(--fw-semibold); font-size: var(--fs-eyebrow); letter-spacing: var(--tracking-wide); text-transform: uppercase; color: var(--text-muted); margin: var(--space-32) 0 var(--space-16); }
  .dep-plats { display: flex; flex-wrap: wrap; justify-content: center; gap: var(--space-16); }
  .dep-plat { display: inline-flex; align-items: center; gap: var(--space-8); background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-pill); padding: var(--space-8) var(--space-16); font-size: var(--fs-md); font-weight: var(--fw-semibold); color: var(--ink); }
  .dep-plat svg { width: 20px; height: 20px; flex: none; }
  /* real provider logos (filled) in their official vendor colours */
  .dep-logo-aws { fill: var(--logo-aws); }
  .dep-logo-gcp { fill: var(--logo-gcp); }
  .dep-logo-azure { fill: var(--logo-azure); }
  /* line icons for non-brand concepts */
  .dep-ico { color: var(--primary); }
  .dep-ico-aws { color: var(--logo-aws); }

  /* VPC container */
  .dep-vpc { position: relative; margin-top: var(--space-48); border: 1px dashed var(--border-strong); border-radius: var(--radius-3xl); padding: clamp(var(--space-32), 4vw, var(--space-48)); background: var(--primary-tint-05); }
  .dep-vpc-badge { position: absolute; top: 0; left: 50%; transform: translate(-50%, -50%); display: inline-flex; align-items: center; gap: var(--space-8); background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-pill); padding: var(--space-8) var(--space-16); font-weight: var(--fw-semibold); font-size: var(--fs-eyebrow); letter-spacing: var(--tracking-wide); text-transform: uppercase; color: var(--primary); white-space: nowrap; max-width: calc(100% - var(--space-32)); overflow: hidden; text-overflow: ellipsis; }
  .dep-vpc-badge svg { width: 15px; height: 15px; flex: none; }

  /* vertical stack of layers + connectors */
  .dep-stack { display: flex; flex-direction: column; gap: var(--space-16); }
  .dep-arrow { display: flex; justify-content: center; color: var(--brand-300); }

  /* a layer group */
  .dep-group { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-2xl); padding: var(--space-32); }
  .dep-group.is-core { background: var(--tint); border-color: var(--primary-tint-10); }
  .dep-group.is-dashed { border-style: dashed; background: transparent; }
  .dep-group-head { display: flex; align-items: center; flex-wrap: wrap; gap: var(--space-8); margin-bottom: var(--space-16); }
  .dep-group-label { font-weight: var(--fw-bold); font-size: var(--fs-xs); letter-spacing: var(--tracking-wide); text-transform: uppercase; color: var(--text-caption); }
  .dep-group.is-core .dep-group-label { color: var(--primary); }
  .dep-group-note { font-size: var(--fs-sm); color: var(--text-caption); }
  .dep-badge { font-size: var(--fs-xs); font-weight: var(--fw-semibold); color: var(--text-caption); background: var(--tint); border-radius: var(--radius-pill); padding: 2px var(--space-8); }

  /* cards inside a group */
  .dep-cards { display: grid; gap: var(--space-16); grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); }
  .dep-cards.cols-1 { grid-template-columns: 1fr; }
  .dep-cards.cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  .dep-cards.cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
  .dep-card { display: flex; align-items: flex-start; gap: var(--space-8); background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-xl); padding: var(--space-16); }
  .dep-card.is-dashed { border-style: dashed; background: transparent; }
  .dep-mark { flex: none; width: 9px; height: 9px; border-radius: var(--radius-pill); margin-top: 6px; }
  .dep-mark.is-core { background: var(--primary); }
  .dep-mark.is-edge { background: var(--brand-300); }
  .dep-mark.is-data { background: var(--green); }
  .dep-mark.is-optional { background: transparent; border: 1px dashed var(--text-muted); }
  .dep-card-txt { display: flex; flex-direction: column; min-width: 0; }
  .dep-card-title { font-weight: var(--fw-semibold); font-size: var(--fs-md); color: var(--ink); line-height: var(--lh-snug); }
  .dep-card.is-dashed .dep-card-title { color: var(--text-body); }
  .dep-card-sub { font-size: var(--fs-sm); color: var(--text-caption); margin-top: 2px; }

  /* two-column row (side-by-side half groups) */
  .dep-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: var(--space-16); }

  /* legend */
  .dep-legend { display: flex; flex-wrap: wrap; justify-content: center; gap: var(--space-32); margin-top: var(--space-32); }
  .dep-legend-item { display: inline-flex; align-items: center; gap: var(--space-8); font-size: var(--fs-sm); color: var(--text-caption); }
  .dep-legend-item .dep-mark { margin-top: 0; }

  @media (max-width: 640px) {
    .dep-cards.cols-2, .dep-cards.cols-3 { grid-template-columns: 1fr; }
    .dep-vpc { margin-top: var(--space-64); }
    .dep-vpc-badge { white-space: normal; text-align: center; line-height: var(--lh-snug); }
  }
</style>
<?php endif; ?>

<section class="dep-section">
  <div class="dep-wrap">
    <div class="dep-head">
      <?php if ( $dep['eyebrow'] ) : ?><p class="dep-eyebrow"><?php echo esc_html( $dep['eyebrow'] ); ?></p><?php endif; ?>
      <?php if ( $dep['title'] ) : ?><h2><?php echo wp_kses_post( $dep['title'] ); ?></h2><?php endif; ?>
      <?php if ( $dep['lead'] ) : ?><p class="dep-lead"><?php echo wp_kses_post( $dep['lead'] ); ?></p><?php endif; ?>
    </div>

    <?php if ( ! empty( $dep['platforms'] ) ) : ?>
    <?php if ( $dep['platforms_label'] ) : ?><p class="dep-plats-label"><?php echo esc_html( $dep['platforms_label'] ); ?></p><?php endif; ?>
    <div class="dep-plats">
      <?php foreach ( $dep['platforms'] as $p ) : ?>
      <span class="dep-plat"><?php if ( ! empty( $p['icon'] ) ) { echo $p['icon']; } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted inline SVG ?><?php echo esc_html( $p['label'] ); ?></span>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if ( ! empty( $dep_rows ) ) : ?>
    <div class="dep-vpc">
      <?php if ( $dep['vpc_label'] ) : ?>
      <span class="dep-vpc-badge"><?php if ( $dep['vpc_icon'] ) { echo $dep['vpc_icon']; } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted inline SVG ?><?php echo esc_html( $dep['vpc_label'] ); ?></span>
      <?php endif; ?>
      <div class="dep-stack">
        <?php
        foreach ( $dep_rows as $ri => $row ) :
			$prev = $ri > 0 ? $dep_rows[ $ri - 1 ] : null;
			// A down-arrow connects two consecutive full-width layers.
			if ( 'full' === $row['type'] && $prev && 'full' === $prev['type'] ) :
				?>
        <div class="dep-arrow" aria-hidden="true"><?php echo $dep_arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted inline SVG ?></div>
				<?php
			endif;
			if ( 'full' === $row['type'] ) {
				ethora_render_dep_group( $row['group'] );
			} else {
				echo '<div class="dep-row">';
				foreach ( $row['groups'] as $hg ) {
					ethora_render_dep_group( $hg );
				}
				echo '</div>';
			}
		endforeach;
        ?>
      </div>
    </div>
    <?php endif; ?>

    <?php if ( ! empty( $dep['legend'] ) ) : ?>
    <div class="dep-legend">
      <?php foreach ( $dep['legend'] as $l ) :
			$ld = isset( $l['dot'] ) ? $l['dot'] : 'core';
			$lm = 'optional' === $ld ? 'is-optional' : 'is-' . esc_attr( $ld );
			?>
      <span class="dep-legend-item"><span class="dep-mark <?php echo $lm; ?>"></span><?php echo wp_kses_post( $l['label'] ); ?></span>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>
