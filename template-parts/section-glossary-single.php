<?php
/**
 * Reusable SINGLE glossary-term layout.
 *
 * Renders one glossary entry: a back-to-list button, an optional faded watermark,
 * the term as an <h1>, a lead paragraph, then a body of subheading + rich blocks
 * (paragraphs, ordered/unordered lists, endpoint code chips), and an optional
 * "related terms" strip. Tokens only (css/tokens.css); scoped to .gterm.
 *
 * Usage:
 *   get_template_part( 'template-parts/section', 'glossary-single', array(
 *     'back_url'  => '/glossary/',
 *     'back_label'=> 'All terms',
 *     'eyebrow'   => 'Glossary',
 *     'watermark' => 'API',            // short faded backdrop behind the H1 (optional)
 *     'term'      => 'API (Application Programming Interface)',
 *     'lead'      => 'A set of rules and protocols that let software talk to each other.',
 *     'sections'  => array(
 *       array(
 *         'heading' => 'General definition',
 *         'blocks'  => array(
 *           array( 'p'  => 'Paragraph HTML&hellip;' ),
 *           array( 'ol' => array( 'First item', 'Second item' ) ),
 *           array( 'ul' => array( 'Bullet one', 'Bullet two' ) ),
 *           array( 'code' => 'https://api.chat.ethora.com/api-docs/', 'code_label' => 'Swagger' ),
 *           array( 'img' => 'https://…/shot.png', 'alt' => 'Alt text', 'caption' => 'Optional caption', 'w' => 2048, 'h' => 1241 ), // URL or theme-images path
 *           array( 'video' => 'GghYyhBSgS0', 'title' => 'What is RCS?', 'caption' => 'Optional caption' ), // YouTube video id, privacy-enhanced lazy embed
 *         ),
 *       ),
 *     ),
 *     'related'   => array(
 *       array( 'label' => 'Chat SDK', 'url' => 'https://ethora.com/glossary/chat-sdk/' ),
 *     ),
 *   ) );
 */

$g = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'back_url'     => '/glossary/',
		'back_label'   => 'All terms',
		'eyebrow'      => 'Glossary',
		'watermark'    => '',
		'illustration' => '',   // key into the hero illustration library (e.g. 'api', 'ai') — replaces the watermark
		'term'         => '',
		'lead'         => '',
		'sections'     => array(),
		'related'      => array(),
	)
);

/**
 * Hero illustration library — flat, on-brand inline SVGs keyed by term.
 * Tokens only (fills reference css custom properties, which inline SVG inherits).
 * Returns '' for an unknown/empty key so the hero falls back to the watermark.
 */
if ( ! function_exists( 'ethora_gterm_illustration' ) ) :
	function ethora_gterm_illustration( $key ) {
		$key    = strtolower( (string) $key );
		$sparkle = 'M0 -10 C1.4 -3.4 3.4 -1.4 10 0 C3.4 1.4 1.4 3.4 0 10 C-1.4 3.4 -3.4 1.4 -10 0 C-3.4 -1.4 -1.4 -3.4 0 -10 Z';

		if ( 'api' === $key ) {
			return '<svg class="gterm-svg" viewBox="0 0 360 320" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="A client application exchanging requests and responses with the Ethora API server">
			  <!-- request / response connector (behind the pill) -->
			  <line x1="104" y1="150" x2="232" y2="196" stroke="var(--primary)" stroke-width="2.5" stroke-linecap="round"/>
			  <polygon points="232,196 219,197 223,186" fill="var(--primary)"/>
			  <polygon points="104,150 113,160 117,148" fill="var(--primary)"/>

			  <!-- Client app card -->
			  <text class="gterm-svg-cap" x="14" y="20">CLIENT APP</text>
			  <rect x="12" y="28" width="184" height="116" rx="18" fill="var(--white)" stroke="var(--border)" stroke-width="1.5"/>
			  <circle cx="34" cy="50" r="4" fill="var(--brand-100)"/>
			  <circle cx="48" cy="50" r="4" fill="var(--brand-100)"/>
			  <circle cx="62" cy="50" r="4" fill="var(--brand-100)"/>
			  <line x1="12" y1="68" x2="196" y2="68" stroke="var(--hairline)" stroke-width="1.5"/>
			  <rect x="30" y="84"  width="96"  height="11" rx="5.5" fill="var(--primary-light)"/>
			  <rect x="30" y="104" width="140" height="11" rx="5.5" fill="var(--primary-light)"/>
			  <rect x="30" y="124" width="72"  height="11" rx="5.5" fill="var(--primary-light)"/>

			  <!-- API server card -->
			  <text class="gterm-svg-cap" x="166" y="168">API SERVER</text>
			  <rect x="164" y="176" width="184" height="120" rx="18" fill="var(--primary-light)" stroke="var(--border)" stroke-width="1.5"/>
			  <text class="gterm-svg-brace" x="300" y="150">{ }</text>
			  <g>
			    <rect x="180" y="196" width="152" height="26" rx="8" fill="var(--white)"/>
			    <circle cx="196" cy="209" r="4" fill="var(--green)"/>
			    <rect x="210" y="205" width="80"  height="8" rx="4" fill="var(--brand-100)"/>
			    <rect x="180" y="228" width="152" height="26" rx="8" fill="var(--white)"/>
			    <circle cx="196" cy="241" r="4" fill="var(--green)"/>
			    <rect x="210" y="237" width="104" height="8" rx="4" fill="var(--brand-100)"/>
			    <rect x="180" y="260" width="152" height="26" rx="8" fill="var(--white)"/>
			    <circle cx="196" cy="273" r="4" fill="var(--green)"/>
			    <rect x="210" y="269" width="64"  height="8" rx="4" fill="var(--brand-100)"/>
			  </g>

			  <!-- endpoint pill sitting on the connector -->
			  <rect x="112" y="156" width="136" height="34" rx="17" fill="var(--white)" stroke="var(--primary)" stroke-width="1.5"/>
			  <text class="gterm-svg-mono" x="180" y="178" text-anchor="middle">GET /users</text>
			</svg>';
		}

		if ( 'ai' === $key ) {
			return '<svg class="gterm-svg" viewBox="0 0 360 320" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="An AI agent powered by a language model replying inside a chat">
			  <!-- model links + nodes behind the agent -->
			  <g stroke="var(--brand-100)" stroke-width="2">
			    <line x1="180" y1="72" x2="60"  y2="40"/>
			    <line x1="180" y1="72" x2="300" y2="44"/>
			    <line x1="180" y1="72" x2="48"  y2="122"/>
			    <line x1="180" y1="72" x2="312" y2="122"/>
			  </g>
			  <g fill="var(--white)" stroke="var(--brand-100)" stroke-width="2">
			    <circle cx="60"  cy="40"  r="9"/>
			    <circle cx="300" cy="44"  r="9"/>
			    <circle cx="48"  cy="122" r="9"/>
			    <circle cx="312" cy="122" r="9"/>
			  </g>

			  <!-- agent node -->
			  <rect x="140" y="32" width="80" height="80" rx="26" fill="var(--primary)"/>
			  <path transform="translate(180 72) scale(1.7)" d="' . $sparkle . '" fill="var(--white)"/>
			  <path transform="translate(226 40) scale(0.75)" d="' . $sparkle . '" fill="var(--orange)"/>

			  <!-- incoming (user) bubble -->
			  <rect x="16" y="196" width="150" height="92" rx="20" fill="var(--white)" stroke="var(--border)" stroke-width="1.5"/>
			  <rect x="32" y="220" width="96"  height="10" rx="5" fill="var(--primary-light)"/>
			  <rect x="32" y="240" width="118" height="10" rx="5" fill="var(--primary-light)"/>
			  <rect x="32" y="260" width="72"  height="10" rx="5" fill="var(--primary-light)"/>

			  <!-- AI reply bubble -->
			  <path d="M318 268 l24 24 l-20 -2 z" fill="var(--primary)"/>
			  <rect x="194" y="180" width="152" height="96" rx="20" fill="var(--primary)"/>
			  <path transform="translate(216 206) scale(0.85)" d="' . $sparkle . '" fill="var(--white)"/>
			  <rect x="236" y="200" width="92"  height="10" rx="5" fill="var(--white)"/>
			  <rect x="212" y="222" width="118" height="10" rx="5" fill="var(--white)" opacity="0.85"/>
			  <rect x="212" y="242" width="84"  height="10" rx="5" fill="var(--white)" opacity="0.85"/>
			</svg>';
		}

		if ( 'api-integration' === $key ) {
			return '<svg class="gterm-svg" viewBox="0 0 360 320" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Two systems connected by a central sync badge that keeps their data in sync">
			  <!-- System A card -->
			  <text class="gterm-svg-cap" x="20" y="86">SYSTEM A</text>
			  <rect x="20" y="96" width="116" height="128" rx="18" fill="var(--white)" stroke="var(--border)" stroke-width="1.5"/>
			  <rect x="34" y="110" width="20" height="20" rx="6" fill="var(--primary)"/>
			  <rect x="62" y="116" width="52" height="9" rx="4.5" fill="var(--primary-light)"/>
			  <line x1="20" y1="140" x2="136" y2="140" stroke="var(--hairline)" stroke-width="1.5"/>
			  <rect x="34" y="152" width="84" height="8" rx="4" fill="var(--primary-light)"/>
			  <rect x="34" y="170" width="60" height="8" rx="4" fill="var(--primary-light)"/>
			  <rect x="34" y="188" width="72" height="8" rx="4" fill="var(--primary-light)"/>

			  <!-- System B card -->
			  <text class="gterm-svg-cap" x="224" y="86">SYSTEM B</text>
			  <rect x="224" y="96" width="116" height="128" rx="18" fill="var(--primary-light)" stroke="var(--border)" stroke-width="1.5"/>
			  <rect x="238" y="110" width="20" height="20" rx="6" fill="var(--white)"/>
			  <rect x="266" y="116" width="52" height="9" rx="4.5" fill="var(--white)"/>
			  <line x1="224" y1="140" x2="340" y2="140" stroke="var(--hairline)" stroke-width="1.5"/>
			  <rect x="238" y="152" width="84" height="8" rx="4" fill="var(--white)"/>
			  <rect x="238" y="170" width="60" height="8" rx="4" fill="var(--white)"/>
			  <rect x="238" y="188" width="72" height="8" rx="4" fill="var(--white)"/>

			  <!-- Plug connectors where they meet -->
			  <rect x="130" y="152" width="16" height="16" rx="4" fill="var(--primary)"/>
			  <rect x="214" y="152" width="16" height="16" rx="4" fill="var(--primary)"/>

			  <!-- Central sync badge -->
			  <circle cx="180" cy="160" r="38" fill="var(--primary)"/>
			  <path d="M180 138 A22 22 0 1 1 158 160" fill="none" stroke="var(--white)" stroke-width="3"/>
			  <polygon points="158,169 151,159 165,159" fill="var(--white)"/>
			  <path d="M180 182 A22 22 0 1 1 202 160" fill="none" stroke="var(--white)" stroke-width="3"/>
			  <polygon points="202,151 195,161 209,161" fill="var(--white)"/>
			</svg>';
		}

		if ( 'app-owner' === $key ) {
			return '<svg class="gterm-svg" viewBox="0 0 360 320" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="An admin dashboard panel with an owner avatar and control toggles">
			  <text class="gterm-svg-cap" x="40" y="34">ADMIN PANEL</text>

			  <!-- Dashboard card -->
			  <rect x="40" y="44" width="280" height="232" rx="18" fill="var(--white)" stroke="var(--border)" stroke-width="1.5"/>

			  <!-- Owner avatar with crown -->
			  <circle cx="72" cy="76" r="20" fill="var(--primary)"/>
			  <path d="M60 80 L60 70 L66 75 L72 66 L78 75 L84 70 L84 80 Z" fill="var(--white)"/>
			  <circle cx="87" cy="90" r="5" fill="var(--green)" stroke="var(--white)" stroke-width="2"/>

			  <!-- Header title bars -->
			  <rect x="106" y="64" width="104" height="11" rx="5.5" fill="var(--primary-light)"/>
			  <rect x="106" y="82" width="64"  height="9"  rx="4.5" fill="var(--brand-100)"/>
			  <line x1="40" y1="112" x2="320" y2="112" stroke="var(--hairline)" stroke-width="1.5"/>

			  <!-- Control rows -->
			  <rect x="60" y="126" width="240" height="34" rx="10" fill="var(--primary-light)"/>
			  <rect x="76" y="138" width="120" height="10" rx="5" fill="var(--white)"/>
			  <rect x="258" y="136" width="30" height="14" rx="7" fill="var(--primary)"/>
			  <circle cx="281" cy="143" r="5" fill="var(--white)"/>

			  <rect x="60" y="170" width="240" height="34" rx="10" fill="var(--primary-light)"/>
			  <rect x="76" y="182" width="92" height="10" rx="5" fill="var(--white)"/>
			  <rect x="258" y="180" width="30" height="14" rx="7" fill="var(--white)" stroke="var(--border)" stroke-width="1.5"/>
			  <circle cx="267" cy="187" r="5" fill="var(--brand-100)"/>

			  <rect x="60" y="214" width="240" height="34" rx="10" fill="var(--primary-light)"/>
			  <rect x="76" y="226" width="134" height="10" rx="5" fill="var(--white)"/>
			  <rect x="258" y="224" width="30" height="14" rx="7" fill="var(--primary)"/>
			  <circle cx="281" cy="231" r="5" fill="var(--white)"/>
			</svg>';
		}

		if ( 'app' === $key ) {
			return '<svg class="gterm-svg" viewBox="0 0 360 320" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="The Ethora application shown as a phone with a grid of app tiles and a chat tile highlighted">
			  <text class="gterm-svg-cap" x="110" y="26">APP</text>

			  <!-- Phone frame -->
			  <rect x="110" y="36" width="140" height="252" rx="22" fill="var(--white)" stroke="var(--border)" stroke-width="1.5"/>

			  <!-- Top bar dots -->
			  <circle cx="130" cy="58" r="3.5" fill="var(--brand-100)"/>
			  <circle cx="146" cy="58" r="3.5" fill="var(--brand-100)"/>
			  <circle cx="162" cy="58" r="3.5" fill="var(--brand-100)"/>
			  <line x1="110" y1="74" x2="250" y2="74" stroke="var(--hairline)" stroke-width="1.5"/>

			  <!-- App tiles -->
			  <g fill="var(--primary-light)">
			    <rect x="119" y="92"  width="30" height="30" rx="8"/>
			    <rect x="165" y="92"  width="30" height="30" rx="8"/>
			    <rect x="211" y="92"  width="30" height="30" rx="8"/>
			    <rect x="119" y="136" width="30" height="30" rx="8"/>
			    <rect x="211" y="136" width="30" height="30" rx="8"/>
			    <rect x="119" y="180" width="30" height="30" rx="8"/>
			    <rect x="165" y="180" width="30" height="30" rx="8"/>
			    <rect x="211" y="180" width="30" height="30" rx="8"/>
			  </g>

			  <!-- Ethora chat tile (focal) -->
			  <rect x="165" y="136" width="30" height="30" rx="8" fill="var(--primary)"/>
			  <rect x="170" y="144" width="20" height="13" rx="4" fill="var(--white)"/>
			  <path d="M174 157 l0 6 l8 -6 z" fill="var(--white)"/>

			  <!-- Home indicator -->
			  <rect x="160" y="256" width="40" height="6" rx="3" fill="var(--brand-100)"/>
			</svg>';
		}

		if ( 'backend' === $key ) {
			return '<svg class="gterm-svg" viewBox="0 0 360 320" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="A backend server stack with layered service cards a database and a configuration gear">
			  <text class="gterm-svg-cap" x="32" y="52">SERVER</text>

			  <!-- Connector bus: stack to database -->
			  <g stroke="var(--border)" stroke-width="1.5">
			    <line x1="204" y1="95"  x2="214" y2="95"/>
			    <line x1="204" y1="155" x2="214" y2="155"/>
			    <line x1="204" y1="215" x2="214" y2="215"/>
			    <line x1="214" y1="95"  x2="214" y2="215"/>
			    <line x1="214" y1="150" x2="244" y2="150"/>
			    <line x1="214" y1="215" x2="184" y2="260"/>
			  </g>

			  <!-- Layer cards -->
			  <rect x="32" y="72" width="172" height="46" rx="12" fill="var(--primary-light)" stroke="var(--border)" stroke-width="1.5"/>
			  <circle cx="54" cy="95" r="5" fill="var(--green)"/>
			  <rect x="72" y="85"  width="104" height="9" rx="4.5" fill="var(--white)"/>
			  <rect x="72" y="101" width="72"  height="8" rx="4"   fill="var(--brand-100)"/>

			  <rect x="32" y="132" width="172" height="46" rx="12" fill="var(--primary-light)" stroke="var(--border)" stroke-width="1.5"/>
			  <circle cx="54" cy="155" r="5" fill="var(--green)"/>
			  <rect x="72" y="145" width="92" height="9" rx="4.5" fill="var(--white)"/>
			  <rect x="72" y="161" width="60" height="8" rx="4"   fill="var(--brand-100)"/>

			  <rect x="32" y="192" width="172" height="46" rx="12" fill="var(--primary-light)" stroke="var(--border)" stroke-width="1.5"/>
			  <circle cx="54" cy="215" r="5" fill="var(--green)"/>
			  <rect x="72" y="205" width="112" height="9" rx="4.5" fill="var(--white)"/>
			  <rect x="72" y="221" width="80"  height="8" rx="4"   fill="var(--brand-100)"/>

			  <!-- Database cylinder -->
			  <path d="M244 96 L244 196 A38 13 0 0 0 320 196 L320 96 Z" fill="var(--primary-light)"/>
			  <line x1="244" y1="96" x2="244" y2="196" stroke="var(--border)" stroke-width="1.5"/>
			  <line x1="320" y1="96" x2="320" y2="196" stroke="var(--border)" stroke-width="1.5"/>
			  <path d="M244 196 A38 13 0 0 0 320 196" fill="none" stroke="var(--border)" stroke-width="1.5"/>
			  <path d="M244 130 A38 13 0 0 0 320 130" fill="none" stroke="var(--hairline)" stroke-width="1.5"/>
			  <path d="M244 163 A38 13 0 0 0 320 163" fill="none" stroke="var(--hairline)" stroke-width="1.5"/>
			  <ellipse cx="282" cy="96" rx="38" ry="13" fill="var(--white)" stroke="var(--border)" stroke-width="1.5"/>

			  <!-- Config gear -->
			  <line x1="250" y1="196" x2="182" y2="262" stroke="var(--border)" stroke-width="1.5"/>
			  <g fill="var(--primary)">
			    <rect x="156" y="256" width="32" height="32" rx="9"/>
			    <rect x="156" y="256" width="32" height="32" rx="9" transform="rotate(45 172 272)"/>
			    <circle cx="172" cy="272" r="17"/>
			  </g>
			  <circle cx="172" cy="272" r="7" fill="var(--white)"/>
			</svg>';
		}

		if ( 'chat-bot' === $key ) {
			return '<svg class="gterm-svg" viewBox="0 0 360 320" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="A friendly chat bot with a robot face replying inside a chat bubble">
			  <!-- antenna -->
			  <line x1="105" y1="66" x2="105" y2="88" stroke="var(--primary)" stroke-width="2.5" stroke-linecap="round"/>
			  <circle cx="105" cy="58" r="6" fill="var(--primary)"/>
			  <!-- robot head -->
			  <rect x="42" y="88" width="126" height="124" rx="30" fill="var(--primary)"/>
			  <circle cx="84" cy="142" r="13" fill="var(--white)"/>
			  <circle cx="128" cy="142" r="13" fill="var(--white)"/>
			  <rect x="80" y="176" width="52" height="12" rx="6" fill="var(--white)"/>
			  <path transform="translate(178 96) scale(0.72)" d="' . $sparkle . '" fill="var(--orange)"/>
			  <!-- chat bubble -->
			  <rect x="198" y="112" width="140" height="96" rx="20" fill="var(--white)" stroke="var(--border)" stroke-width="1.5"/>
			  <path d="M198 156 l-16 14 l16 6 z" fill="var(--white)"/>
			  <rect x="216" y="136" width="96"  height="10" rx="5" fill="var(--primary-light)"/>
			  <rect x="216" y="158" width="112" height="10" rx="5" fill="var(--primary-light)"/>
			  <rect x="216" y="180" width="72"  height="10" rx="5" fill="var(--primary-light)"/>
			  <text class="gterm-svg-cap" x="82" y="248">BOT</text>
			</svg>';
		}

		if ( 'chat-sdk' === $key ) {
			return '<svg class="gterm-svg" viewBox="0 0 360 320" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="A chat SDK toolkit with a central chat card surrounded by code, push and UI building blocks">
			  <!-- connectors -->
			  <g stroke="var(--brand-100)" stroke-width="2">
			    <line x1="180" y1="112" x2="180" y2="78"/>
			    <line x1="134" y1="198" x2="92"  y2="228"/>
			    <line x1="226" y1="198" x2="268" y2="228"/>
			  </g>
			  <!-- code brace tile -->
			  <rect x="150" y="26" width="60" height="52" rx="14" fill="var(--white)" stroke="var(--border)" stroke-width="1.5"/>
			  <text class="gterm-svg-brace" x="180" y="62" text-anchor="middle">{ }</text>
			  <!-- push / bell tile -->
			  <rect x="44" y="226" width="66" height="66" rx="16" fill="var(--white)" stroke="var(--border)" stroke-width="1.5"/>
			  <path d="M77 246 c-9 0 -15 7 -15 15 v7 l-5 7 h40 l-5 -7 v-7 c0 -8 -6 -15 -15 -15 z" fill="var(--primary)"/>
			  <circle cx="77" cy="280" r="4" fill="var(--primary)"/>
			  <circle cx="99" cy="242" r="6" fill="var(--orange)"/>
			  <!-- UI blocks tile -->
			  <rect x="250" y="226" width="66" height="66" rx="16" fill="var(--white)" stroke="var(--border)" stroke-width="1.5"/>
			  <rect x="264" y="240" width="38" height="10" rx="5" fill="var(--primary-light)"/>
			  <rect x="264" y="258" width="17" height="18" rx="4" fill="var(--brand-100)"/>
			  <rect x="285" y="258" width="17" height="18" rx="4" fill="var(--primary-light)"/>
			  <!-- central card + chat bubble -->
			  <rect x="120" y="112" width="120" height="88" rx="20" fill="var(--white)" stroke="var(--border)" stroke-width="1.5"/>
			  <path d="M158 184 l-6 16 l16 -8 z" fill="var(--primary)"/>
			  <rect x="140" y="130" width="80" height="54" rx="16" fill="var(--primary)"/>
			  <rect x="152" y="144" width="50" height="8" rx="4" fill="var(--white)"/>
			  <rect x="152" y="160" width="40" height="8" rx="4" fill="var(--white)" opacity="0.85"/>
			  <text class="gterm-svg-cap" x="122" y="104">SDK</text>
			</svg>';
		}

		if ( 'crawler' === $key ) {
			return '<svg class="gterm-svg" viewBox="0 0 360 320" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="A web crawler scanning a site map and building a search index">
			  <!-- page card -->
			  <text class="gterm-svg-cap" x="20" y="52">CRAWLER</text>
			  <rect x="20" y="60" width="176" height="196" rx="18" fill="var(--white)" stroke="var(--border)" stroke-width="1.5"/>
			  <circle cx="40" cy="82" r="4" fill="var(--brand-100)"/>
			  <circle cx="54" cy="82" r="4" fill="var(--brand-100)"/>
			  <circle cx="68" cy="82" r="4" fill="var(--brand-100)"/>
			  <line x1="20" y1="98" x2="196" y2="98" stroke="var(--hairline)" stroke-width="1.5"/>
			  <!-- sitemap tree -->
			  <g stroke="var(--brand-100)" stroke-width="2">
			    <line x1="100" y1="122" x2="58" y2="168"/>
			    <line x1="100" y1="122" x2="100" y2="168"/>
			    <line x1="100" y1="122" x2="142" y2="168"/>
			    <line x1="58" y1="168" x2="58" y2="212"/>
			    <line x1="142" y1="168" x2="142" y2="212"/>
			  </g>
			  <circle cx="100" cy="122" r="8" fill="var(--primary)"/>
			  <g fill="var(--white)" stroke="var(--brand-100)" stroke-width="2">
			    <circle cx="58" cy="168" r="7"/>
			    <circle cx="100" cy="168" r="7"/>
			    <circle cx="142" cy="168" r="7"/>
			    <circle cx="58" cy="212" r="6"/>
			    <circle cx="142" cy="212" r="6"/>
			  </g>
			  <!-- magnifier scanning -->
			  <circle cx="118" cy="150" r="22" fill="none" stroke="var(--primary)" stroke-width="2.5"/>
			  <line x1="134" y1="166" x2="150" y2="182" stroke="var(--primary)" stroke-width="3.5" stroke-linecap="round"/>
			  <!-- crawl arrow -->
			  <line x1="200" y1="158" x2="226" y2="158" stroke="var(--primary)" stroke-width="2.5" stroke-linecap="round"/>
			  <polygon points="226,158 216,152 216,164" fill="var(--primary)"/>
			  <!-- index list -->
			  <text class="gterm-svg-cap" x="230" y="76">INDEX</text>
			  <rect x="230" y="86" width="114" height="150" rx="16" fill="var(--white)" stroke="var(--border)" stroke-width="1.5"/>
			  <circle cx="250" cy="110" r="4" fill="var(--green)"/>
			  <rect x="262" y="104" width="68" height="11" rx="5.5" fill="var(--primary-light)"/>
			  <circle cx="250" cy="132" r="4" fill="var(--brand-100)"/>
			  <rect x="262" y="126" width="76" height="11" rx="5.5" fill="var(--primary-light)"/>
			  <circle cx="250" cy="154" r="4" fill="var(--brand-100)"/>
			  <rect x="262" y="148" width="58" height="11" rx="5.5" fill="var(--primary-light)"/>
			  <circle cx="250" cy="176" r="4" fill="var(--brand-100)"/>
			  <rect x="262" y="170" width="72" height="11" rx="5.5" fill="var(--primary-light)"/>
			  <circle cx="250" cy="198" r="4" fill="var(--brand-100)"/>
			  <rect x="262" y="192" width="52" height="11" rx="5.5" fill="var(--primary-light)"/>
			</svg>';
		}

		if ( 'dedicated-server' === $key ) {
			return '<svg class="gterm-svg" viewBox="0 0 360 320" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="A single dedicated server tower isolated inside its own secure network boundary">
			  <!-- isolation boundary -->
			  <text class="gterm-svg-cap" x="48" y="42">ISOLATED NETWORK</text>
			  <rect x="44" y="52" width="272" height="224" rx="22" fill="none" stroke="var(--primary)" stroke-width="2" stroke-dasharray="7 8"/>

			  <!-- server tower -->
			  <rect x="124" y="76" width="112" height="176" rx="16" fill="var(--white)" stroke="var(--border)" stroke-width="1.5"/>
			  <rect x="140" y="88" width="50" height="10" rx="5" fill="var(--primary)"/>
			  <circle cx="222" cy="93" r="4" fill="var(--green)"/>
			  <line x1="124" y1="110" x2="236" y2="110" stroke="var(--hairline)" stroke-width="1.5"/>

			  <!-- stacked slats with status LEDs -->
			  <g>
			    <rect x="140" y="120" width="80" height="22" rx="8" fill="var(--primary-light)"/>
			    <circle cx="152" cy="131" r="4" fill="var(--green)"/>
			    <rect x="166" y="127" width="40" height="8" rx="4" fill="var(--brand-100)"/>
			    <rect x="140" y="152" width="80" height="22" rx="8" fill="var(--primary-light)"/>
			    <circle cx="152" cy="163" r="4" fill="var(--green)"/>
			    <rect x="166" y="159" width="40" height="8" rx="4" fill="var(--brand-100)"/>
			    <rect x="140" y="184" width="80" height="22" rx="8" fill="var(--primary-light)"/>
			    <circle cx="152" cy="195" r="4" fill="var(--green)"/>
			    <rect x="166" y="191" width="40" height="8" rx="4" fill="var(--brand-100)"/>
			    <rect x="140" y="216" width="80" height="22" rx="8" fill="var(--primary-light)"/>
			    <circle cx="152" cy="227" r="4" fill="var(--green)"/>
			    <rect x="166" y="223" width="40" height="8" rx="4" fill="var(--brand-100)"/>
			  </g>

			  <!-- security shield -->
			  <path d="M276 130 L292 136 L292 150 C292 161 285 167 276 172 C267 167 260 161 260 150 L260 136 Z" fill="var(--primary)"/>
			  <path d="M269 150 l5 5 l9 -11" stroke="var(--white)" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>

			  <text class="gterm-svg-cap" x="180" y="300" text-anchor="middle">DEDICATED</text>
			</svg>';
		}

		if ( 'ejabberd' === $key ) {
			return '<svg class="gterm-svg" viewBox="0 0 360 320" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="A central chat server hub connected to several client chat apps">
			  <!-- connectors -->
			  <g stroke="var(--brand-100)" stroke-width="2">
			    <line x1="152" y1="140" x2="104" y2="82"/>
			    <line x1="208" y1="140" x2="256" y2="82"/>
			    <line x1="152" y1="180" x2="104" y2="238"/>
			    <line x1="208" y1="180" x2="256" y2="238"/>
			  </g>
			  <!-- top-left client -->
			  <rect x="30" y="40" width="98" height="58" rx="16" fill="var(--white)" stroke="var(--border)" stroke-width="1.5"/>
			  <rect x="44" y="56" width="60" height="8" rx="4" fill="var(--primary-light)"/>
			  <rect x="44" y="72" width="44" height="8" rx="4" fill="var(--primary-light)"/>
			  <circle cx="120" cy="48" r="5" fill="var(--green)"/>
			  <!-- top-right client -->
			  <rect x="232" y="40" width="98" height="58" rx="16" fill="var(--white)" stroke="var(--border)" stroke-width="1.5"/>
			  <rect x="246" y="56" width="60" height="8" rx="4" fill="var(--primary-light)"/>
			  <rect x="246" y="72" width="44" height="8" rx="4" fill="var(--primary-light)"/>
			  <circle cx="322" cy="48" r="5" fill="var(--green)"/>
			  <!-- bottom-left client -->
			  <rect x="30" y="222" width="98" height="58" rx="16" fill="var(--white)" stroke="var(--border)" stroke-width="1.5"/>
			  <rect x="44" y="238" width="60" height="8" rx="4" fill="var(--primary-light)"/>
			  <rect x="44" y="254" width="44" height="8" rx="4" fill="var(--primary-light)"/>
			  <circle cx="120" cy="230" r="5" fill="var(--green)"/>
			  <!-- bottom-right client -->
			  <rect x="232" y="222" width="98" height="58" rx="16" fill="var(--white)" stroke="var(--border)" stroke-width="1.5"/>
			  <rect x="246" y="238" width="60" height="8" rx="4" fill="var(--primary-light)"/>
			  <rect x="246" y="254" width="44" height="8" rx="4" fill="var(--primary-light)"/>
			  <circle cx="322" cy="230" r="5" fill="var(--green)"/>
			  <!-- chat server hub -->
			  <rect x="146" y="126" width="68" height="68" rx="20" fill="var(--primary)"/>
			  <rect x="160" y="140" width="40" height="11" rx="3" fill="var(--white)"/>
			  <rect x="160" y="155" width="40" height="11" rx="3" fill="var(--white)" opacity="0.9"/>
			  <rect x="160" y="170" width="40" height="11" rx="3" fill="var(--white)" opacity="0.8"/>
			  <text class="gterm-svg-cap" x="140" y="216">CHAT SERVER</text>
			</svg>';
		}

		if ( 'erlang' === $key ) {
			return '<svg class="gterm-svg" viewBox="0 0 360 320" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="A grid of many lightweight processes with a few highlighted nodes passing messages between each other">
			  <defs>
			    <marker id="erl-arrow" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
			      <path d="M0 0 L10 5 L0 10 z" fill="var(--primary)"/>
			    </marker>
			  </defs>

			  <text class="gterm-svg-cap" x="40" y="42">PROCESSES</text>

			  <!-- process grid -->
			  <g fill="var(--brand-100)">
			    <circle cx="61" cy="70" r="6"/><circle cx="95" cy="70" r="6"/><circle cx="129" cy="70" r="6"/><circle cx="163" cy="70" r="6"/><circle cx="197" cy="70" r="6"/><circle cx="231" cy="70" r="6"/><circle cx="265" cy="70" r="6"/><circle cx="299" cy="70" r="6"/>
			    <circle cx="61" cy="103" r="6"/><circle cx="95" cy="103" r="6"/><circle cx="129" cy="103" r="6"/><circle cx="163" cy="103" r="6"/><circle cx="197" cy="103" r="6"/><circle cx="231" cy="103" r="6"/><circle cx="265" cy="103" r="6"/><circle cx="299" cy="103" r="6"/>
			    <circle cx="61" cy="136" r="6"/><circle cx="95" cy="136" r="6"/><circle cx="129" cy="136" r="6"/><circle cx="163" cy="136" r="6"/><circle cx="197" cy="136" r="6"/><circle cx="231" cy="136" r="6"/><circle cx="265" cy="136" r="6"/><circle cx="299" cy="136" r="6"/>
			    <circle cx="61" cy="169" r="6"/><circle cx="95" cy="169" r="6"/><circle cx="129" cy="169" r="6"/><circle cx="163" cy="169" r="6"/><circle cx="197" cy="169" r="6"/><circle cx="231" cy="169" r="6"/><circle cx="265" cy="169" r="6"/><circle cx="299" cy="169" r="6"/>
			    <circle cx="61" cy="202" r="6"/><circle cx="95" cy="202" r="6"/><circle cx="129" cy="202" r="6"/><circle cx="163" cy="202" r="6"/><circle cx="197" cy="202" r="6"/><circle cx="231" cy="202" r="6"/><circle cx="265" cy="202" r="6"/><circle cx="299" cy="202" r="6"/>
			    <circle cx="61" cy="235" r="6"/><circle cx="95" cy="235" r="6"/><circle cx="129" cy="235" r="6"/><circle cx="163" cy="235" r="6"/><circle cx="197" cy="235" r="6"/><circle cx="231" cy="235" r="6"/><circle cx="265" cy="235" r="6"/><circle cx="299" cy="235" r="6"/>
			  </g>

			  <!-- highlighted processes -->
			  <g fill="var(--primary)">
			    <circle cx="129" cy="70" r="8"/>
			    <circle cx="231" cy="103" r="8"/>
			    <circle cx="95" cy="169" r="8"/>
			    <circle cx="197" cy="169" r="8"/>
			    <circle cx="265" cy="202" r="8"/>
			  </g>

			  <!-- message passing -->
			  <g stroke="var(--primary)" stroke-width="2" stroke-linecap="round">
			    <line x1="138" y1="73" x2="220" y2="99" marker-end="url(#erl-arrow)"/>
			    <line x1="227" y1="111" x2="202" y2="158" marker-end="url(#erl-arrow)"/>
			    <line x1="188" y1="169" x2="106" y2="169" marker-end="url(#erl-arrow)"/>
			    <line x1="205" y1="173" x2="254" y2="197" marker-end="url(#erl-arrow)"/>
			  </g>

			  <text class="gterm-svg-mono" x="180" y="288" text-anchor="middle">&gt; 2M concurrent</text>
			</svg>';
		}

		if ( 'frontend' === $key ) {
			return '<svg class="gterm-svg" viewBox="0 0 360 320" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="A browser window showing a frontend user interface with a heading, text lines and a button">
			  <!-- browser window -->
			  <text class="gterm-svg-cap" x="40" y="30">FRONTEND</text>
			  <rect x="40" y="40" width="280" height="228" rx="18" fill="var(--white)" stroke="var(--border)" stroke-width="1.5"/>

			  <!-- top bar: window dots + url bar -->
			  <circle cx="64" cy="66" r="4" fill="var(--brand-100)"/>
			  <circle cx="80" cy="66" r="4" fill="var(--brand-100)"/>
			  <circle cx="96" cy="66" r="4" fill="var(--brand-100)"/>
			  <rect x="118" y="58" width="178" height="16" rx="8" fill="var(--primary-light)"/>
			  <line x1="40" y1="86" x2="320" y2="86" stroke="var(--hairline)" stroke-width="1.5"/>

			  <!-- heading + text lines -->
			  <rect x="64" y="108" width="140" height="14" rx="7" fill="var(--primary)"/>
			  <rect x="64" y="136" width="132" height="11" rx="5.5" fill="var(--primary-light)"/>
			  <rect x="64" y="156" width="132" height="11" rx="5.5" fill="var(--primary-light)"/>
			  <rect x="64" y="176" width="104" height="11" rx="5.5" fill="var(--primary-light)"/>

			  <!-- media thumbnail -->
			  <rect x="216" y="132" width="80" height="64" rx="12" fill="var(--primary-light)" stroke="var(--border)" stroke-width="1.5"/>
			  <circle cx="278" cy="150" r="7" fill="var(--white)"/>
			  <polygon points="220,196 246,166 272,196" fill="var(--white)"/>

			  <!-- primary button -->
			  <rect x="64" y="206" width="124" height="38" rx="12" fill="var(--primary)"/>
			  <rect x="84" y="221" width="84" height="8" rx="4" fill="var(--white)"/>
			</svg>';
		}

		if ( 'llm' === $key ) {
			return '<svg class="gterm-svg" viewBox="0 0 360 320" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="A language model block turning input tokens into generated text">
			  <!-- input tokens -->
			  <text class="gterm-svg-cap" x="14" y="112">TOKENS</text>
			  <rect x="16" y="122" width="64" height="11" rx="5.5" fill="var(--primary-light)"/>
			  <rect x="16" y="142" width="48" height="11" rx="5.5" fill="var(--primary-light)"/>
			  <rect x="16" y="162" width="58" height="11" rx="5.5" fill="var(--primary-light)"/>
			  <!-- token to model arrow -->
			  <line x1="88" y1="150" x2="112" y2="150" stroke="var(--primary)" stroke-width="2.5" stroke-linecap="round"/>
			  <polygon points="112,150 102,144 102,156" fill="var(--primary)"/>
			  <!-- LLM block -->
			  <text class="gterm-svg-cap" x="118" y="60">LLM</text>
			  <rect x="118" y="70" width="104" height="170" rx="20" fill="var(--primary-light)" stroke="var(--border)" stroke-width="1.5"/>
			  <!-- parameter grid connectors -->
			  <g stroke="var(--brand-100)" stroke-width="1.5">
			    <line x1="142" y1="100" x2="198" y2="100"/>
			    <line x1="142" y1="164" x2="198" y2="164"/>
			    <line x1="142" y1="196" x2="198" y2="196"/>
			    <line x1="142" y1="100" x2="142" y2="196"/>
			    <line x1="198" y1="100" x2="198" y2="196"/>
			  </g>
			  <!-- parameter nodes -->
			  <g fill="var(--brand-100)">
			    <circle cx="142" cy="100" r="4"/>
			    <circle cx="170" cy="100" r="4"/>
			    <circle cx="198" cy="100" r="4"/>
			    <circle cx="142" cy="164" r="4"/>
			    <circle cx="198" cy="164" r="4"/>
			    <circle cx="142" cy="196" r="4"/>
			    <circle cx="170" cy="196" r="4"/>
			    <circle cx="198" cy="196" r="4"/>
			  </g>
			  <!-- highlighted model node -->
			  <circle cx="170" cy="132" r="16" fill="var(--primary)"/>
			  <path transform="translate(170 132) scale(0.85)" d="' . $sparkle . '" fill="var(--white)"/>
			  <!-- model to output arrow -->
			  <line x1="228" y1="150" x2="252" y2="150" stroke="var(--primary)" stroke-width="2.5" stroke-linecap="round"/>
			  <polygon points="252,150 242,144 242,156" fill="var(--primary)"/>
			  <!-- generated text bubble -->
			  <rect x="256" y="94" width="90" height="112" rx="18" fill="var(--white)" stroke="var(--border)" stroke-width="1.5"/>
			  <rect x="272" y="118" width="58" height="9" rx="4.5" fill="var(--primary-light)"/>
			  <rect x="272" y="138" width="46" height="9" rx="4.5" fill="var(--primary-light)"/>
			  <rect x="272" y="158" width="62" height="9" rx="4.5" fill="var(--primary-light)"/>
			  <rect x="272" y="178" width="38" height="9" rx="4.5" fill="var(--primary-light)"/>
			</svg>';
		}

		if ( 'mcp' === $key ) {
			return '<svg class="gterm-svg" viewBox="0 0 360 320" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="An MCP server hub linking an AI model to a row of tools and resources">
			  <!-- model to hub connector -->
			  <line x1="180" y1="82" x2="180" y2="122" stroke="var(--brand-100)" stroke-width="2.5"/>
			  <!-- AI model node -->
			  <circle cx="180" cy="54" r="26" fill="var(--white)" stroke="var(--brand-100)" stroke-width="2"/>
			  <path transform="translate(180 54) scale(0.9)" d="' . $sparkle . '" fill="var(--primary)"/>
			  <!-- hub to tools connectors -->
			  <g stroke="var(--brand-100)" stroke-width="2">
			    <line x1="180" y1="198" x2="68" y2="246"/>
			    <line x1="180" y1="198" x2="180" y2="246"/>
			    <line x1="180" y1="198" x2="292" y2="246"/>
			  </g>
			  <!-- MCP hub -->
			  <rect x="144" y="122" width="72" height="72" rx="22" fill="var(--primary)"/>
			  <text class="gterm-svg-cap" x="226" y="162">MCP SERVER</text>
			  <!-- plug glyph -->
			  <line x1="173" y1="142" x2="173" y2="151" stroke="var(--white)" stroke-width="3.5" stroke-linecap="round"/>
			  <line x1="187" y1="142" x2="187" y2="151" stroke="var(--white)" stroke-width="3.5" stroke-linecap="round"/>
			  <rect x="166" y="150" width="28" height="18" rx="6" fill="var(--white)"/>
			  <line x1="180" y1="168" x2="180" y2="180" stroke="var(--white)" stroke-width="3.5" stroke-linecap="round"/>
			  <!-- tool tiles -->
			  <rect x="28" y="248" width="80" height="60" rx="14" fill="var(--white)" stroke="var(--border)" stroke-width="1.5"/>
			  <rect x="140" y="248" width="80" height="60" rx="14" fill="var(--white)" stroke="var(--border)" stroke-width="1.5"/>
			  <rect x="252" y="248" width="80" height="60" rx="14" fill="var(--white)" stroke="var(--border)" stroke-width="1.5"/>
			  <!-- brace glyph -->
			  <text class="gterm-svg-mono" x="68" y="285" text-anchor="middle">{ }</text>
			  <!-- database glyph -->
			  <g stroke="var(--primary)" stroke-width="2" fill="none">
			    <ellipse cx="180" cy="266" rx="15" ry="5"/>
			    <line x1="165" y1="266" x2="165" y2="290"/>
			    <line x1="195" y1="266" x2="195" y2="290"/>
			    <path d="M165 278 a15 5 0 0 0 30 0"/>
			    <path d="M165 290 a15 5 0 0 0 30 0"/>
			  </g>
			  <!-- wrench glyph -->
			  <circle cx="286" cy="270" r="7" fill="none" stroke="var(--primary)" stroke-width="2.5"/>
			  <line x1="291" y1="276" x2="302" y2="288" stroke="var(--primary)" stroke-width="3.5" stroke-linecap="round"/>
			</svg>';
		}

		if ( 'npm' === $key ) {
			return '<svg class="gterm-svg" viewBox="0 0 360 320" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="An npm package with a version tag being imported into a project">
			  <!-- package box (two offset faces) -->
			  <text class="gterm-svg-cap" x="44" y="118">NPM</text>
			  <rect x="62" y="130" width="90" height="90" rx="14" fill="var(--primary)"/>
			  <rect x="46" y="148" width="90" height="90" rx="14" fill="var(--primary-light)" stroke="var(--border)" stroke-width="1.5"/>
			  <text class="gterm-svg-mono" x="91" y="200" text-anchor="middle">npm</text>

			  <!-- version tag chip -->
			  <rect x="104" y="126" width="64" height="24" rx="12" fill="var(--white)" stroke="var(--primary)" stroke-width="1.5"/>
			  <text class="gterm-svg-mono" x="136" y="143" text-anchor="middle">1.0.0</text>

			  <!-- import arrow -->
			  <line x1="150" y1="196" x2="210" y2="196" stroke="var(--primary)" stroke-width="2.5" stroke-linecap="round"/>
			  <polygon points="210,196 198,190 198,202" fill="var(--primary)"/>

			  <!-- project card -->
			  <text class="gterm-svg-cap" x="222" y="142">PROJECT</text>
			  <rect x="222" y="150" width="112" height="92" rx="16" fill="var(--white)" stroke="var(--border)" stroke-width="1.5"/>
			  <rect x="240" y="170" width="60" height="10" rx="5" fill="var(--primary-light)"/>
			  <rect x="240" y="190" width="80" height="24" rx="8" fill="var(--primary-light)" stroke="var(--border)" stroke-width="1.5"/>
			  <circle cx="254" cy="202" r="5" fill="var(--primary)"/>
			  <rect x="266" y="198" width="44" height="8" rx="4" fill="var(--brand-100)"/>
			  <rect x="240" y="224" width="52" height="10" rx="5" fill="var(--primary-light)"/>
			</svg>';
		}

		if ( 'rag' === $key ) {
			return '<svg class="gterm-svg" viewBox="0 0 360 320" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Retrieval augmented generation pulling from a knowledge base through a model into an answer">
			  <!-- knowledge base -->
			  <text class="gterm-svg-cap" x="24" y="86">KNOWLEDGE</text>
			  <rect x="46" y="96" width="72" height="96" rx="12" fill="var(--white)" stroke="var(--border)" stroke-width="1.5"/>
			  <rect x="34" y="104" width="72" height="96" rx="12" fill="var(--white)" stroke="var(--border)" stroke-width="1.5"/>
			  <rect x="22" y="112" width="72" height="96" rx="12" fill="var(--white)" stroke="var(--border)" stroke-width="1.5"/>
			  <rect x="34" y="128" width="40" height="8" rx="4" fill="var(--brand-100)"/>
			  <rect x="34" y="144" width="48" height="8" rx="4" fill="var(--brand-100)"/>
			  <rect x="34" y="160" width="32" height="8" rx="4" fill="var(--brand-100)"/>
			  <!-- magnifier over the docs -->
			  <circle cx="86" cy="176" r="18" fill="var(--white)" stroke="var(--primary)" stroke-width="2.5"/>
			  <line x1="99" y1="189" x2="112" y2="202" stroke="var(--primary)" stroke-width="3.5" stroke-linecap="round"/>
			  <!-- retrieve arrow -->
			  <line x1="124" y1="150" x2="150" y2="150" stroke="var(--primary)" stroke-width="2.5" stroke-linecap="round"/>
			  <polygon points="150,150 140,144 140,156" fill="var(--primary)"/>
			  <!-- model node -->
			  <rect x="154" y="116" width="68" height="68" rx="20" fill="var(--primary)"/>
			  <path transform="translate(188 150) scale(1.4)" d="' . $sparkle . '" fill="var(--white)"/>
			  <!-- generate arrow -->
			  <line x1="226" y1="150" x2="252" y2="150" stroke="var(--primary)" stroke-width="2.5" stroke-linecap="round"/>
			  <polygon points="252,150 242,144 242,156" fill="var(--primary)"/>
			  <!-- answer bubble -->
			  <text class="gterm-svg-cap" x="256" y="106">ANSWER</text>
			  <rect x="256" y="116" width="90" height="88" rx="18" fill="var(--white)" stroke="var(--border)" stroke-width="1.5"/>
			  <rect x="272" y="140" width="58" height="9" rx="4.5" fill="var(--primary-light)"/>
			  <rect x="272" y="160" width="46" height="9" rx="4.5" fill="var(--primary-light)"/>
			  <rect x="272" y="180" width="60" height="9" rx="4.5" fill="var(--primary-light)"/>
			</svg>';
		}

		if ( 'react' === $key ) {
			return '<svg class="gterm-svg" viewBox="0 0 360 320" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="The React atom mark: three electron orbits circling a central nucleus">
			  <!-- three orbits, rotated 60 degrees apart -->
			  <g stroke="var(--brand-100)" stroke-width="2.5" fill="none">
			    <ellipse cx="180" cy="150" rx="95" ry="36"/>
			    <ellipse cx="180" cy="150" rx="95" ry="36" transform="rotate(60 180 150)"/>
			    <ellipse cx="180" cy="150" rx="95" ry="36" transform="rotate(120 180 150)"/>
			  </g>

			  <!-- electrons on the orbits -->
			  <circle cx="275" cy="150" r="7" fill="var(--primary)"/>
			  <circle cx="133" cy="232" r="7" fill="var(--primary)"/>
			  <circle cx="133" cy="68" r="7" fill="var(--primary)"/>

			  <!-- nucleus -->
			  <circle cx="180" cy="150" r="13" fill="var(--primary)"/>

			  <text class="gterm-svg-cap" x="180" y="298" text-anchor="middle">REACT</text>
			</svg>';
		}

		if ( 'sla' === $key ) {
			return '<svg class="gterm-svg" viewBox="0 0 360 320" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="A service level agreement document with a signature beside an uptime badge and a security check">
			  <!-- agreement document -->
			  <text class="gterm-svg-cap" x="36" y="46">SERVICE AGREEMENT</text>
			  <rect x="36" y="56" width="176" height="224" rx="16" fill="var(--white)" stroke="var(--border)" stroke-width="1.5"/>
			  <rect x="58" y="80" width="110" height="13" rx="6" fill="var(--primary)"/>
			  <rect x="58" y="102" width="70" height="8" rx="4" fill="var(--brand-100)"/>
			  <line x1="58" y1="122" x2="190" y2="122" stroke="var(--hairline)" stroke-width="1.5"/>
			  <rect x="58" y="136" width="132" height="9" rx="4.5" fill="var(--primary-light)"/>
			  <rect x="58" y="154" width="132" height="9" rx="4.5" fill="var(--primary-light)"/>
			  <rect x="58" y="172" width="104" height="9" rx="4.5" fill="var(--primary-light)"/>
			  <rect x="58" y="190" width="132" height="9" rx="4.5" fill="var(--primary-light)"/>
			  <rect x="58" y="208" width="88" height="9" rx="4.5" fill="var(--primary-light)"/>
			  <path d="M58 246 c10 -14 18 6 27 -5 c7 -8 13 6 21 -3 c6 -6 11 4 20 -2" stroke="var(--green)" stroke-width="2.5" fill="none" stroke-linecap="round"/>
			  <line x1="58" y1="258" x2="150" y2="258" stroke="var(--hairline)" stroke-width="1.5"/>

			  <!-- uptime badge -->
			  <text class="gterm-svg-cap" x="236" y="92">UPTIME</text>
			  <rect x="234" y="100" width="100" height="44" rx="12" fill="var(--white)" stroke="var(--primary)" stroke-width="1.5"/>
			  <text class="gterm-svg-mono" x="284" y="128" text-anchor="middle">99.9%</text>

			  <!-- security check shield -->
			  <path d="M284 176 L302 183 L302 199 C302 212 294 219 284 224 C274 219 266 212 266 199 L266 183 Z" fill="var(--green)"/>
			  <path d="M276 199 l6 6 l11 -13" stroke="var(--white)" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
			  <text class="gterm-svg-cap" x="284" y="248" text-anchor="middle">VERIFIED</text>

			  <text class="gterm-svg-cap" x="180" y="304" text-anchor="middle">SLA</text>
			</svg>';
		}

		if ( 'sso' === $key ) {
			return '<svg class="gterm-svg" viewBox="0 0 360 320" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="A single sign-on login card connected to three identity provider tiles with a signed-in check">
			  <!-- connectors to providers -->
			  <g stroke="var(--brand-100)" stroke-width="2">
			    <line x1="180" y1="160" x2="74" y2="228"/>
			    <line x1="180" y1="160" x2="180" y2="228"/>
			    <line x1="180" y1="160" x2="286" y2="228"/>
			  </g>
			  <g fill="var(--white)" stroke="var(--brand-100)" stroke-width="2">
			    <circle cx="74" cy="228" r="5"/>
			    <circle cx="180" cy="228" r="5"/>
			    <circle cx="286" cy="228" r="5"/>
			  </g>

			  <!-- central login card -->
			  <text class="gterm-svg-cap" x="112" y="40">SIGN IN</text>
			  <rect x="112" y="48" width="136" height="112" rx="18" fill="var(--white)" stroke="var(--border)" stroke-width="1.5"/>
			  <path d="M168 82 v-8 a12 12 0 0 1 24 0 v8" stroke="var(--primary)" stroke-width="3" fill="none" stroke-linecap="round"/>
			  <rect x="160" y="82" width="40" height="32" rx="8" fill="var(--primary)"/>
			  <circle cx="180" cy="96" r="4" fill="var(--white)"/>
			  <rect x="178.5" y="97" width="3" height="8" rx="1.5" fill="var(--white)"/>
			  <rect x="142" y="128" width="76" height="9" rx="4.5" fill="var(--primary-light)"/>
			  <rect x="142" y="144" width="52" height="9" rx="4.5" fill="var(--primary-light)"/>
			  <circle cx="238" cy="56" r="12" fill="var(--green)"/>
			  <path d="M232 56 l4 4 l8 -9" stroke="var(--white)" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>

			  <!-- provider tiles -->
			  <rect x="44" y="234" width="60" height="60" rx="14" fill="var(--white)" stroke="var(--border)" stroke-width="1.5"/>
			  <circle cx="74" cy="264" r="15" fill="none" stroke="var(--primary)" stroke-width="2.5"/>
			  <rect x="150" y="234" width="60" height="60" rx="14" fill="var(--white)" stroke="var(--border)" stroke-width="1.5"/>
			  <path d="M180 250 l15 26 h-30 z" fill="none" stroke="var(--primary)" stroke-width="2.5" stroke-linejoin="round"/>
			  <rect x="256" y="234" width="60" height="60" rx="14" fill="var(--white)" stroke="var(--border)" stroke-width="1.5"/>
			  <rect x="272" y="250" width="28" height="28" rx="4" fill="none" stroke="var(--primary)" stroke-width="2.5" transform="rotate(45 286 264)"/>

			  <text class="gterm-svg-cap" x="180" y="312" text-anchor="middle">SSO</text>
			</svg>';
		}

		if ( 'wallet' === $key ) {
			return '<svg class="gterm-svg" viewBox="0 0 360 320" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="A digital wallet holding several verified asset cards secured with a lock">
			  <!-- fanned asset cards -->
			  <rect x="124" y="80" width="112" height="150" rx="12" fill="var(--white)" stroke="var(--border)" stroke-width="1.5" transform="rotate(-13 180 258)"/>
			  <rect x="124" y="80" width="112" height="150" rx="12" fill="var(--white)" stroke="var(--border)" stroke-width="1.5" transform="rotate(13 180 258)"/>
			  <rect x="124" y="72" width="112" height="150" rx="12" fill="var(--white)" stroke="var(--border)" stroke-width="1.5"/>
			  <rect x="140" y="90" width="80" height="11" rx="5.5" fill="var(--primary-light)"/>
			  <rect x="140" y="112" width="80" height="9" rx="4" fill="var(--brand-100)"/>
			  <rect x="140" y="130" width="58" height="9" rx="4" fill="var(--brand-100)"/>
			  <circle cx="150" cy="156" r="8" fill="var(--green)"/>
			  <path d="M146 156 l3 3 l6 -7" stroke="var(--white)" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
			  <rect x="166" y="152" width="48" height="8" rx="4" fill="var(--brand-100)"/>

			  <!-- wallet body -->
			  <rect x="58" y="168" width="244" height="120" rx="22" fill="var(--primary)"/>
			  <rect x="76" y="226" width="208" height="8" rx="4" fill="var(--primary-light)" opacity="0.5"/>

			  <!-- lock clasp -->
			  <path d="M168 236 v-6 a12 12 0 0 1 24 0 v6" stroke="var(--white)" stroke-width="3" fill="none" stroke-linecap="round"/>
			  <rect x="164" y="236" width="32" height="24" rx="5" fill="var(--white)"/>
			  <circle cx="180" cy="246" r="3" fill="var(--primary)"/>
			  <rect x="178.5" y="247" width="3" height="7" rx="1.5" fill="var(--primary)"/>

			  <text class="gterm-svg-cap" x="180" y="308" text-anchor="middle">WALLET</text>
			</svg>';
		}

		if ( 'white-label' === $key ) {
			return '<svg class="gterm-svg" viewBox="0 0 360 320" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="A blank app label being replaced by a branded tile snapping into place">
			  <!-- app phone -->
			  <text class="gterm-svg-cap" x="196" y="40">APP</text>
			  <rect x="188" y="48" width="140" height="228" rx="26" fill="var(--white)" stroke="var(--border)" stroke-width="1.5"/>
			  <rect x="240" y="58" width="36" height="6" rx="3" fill="var(--brand-100)"/>

			  <!-- blank dashed label slot -->
			  <rect x="214" y="80" width="88" height="64" rx="14" fill="var(--primary-light)" stroke="var(--primary)" stroke-width="2" stroke-dasharray="6 6"/>
			  <line x1="258" y1="100" x2="258" y2="124" stroke="var(--brand-100)" stroke-width="2" stroke-linecap="round"/>
			  <line x1="246" y1="112" x2="270" y2="112" stroke="var(--brand-100)" stroke-width="2" stroke-linecap="round"/>

			  <!-- app content -->
			  <rect x="214" y="160" width="88" height="10" rx="5" fill="var(--primary-light)"/>
			  <rect x="214" y="178" width="60" height="10" rx="5" fill="var(--primary-light)"/>
			  <rect x="214" y="200" width="88" height="30" rx="8" fill="var(--white)" stroke="var(--border)" stroke-width="1.5"/>
			  <circle cx="228" cy="215" r="5" fill="var(--brand-100)"/>
			  <rect x="240" y="211" width="50" height="8" rx="4" fill="var(--brand-100)"/>
			  <circle cx="234" cy="252" r="5" fill="var(--brand-100)"/>
			  <circle cx="258" cy="252" r="5" fill="var(--primary)"/>
			  <circle cx="282" cy="252" r="5" fill="var(--brand-100)"/>

			  <!-- brand tile snapping in -->
			  <rect x="40" y="92" width="80" height="80" rx="18" fill="var(--primary)"/>
			  <path transform="translate(80 132) scale(1.5)" d="' . $sparkle . '" fill="var(--white)"/>
			  <text class="gterm-svg-cap" x="80" y="192" text-anchor="middle">YOUR BRAND</text>

			  <!-- arrow into the slot -->
			  <path d="M128 126 C158 112 180 104 204 100" stroke="var(--primary)" stroke-width="2.5" fill="none" stroke-linecap="round"/>
			  <polygon points="204,100 191,99 196,111" fill="var(--primary)"/>
			  <path transform="translate(300 84) scale(0.7)" d="' . $sparkle . '" fill="var(--orange)"/>

			  <text class="gterm-svg-cap" x="180" y="304" text-anchor="middle">WHITE-LABEL</text>
			</svg>';
		}

		if ( 'xmpp' === $key ) {
			return '<svg class="gterm-svg" viewBox="0 0 360 320" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="A decentralized XMPP federation mesh of servers exchanging a message">
			  <!-- mesh edges -->
			  <g stroke="var(--brand-100)" stroke-width="2">
			    <line x1="66"  y1="74"  x2="300" y2="74"/>
			    <line x1="300" y1="74"  x2="312" y2="226"/>
			    <line x1="312" y1="226" x2="60"  y2="232"/>
			    <line x1="60"  y1="232" x2="66"  y2="74"/>
			    <line x1="66"  y1="74"  x2="312" y2="226"/>
			    <line x1="300" y1="74"  x2="60"  y2="232"/>
			  </g>
			  <!-- outer nodes -->
			  <g fill="var(--white)" stroke="var(--brand-100)" stroke-width="2">
			    <circle cx="66"  cy="74"  r="16"/>
			    <circle cx="300" cy="74"  r="16"/>
			    <circle cx="312" cy="226" r="16"/>
			    <circle cx="60"  cy="232" r="16"/>
			  </g>
			  <!-- central node -->
			  <circle cx="185" cy="150" r="21" fill="var(--primary)"/>
			  <!-- presence dots -->
			  <circle cx="80"  cy="62"  r="5" fill="var(--green)"/>
			  <circle cx="326" cy="214" r="5" fill="var(--green)"/>
			  <!-- message envelope on the top edge -->
			  <rect x="160" y="60" width="46" height="30" rx="6" fill="var(--white)" stroke="var(--primary)" stroke-width="1.5"/>
			  <path d="M162 64 L183 79 L204 64" stroke="var(--primary)" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
			  <text class="gterm-svg-cap" x="158" y="300">XMPP</text>
			</svg>';
		}

		return '';
	}
endif;

$gterm_svg = ethora_gterm_illustration( $g['illustration'] );

// Print the scoped CSS once per request (multiple instances won't duplicate it).
static $gterm_assets = false;
if ( ! $gterm_assets ) :
	$gterm_assets = true;
	?>
	<style>
	  /* ===== Single glossary term — scoped to .gterm. Tokens only. ===== */
	  .gterm * { box-sizing: border-box; }
	  .gterm { font-family: var(--font-body); color: var(--text-body); background: var(--background); }
	  .gterm-wrap { max-width: var(--container-md); margin: 0 auto; padding: 0 var(--section-x); }

	  /* ---- Hero ---- */
	  .gterm-hero {
	    position: relative; overflow: hidden;
	    padding-top: var(--hero-pt); padding-bottom: var(--space-48);
	    background: linear-gradient(180deg, var(--primary-light) 0%, var(--background) 100%);
	  }
	  .gterm-hero .gterm-wrap { position: relative; z-index: 1; }
	  .gterm-watermark {
	    position: absolute; top: var(--hero-pt); left: 50%; transform: translate(-50%, -18%);
	    z-index: 0; pointer-events: none; user-select: none; white-space: nowrap;
	    font-weight: var(--fw-bold); font-size: var(--fs-watermark);
	    line-height: 1; letter-spacing: var(--tracking-tight); color: var(--brand-100);
	  }

	  /* ---- Hero illustration (replaces the watermark when `illustration` is set) ---- */
	  .gterm-hero-grid { display: flex; align-items: center; gap: var(--space-48); }
	  .gterm-hero-main { flex: 1 1 auto; min-width: 0; }
	  .gterm-hero-art  { flex: 0 0 340px; max-width: 340px; }
	  .gterm-svg { display: block; width: 100%; height: auto; }
	  .gterm-svg-cap {
	    font-family: var(--font-body); font-size: var(--fs-xs); font-weight: var(--fw-semibold);
	    letter-spacing: var(--tracking-wide); text-transform: uppercase; fill: var(--text-caption);
	  }
	  .gterm-svg-mono  { font-family: var(--font-mono); font-size: var(--fs-sm); font-weight: var(--fw-semibold); fill: var(--primary); }
	  .gterm-svg-brace { font-family: var(--font-mono); font-size: var(--fs-3xl); font-weight: var(--fw-bold); fill: var(--brand-100); }

	  /* ---- Breadcrumb (mirrors the blog .cap-navigation pattern) ---- */
	  .gterm-crumbs {
	    display: flex; flex-wrap: wrap; align-items: center; gap: var(--space-8);
	    font-family: var(--font-body); font-size: var(--fs-sm);
	    font-weight: var(--fw-semibold); line-height: 1;
	  }
	  .gterm-crumbs a { color: var(--primary); text-decoration: none; transition: color .2s ease; }
	  .gterm-crumbs a:hover { text-decoration: underline; text-underline-offset: 2px; }
	  .gterm-crumb-home { text-decoration: underline; text-underline-offset: 2px; }
	  .gterm-crumb-sep { width: 16px; height: 16px; color: var(--text-muted); flex: 0 0 auto; }
	  .gterm-crumb-current { color: var(--ink); font-weight: var(--fw-bold); }

	  .gterm-eyebrow {
	    margin: var(--space-32) 0 var(--space-16);
	    font-weight: var(--fw-semibold); font-size: var(--fs-eyebrow);
	    letter-spacing: var(--tracking-wide); text-transform: uppercase; color: var(--primary);
	  }
	  .gterm-hero h1 {
	    font-family: var(--font-body); font-weight: var(--fw-bold);
	    font-size: var(--fs-h1); line-height: var(--lh-heading);
	    letter-spacing: var(--tracking-tight); color: var(--ink); margin: 0;
	  }
	  .gterm-lead {
	    max-width: var(--measure); margin: var(--space-16) 0 0;
	    font-size: var(--fs-lg); line-height: var(--lh-relaxed); color: var(--text-body);
	  }

	  /* ---- Body ---- */
	  .gterm-body { padding: var(--space-64) 0 var(--space-80); }
	  .gterm-section + .gterm-section {
	    margin-top: var(--space-48); padding-top: var(--space-48); border-top: 1px solid var(--hairline);
	  }
	  .gterm-h2 {
	    font-family: var(--font-body); font-weight: var(--fw-bold);
	    font-size: var(--fs-h3-lg); line-height: var(--lh-snug); color: var(--ink);
	    margin: 0 0 var(--space-16);
	  }
	  .gterm-body p { font-size: var(--fs-md); line-height: var(--lh-relaxed); color: var(--text-body); margin: 0 0 var(--space-16); }
	  .gterm-body p:last-child { margin-bottom: 0; }
	  .gterm-body a { color: var(--primary); text-decoration: underline; text-underline-offset: 2px; }
	  .gterm-body code {
	    font-family: var(--font-mono); font-size: 0.9em;
	    padding: 2px var(--space-8); border-radius: var(--radius-sm);
	    background: var(--primary-light); color: var(--primary);
	  }
	  .gterm-body ol, .gterm-body ul { margin: 0 0 var(--space-16); padding-left: var(--space-32); }
	  .gterm-body ul { list-style: disc; }      /* index.css resets ul to list-style:none globally */
	  .gterm-body ol { list-style: decimal; }
	  .gterm-body li { font-size: var(--fs-md); line-height: var(--lh-relaxed); color: var(--text-body); margin-bottom: var(--space-8); }
	  .gterm-body li:last-child { margin-bottom: 0; }
	  .gterm-body li::marker { color: var(--primary); font-weight: var(--fw-semibold); }
	  .gterm-code {
	    display: inline-flex; align-items: center; gap: var(--space-8);
	    padding: var(--space-8) var(--space-16); margin-bottom: var(--space-16);
	    background: var(--primary-light); border: 1px solid var(--border);
	    border-radius: var(--radius-md);
	    font-family: var(--font-mono); font-size: var(--fs-sm); color: var(--primary);
	    word-break: break-all;
	  }
	  .gterm-code-label {
	    font-family: var(--font-body); font-weight: var(--fw-semibold);
	    text-transform: uppercase; letter-spacing: var(--tracking-wide);
	    font-size: var(--fs-xs); color: var(--text-caption);
	  }

	  /* ---- Figure / screenshot ---- */
	  .gterm-figure { margin: var(--space-32) 0 0; }
	  .gterm-figure img {
	    display: block; width: 100%; height: auto;
	    border: 1px solid var(--border); border-radius: var(--radius-2xl);
	    box-shadow: var(--shadow-card); background: var(--white);
	  }
	  .gterm-figcaption {
	    margin-top: var(--space-8); font-size: var(--fs-sm);
	    color: var(--text-caption); text-align: center;
	  }

	  /* ---- Video embed ---- */
	  .gterm-video { margin: var(--space-32) 0 0; }
	  .gterm-video-frame {
	    position: relative; width: 100%; aspect-ratio: 16 / 9;
	    border: 1px solid var(--border); border-radius: var(--radius-2xl);
	    overflow: hidden; box-shadow: var(--shadow-card); background: var(--ink);
	  }
	  .gterm-video-frame iframe {
	    position: absolute; inset: 0; width: 100%; height: 100%; border: 0;
	  }

	  /* ---- Related terms ---- */
	  .gterm-related { margin-top: var(--space-48); padding-top: var(--space-48); border-top: 1px solid var(--hairline); }
	  .gterm-related-label {
	    font-weight: var(--fw-semibold); font-size: var(--fs-sm);
	    text-transform: uppercase; letter-spacing: var(--tracking-wide);
	    color: var(--text-caption); margin: 0 0 var(--space-16);
	  }
	  .gterm-related-list { display: flex; flex-wrap: wrap; gap: var(--space-8); }
	  .gterm-chip {
	    display: inline-flex; align-items: center; gap: var(--space-8);
	    font-size: var(--fs-sm); font-weight: var(--fw-semibold);
	    color: var(--primary); background: var(--primary-light); border: 1px solid transparent;
	    padding: var(--space-8) var(--space-16); border-radius: var(--radius-pill);
	    text-decoration: none; transition: background .2s ease, color .2s ease;
	  }
	  .gterm-chip:hover { background: var(--primary); color: var(--white); }

	  @media (max-width: 900px) {
	    .gterm-hero-grid { flex-direction: column; align-items: flex-start; gap: var(--space-32); }
	    .gterm-hero-main { width: 100%; }
	    .gterm-hero-art  { flex-basis: auto; width: 100%; max-width: 320px; }
	  }
	  @media (max-width: 600px) {
	    .gterm-watermark { font-size: clamp(64px, 26vw, 120px); }
	  }
	</style>
	<?php
endif;

$back = ethora_glossary_local_url( $g['back_url'] );
?>

<main class="gterm">

  <section class="gterm-hero">
    <?php if ( $g['watermark'] && ! $gterm_svg ) : ?>
      <span class="gterm-watermark" aria-hidden="true"><?php echo esc_html( $g['watermark'] ); ?></span>
    <?php endif; ?>
    <div class="gterm-wrap">
      <?php if ( $gterm_svg ) : ?><div class="gterm-hero-grid"><div class="gterm-hero-main"><?php endif; ?>

      <nav class="gterm-crumbs" aria-label="Breadcrumb">
        <a class="gterm-crumb-home" href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
        <svg class="gterm-crumb-sep" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"></path></svg>
        <a href="<?php echo esc_url( $back ); ?>"><?php echo esc_html( $g['back_label'] ); ?></a>
        <svg class="gterm-crumb-sep" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"></path></svg>
        <span class="gterm-crumb-current" aria-current="page"><?php echo wp_kses_post( $g['term'] ); ?></span>
      </nav>

      <?php if ( $g['eyebrow'] ) : ?>
        <p class="gterm-eyebrow"><?php echo esc_html( $g['eyebrow'] ); ?></p>
      <?php endif; ?>

      <h1><?php echo wp_kses_post( $g['term'] ); ?></h1>

      <?php if ( $g['lead'] ) : ?>
        <p class="gterm-lead"><?php echo wp_kses_post( $g['lead'] ); ?></p>
      <?php endif; ?>

      <?php if ( $gterm_svg ) : ?>
        </div>
        <div class="gterm-hero-art"><?php echo $gterm_svg; // safe inline SVG, tokens only ?></div>
      </div>
      <?php endif; ?>
    </div>
  </section>

  <section class="gterm-body">
    <div class="gterm-wrap">
      <?php foreach ( $g['sections'] as $sec ) : ?>
        <div class="gterm-section">
          <?php if ( ! empty( $sec['heading'] ) ) : ?>
            <h2 class="gterm-h2"><?php echo wp_kses_post( $sec['heading'] ); ?></h2>
          <?php endif; ?>

          <?php foreach ( (array) ( $sec['blocks'] ?? array() ) as $block ) : ?>
            <?php if ( ! empty( $block['p'] ) ) : ?>
              <p><?php echo wp_kses_post( $block['p'] ); ?></p>
            <?php elseif ( ! empty( $block['ol'] ) ) : ?>
              <ol>
                <?php foreach ( (array) $block['ol'] as $li ) : ?>
                  <li><?php echo wp_kses_post( $li ); ?></li>
                <?php endforeach; ?>
              </ol>
            <?php elseif ( ! empty( $block['ul'] ) ) : ?>
              <ul>
                <?php foreach ( (array) $block['ul'] as $li ) : ?>
                  <li><?php echo wp_kses_post( $li ); ?></li>
                <?php endforeach; ?>
              </ul>
            <?php elseif ( ! empty( $block['code'] ) ) : ?>
              <div>
                <span class="gterm-code">
                  <?php if ( ! empty( $block['code_label'] ) ) : ?>
                    <span class="gterm-code-label"><?php echo esc_html( $block['code_label'] ); ?></span>
                  <?php endif; ?>
                  <?php echo esc_html( $block['code'] ); ?>
                </span>
              </div>
            <?php elseif ( ! empty( $block['img'] ) ) : ?>
              <?php
                $img_src = preg_match( '#^https?://#', $block['img'] )
                  ? $block['img']
                  : get_template_directory_uri() . '/images/' . ltrim( $block['img'], '/' );
              ?>
              <figure class="gterm-figure">
                <img src="<?php echo esc_url( $img_src ); ?>" alt="<?php echo esc_attr( $block['alt'] ?? '' ); ?>" loading="lazy" decoding="async"<?php
                  if ( ! empty( $block['w'] ) ) { echo ' width="' . intval( $block['w'] ) . '"'; }
                  if ( ! empty( $block['h'] ) ) { echo ' height="' . intval( $block['h'] ) . '"'; }
                ?> />
                <?php if ( ! empty( $block['caption'] ) ) : ?>
                  <figcaption class="gterm-figcaption"><?php echo wp_kses_post( $block['caption'] ); ?></figcaption>
                <?php endif; ?>
              </figure>
            <?php elseif ( ! empty( $block['video'] ) ) : ?>
              <figure class="gterm-video">
                <div class="gterm-video-frame">
                  <iframe
                    src="https://www.youtube-nocookie.com/embed/<?php echo esc_attr( $block['video'] ); ?>"
                    title="<?php echo esc_attr( $block['title'] ?? 'Video' ); ?>"
                    loading="lazy"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    referrerpolicy="strict-origin-when-cross-origin"
                    allowfullscreen></iframe>
                </div>
                <?php if ( ! empty( $block['caption'] ) ) : ?>
                  <figcaption class="gterm-figcaption"><?php echo wp_kses_post( $block['caption'] ); ?></figcaption>
                <?php endif; ?>
              </figure>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>

      <?php if ( ! empty( $g['related'] ) ) : ?>
        <div class="gterm-related">
          <p class="gterm-related-label">Related terms</p>
          <div class="gterm-related-list">
            <?php foreach ( $g['related'] as $rel ) : ?>
              <a class="gterm-chip" href="<?php echo esc_url( ethora_glossary_local_url( $rel['url'] ) ); ?>"><?php echo esc_html( $rel['label'] ); ?></a>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </section>

</main>
