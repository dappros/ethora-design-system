<?php
/**
 * Reusable "Quick Start" section — a numbered, timeline-style how-to with
 * professional, copy-to-clipboard code blocks.
 *
 * Usage:
 *   get_template_part( 'template-parts/section', 'quick-start', array(
 *     'title' => 'Quick Start: Add Chat to Your React App in 5 Minutes',
 *     'intro' => 'Ethora’s React chat component is available as an npm package…',
 *     'steps' => array(
 *       array(
 *         'title' => 'Create a new React project',   // number is added automatically
 *         'text'  => 'Optional HTML shown above the code (inline <code>/<a> allowed).',
 *         'lang'  => 'bash',                          // code label (default: bash)
 *         'code'  => "npm create vite@latest my-app\ncd my-app\nnpm install", // raw code (escaped on output)
 *         'note'  => 'Optional HTML shown below the code.',
 *       ),
 *       // …
 *     ),
 *     'modifier' => '',  // extra class(es) on the <section>
 *   ) );
 *
 * Tip: pass multi-line `code` with a PHP nowdoc (<<<'CODE' … CODE;) so quotes,
 * angle brackets and indentation are preserved verbatim.
 *
 * @param array $args Passed through get_template_part().
 */

$title    = isset( $args['title'] ) ? $args['title'] : '';
$intro    = isset( $args['intro'] ) ? $args['intro'] : '';
$outro    = isset( $args['outro'] ) ? $args['outro'] : '';
$steps    = isset( $args['steps'] ) && is_array( $args['steps'] ) ? $args['steps'] : array();
$modifier = isset( $args['modifier'] ) ? $args['modifier'] : '';

// Print the component CSS + JS only once per request, even if used several times.
static $quick_start_assets_printed = false;
if ( ! $quick_start_assets_printed ) :
	$quick_start_assets_printed = true;
	?>
	<style>
		/* full-width sticky header bar, pinned below the fixed site header */
		.quick-start .qs-head {
			position: sticky;
			top: var(--qs-sticky-top, 80px);
			z-index: 5;
			background: var(--background);
			transition: box-shadow 0.25s ease;
		}
		.quick-start .qs-head-inner {
			max-width: 920px;
			margin: 0 auto;
			padding: 1.1rem 24px 1.2rem;
			text-align: center;
		}
		.quick-start .qs-head h2 { margin: 0; padding-top: 16px; }
		.quick-start.qs-stuck .qs-head { box-shadow: 0 10px 26px -14px rgba(8, 20, 45, 0.25); }
		/* content body: wider than the header and left-aligned (overrides .get-started's center) */
		.quick-start .qs-body {
			max-width: 1200px;
			margin: 0 auto;
			padding: 1.75rem 24px 0;
			text-align: left;
			padding-bottom: 64px;
		}
		/* Mobile-first: single column, image panel hidden. The two-column grid + image
		   only switch on for wide screens (>= 901px) so narrow viewports never overflow. */
		.quick-start .qs-media { display: none; }
		.quick-start .qs-media-stack { position: relative; aspect-ratio: 460 / 400; }
		.quick-start .qs-shot {
			position: absolute;
			inset: 0;
			width: 100%;
			height: 100%;
			object-fit: contain;
			opacity: 0;
			transform: scale(0.985);
			transition: opacity 0.45s ease, transform 0.45s ease;
		}
		.quick-start .qs-shot.is-shown { opacity: 1; transform: none; }
		.quick-start:not(.qs-anim) .qs-shot:first-child { opacity: 1; transform: none; }
		@media (min-width: 901px) {
			.quick-start .qs-grid.has-media {
				display: grid;
				grid-template-columns: minmax(0, 1fr) minmax(0, 440px);
				gap: 48px;
				align-items: start;
			}
			.quick-start .qs-media {
				display: block;
				position: sticky;
				top: var(--qs-media-top, 120px);
				align-self: start;
			}
		}
		/* on mobile/tablet the header just scrolls with the page (no pinning under the nav) */
		@media (max-width: 900px) {
			.quick-start .qs-head { position: static; }
		}
		.quick-start .qs-intro {
			color: var(--text-light);
			font-size: 1.05rem;
			line-height: 1.65;
			margin: 0.75rem auto 0;
			text-align: center;
			max-width: 760px;
		}
		/* list-style:none !important beats the theme's `section.get-started ol { list-style: decimal !important }` (header-v2.php) */
		.quick-start .qs-steps { list-style: none !important; margin: 0; padding: 0 !important; position: relative; }
		.quick-start .qs-step {
			position: relative;
			padding: 0 0 2.25rem 0;
		}
		.quick-start .qs-step:last-child { padding-bottom: 0; }
		/* badge sits in a row with the heading, so numbers line up opposite each <h3> */
		.quick-start .qs-step-head { display: flex; align-items: center; gap: 1rem; }
		.quick-start .qs-step-body { padding: 0.85rem 0 0 3.8rem; }
		/* single continuous gray track (works without JS) */
		.quick-start .qs-steps::before {
			content: "";
			position: absolute;
			left: 1.4rem;
			top: 1.4rem;
			bottom: 1.4rem;
			width: 2px;
			transform: translateX(-50%);
			background: var(--border-color);
			z-index: 0;
		}
		/* with JS the track + fill are positioned precisely between badge centers, so drop the rough fallback line */
		.quick-start.qs-anim .qs-steps::before { display: none; }
		.quick-start .qs-rail-track,
		.quick-start .qs-rail-fill {
			position: absolute;
			left: 1.4rem;
			width: 2px;
			transform: translateX(-50%);
			border-radius: 2px;
			height: 0;
			pointer-events: none;
			z-index: 0;
		}
		.quick-start .qs-rail-track { background: var(--border-color); }
		/* blue fill — no transition: it must track the cursor 1:1 (both updated in the same rAF frame) */
		.quick-start .qs-rail-fill {
			background: var(--primary);
		}
		/* small step markers (dots) on the rail; one big number cursor advances through them */
		.quick-start .qs-dot {
			position: relative;
			flex-shrink: 0;
			width: 2.8rem;
			height: 2.8rem;
			display: flex;
			align-items: center;
			justify-content: center;
			z-index: 1;
		}
		.quick-start .qs-dot::before {
			content: "";
			width: 14px;
			height: 14px;
			border-radius: 50%;
			background: var(--white);
			box-shadow: inset 0 0 0 2px var(--border-color);
			transition: background 0.3s ease, box-shadow 0.3s ease, opacity 0.3s ease;
		}
		.quick-start:not(.qs-anim) .qs-dot::before { background: var(--primary); box-shadow: none; }
		.quick-start.qs-anim .qs-dot.qs-on::before { background: var(--primary); box-shadow: none; }
		.quick-start .qs-cursor {
			position: absolute;
			left: 0;
			top: 0;
			width: 2.8rem;
			height: 2.8rem;
			border-radius: 50%;
			background: var(--primary);
			color: #fff;
			font-weight: 700;
			font-size: 1.05rem;
			display: flex;
			align-items: center;
			justify-content: center;
			box-shadow: 0 0 0 6px rgba(0, 82, 205, 0.14), 0 8px 20px rgba(0, 82, 205, 0.35);
			z-index: 4;
			pointer-events: none;
		}
		.quick-start .qs-cursor.bump { animation: qs-bump 0.4s ease; }
		@keyframes qs-bump { 0% { transform: scale(1); } 40% { transform: scale(1.18); } 100% { transform: scale(1); } }

		/* ---- Scroll animation (only when JS adds .qs-anim; no-JS keeps the static blue look) ---- */
		.quick-start.qs-anim .qs-step {
			opacity: 0;
			transform: translateY(16px);
			transition: opacity 0.5s ease, transform 0.5s ease;
		}
		.quick-start.qs-anim .qs-step.in-view { opacity: 1; transform: none; }
		@media (prefers-reduced-motion: reduce) {
			.quick-start.qs-anim .qs-step { opacity: 1; transform: none; transition: none; }
		}
		.quick-start .qs-step h3 {
			margin: 0;
			flex: 1 1 auto;
			min-width: 0;
			font-size: 1.2rem;
			line-height: 1.3;
			color: var(--text-dark);
		}
		.quick-start .qs-text,
		.quick-start .qs-note {
			color: var(--text-light);
			line-height: 1.65;
			margin: 0 0 1rem;
		}
		.quick-start .qs-note { margin-top: 1rem; margin-bottom: 0; }
		.quick-start .qs-outro {
			color: var(--text-light);
			line-height: 1.65;
			margin: 1.75rem 0 0;
			padding-left: 3.8rem;
		}
		.quick-start .qs-outro a { color: var(--primary); text-decoration: underline; }
		.quick-start .qs-outro code {
			background: var(--primary-light);
			color: var(--primary-dark);
			padding: 0.12em 0.45em;
			border-radius: 6px;
			font-family: ui-monospace, "SF Mono", SFMono-Regular, Menlo, Consolas, monospace;
			font-size: 0.88em;
		}
		/* inline code */
		.quick-start :is(.qs-text, .qs-note) code {
			background: var(--primary-light);
			color: var(--primary-dark);
			padding: 0.12em 0.45em;
			border-radius: 6px;
			font-family: ui-monospace, "SF Mono", SFMono-Regular, Menlo, Consolas, monospace;
			font-size: 0.88em;
		}
		.quick-start .qs-text a,
		.quick-start .qs-note a { color: var(--primary); text-decoration: underline; }
		/* code block — the one bold, editor-style element */
		.quick-start .qs-code {
			margin: 0 0 0.7rem;
			border-radius: 14px;
			overflow: hidden;
			background: #0f172a;
			border: 1px solid #1e293b;
			box-shadow: 0 12px 34px rgba(8, 20, 45, 0.18);
		}
		.quick-start .qs-code-head {
			display: flex;
			align-items: center;
			justify-content: space-between;
			gap: 12px;
			padding: 0.6rem 0.9rem;
			background: #111c34;
			border-bottom: 1px solid #1e293b;
		}
		.quick-start .qs-code-left { display: flex; align-items: center; gap: 0.7rem; }
		.quick-start .qs-dots { display: flex; gap: 6px; }
		.quick-start .qs-dots span { width: 11px; height: 11px; border-radius: 50%; display: block; }
		.quick-start .qs-dots span:nth-child(1) { background: #ff5f57; }
		.quick-start .qs-dots span:nth-child(2) { background: #febc2e; }
		.quick-start .qs-dots span:nth-child(3) { background: #28c840; }
		.quick-start .qs-lang {
			font-family: ui-monospace, "SF Mono", SFMono-Regular, Menlo, Consolas, monospace;
			font-size: 0.72rem;
			letter-spacing: 0.07em;
			text-transform: uppercase;
			color: #7d93c0;
		}
		.quick-start .qs-copy {
			display: inline-flex;
			align-items: center;
			gap: 6px;
			background: transparent;
			color: #9fb3d9;
			border: 1px solid #2a3b5e;
			border-radius: 8px;
			padding: 0.3rem 0.7rem;
			font-size: 0.78rem;
			font-weight: 600;
			cursor: pointer;
			transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease;
		}
		.quick-start .qs-copy:hover { background: #1b2944; color: #fff; }
		.quick-start .qs-copy svg { width: 14px; height: 14px; }
		.quick-start .qs-copy.copied { color: #4ade80; border-color: rgba(34, 197, 94, 0.45); }
		.quick-start .qs-code pre { margin: 0; padding: 1.1rem 1.2rem; overflow-x: auto; }
		.quick-start .qs-code code {
			font-family: ui-monospace, "SF Mono", SFMono-Regular, Menlo, Consolas, monospace;
			font-size: 0.84rem;
			line-height: 1.7;
			color: #e2e8f0;
			white-space: pre;
			tab-size: 2;
		}
		@media (max-width: 600px) {
			.quick-start .qs-body { padding-left: 16px; padding-right: 16px; }
			.quick-start .qs-step h3 { font-size: 1.06rem; }
		}
	</style>
	<script>
		(function () {
			if (window.__qsCopyBound) return;
			window.__qsCopyBound = true;
			document.addEventListener('click', function (e) {
				var btn = e.target.closest('.qs-copy');
				if (!btn) return;
				var wrap = btn.closest('.qs-code');
				var code = wrap && wrap.querySelector('code');
				if (!code) return;
				var text = code.innerText;
				var done = function () {
					btn.classList.add('copied');
					var label = btn.querySelector('.qs-copy-label');
					var prev = label ? label.textContent : '';
					if (label) label.textContent = 'Copied!';
					setTimeout(function () {
						btn.classList.remove('copied');
						if (label) label.textContent = prev || 'Copy';
					}, 1800);
				};
				if (navigator.clipboard && navigator.clipboard.writeText) {
					navigator.clipboard.writeText(text).then(done).catch(function () {});
				} else {
					var ta = document.createElement('textarea');
					ta.value = text; document.body.appendChild(ta); ta.select();
					try { document.execCommand('copy'); done(); } catch (err) {}
					document.body.removeChild(ta);
				}
			});
		})();

		(function () {
			if (window.__qsAnimBound) return;
			window.__qsAnimBound = true;
			var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
			function init() {
				var sections = document.querySelectorAll('.quick-start');
				Array.prototype.forEach.call(sections, function (sec) {
					var steps = Array.prototype.slice.call(sec.querySelectorAll('.qs-step'));
					if (!steps.length) return;
					sec.classList.add('qs-anim');
					if (reduce) {
						steps.forEach(function (s) { s.classList.add('in-view'); });
						return;
					}
					// reveal each step as it enters the viewport
					if ('IntersectionObserver' in window) {
						var revObs = new IntersectionObserver(function (entries) {
							entries.forEach(function (en) {
								if (en.isIntersecting) { en.target.classList.add('in-view'); revObs.unobserve(en.target); }
							});
						}, { threshold: 0.18, rootMargin: '0px 0px -8% 0px' });
						steps.forEach(function (s) { revObs.observe(s); });
					} else {
						steps.forEach(function (s) { s.classList.add('in-view'); });
					}
					// rail + a single pinned number cursor that advances through the dots
					var qsSteps = sec.querySelector('.qs-steps');
					var dots = steps.map(function (s) { return s.querySelector('.qs-dot'); });
					var firstDot = dots[0];
					var lastDot = dots[dots.length - 1];
					var track = document.createElement('span');
					track.className = 'qs-rail-track';
					var fill = document.createElement('span');
					fill.className = 'qs-rail-fill';
					var cursor = document.createElement('span');
					cursor.className = 'qs-cursor';
					var cursorN = document.createElement('span');
					cursorN.className = 'qs-cursor-n';
					cursorN.textContent = '1';
					cursor.appendChild(cursorN);
					if (qsSteps) { qsSteps.appendChild(track); qsSteps.appendChild(fill); qsSteps.appendChild(cursor); }
					// sticky head: pin below the fixed site header
					var qsHead = sec.querySelector('.qs-head');
					var headerEl = document.querySelector('.header');
					var stickyTop = 80;
					var cursorTargetY = 200;
					var shots = sec.querySelectorAll('.qs-shot');
					function setTop() {
						stickyTop = headerEl ? headerEl.offsetHeight : 80;
						sec.style.setProperty('--qs-sticky-top', stickyTop + 'px');
						var headH = qsHead ? qsHead.offsetHeight : 0;
						sec.style.setProperty('--qs-media-top', (stickyTop + headH + 24) + 'px');
						// pin the number a comfortable distance below the sticky header (desktop) or the nav (mobile)
						var headSticky = qsHead && getComputedStyle( qsHead ).position === 'sticky';
						cursorTargetY = stickyTop + ( headSticky ? headH + 72 : 96 );
					}
					setTop();
					var curIdx = -1;
					var ticking = false;
					function update() {
						ticking = false;
						if (qsHead) {
							sec.classList.toggle('qs-stuck', qsHead.getBoundingClientRect().top <= stickyTop + 1);
						}
						if (!qsSteps || !firstDot || !lastDot) return;
						var stepsTop = qsSteps.getBoundingClientRect().top;
						var ch = cursor.offsetHeight || 44;
						var within = function (el) { var r = el.getBoundingClientRect(); return r.top + r.height / 2 - stepsTop; };
						var firstC = within(firstDot);
						var lastC = within(lastDot);
						// cursor stays pinned at cursorTargetY on screen, clamped to the rail
						var cur = cursorTargetY - stepsTop;
						if (cur < firstC) { cur = firstC; }
						if (cur > lastC) { cur = lastC; }
						cursor.style.top = (cur - ch / 2) + 'px';
						track.style.top = firstC + 'px';
						track.style.height = (lastC - firstC) + 'px';
						fill.style.top = firstC + 'px';
						fill.style.height = (cur - firstC) + 'px';
						// active = last dot the cursor has reached; fill passed dots
						var activeIdx = 0;
						for (var i = 0; i < dots.length; i++) {
							var on = within(dots[i]) <= cur + 1;
							if (on) { activeIdx = i; }
							if (dots[i]) { dots[i].classList.toggle('qs-on', on); }
						}
						if (activeIdx !== curIdx) {
							cursorN.textContent = activeIdx + 1;
							cursor.classList.remove('bump');
							void cursor.offsetWidth;
							cursor.classList.add('bump');
							curIdx = activeIdx;
						}
						// swap the right-side image to match the active step
						if (shots.length) {
							var stepNum = activeIdx + 1;
							var chosen = shots[0];
							for (var k = 0; k < shots.length; k++) {
								if (+shots[k].getAttribute('data-step') <= stepNum) { chosen = shots[k]; }
							}
							for (var m = 0; m < shots.length; m++) {
								shots[m].classList.toggle('is-shown', shots[m] === chosen);
							}
						}
					}
					function onScroll() { if (!ticking) { ticking = true; requestAnimationFrame(update); } }
					function onResize() { setTop(); onScroll(); }
					window.addEventListener('scroll', onScroll, { passive: true });
					window.addEventListener('resize', onResize, { passive: true });
					update();
				});
			}
			if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
		})();
	</script>
	<?php
endif;

$section_class = 'get-started white quick-start' . ( $modifier ? ' ' . $modifier : '' );

// Steps may carry a per-step `image` (+ `image_alt`) shown in a sticky panel on the right.
$qs_has_media = false;
foreach ( $steps as $qs_st ) {
	if ( ! empty( $qs_st['image'] ) ) { $qs_has_media = true; break; }
}
$qs_tpl = get_template_directory_uri();
?>
<section class="<?php echo esc_attr( $section_class ); ?>">
	<?php if ( $title || $intro ) : ?>
		<div class="qs-head">
			<div class="qs-head-inner">
				<?php if ( $title ) : ?>
					<h2><?php echo esc_html( $title ); ?></h2>
				<?php endif; ?>
				<?php if ( $intro ) : ?>
					<p class="qs-intro"><?php echo wp_kses_post( $intro ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>

	<div class="qs-body">
		<div class="qs-grid<?php echo $qs_has_media ? ' has-media' : ''; ?>">
		<?php if ( $steps ) : ?>
			<ol class="qs-steps">
				<?php
				$i = 0;
				foreach ( $steps as $step ) :
					$i++;
					$s_title = isset( $step['title'] ) ? $step['title'] : '';
					$s_text  = isset( $step['text'] ) ? $step['text'] : '';
					$s_code  = isset( $step['code'] ) ? $step['code'] : '';
					$s_lang  = isset( $step['lang'] ) ? $step['lang'] : 'bash';
					$s_note  = isset( $step['note'] ) ? $step['note'] : '';

					// A step may carry one `code` (+ `lang`) or several blocks via `codes` => [ ['lang'=>,'code'=>], … ].
					$s_blocks = isset( $step['codes'] ) && is_array( $step['codes'] ) ? $step['codes'] : array();
					if ( ! $s_blocks && '' !== $s_code ) {
						$s_blocks = array( array( 'lang' => $s_lang, 'code' => $s_code ) );
					}
					?>
					<li class="qs-step">
						<div class="qs-step-head">
							<span class="qs-dot" aria-hidden="true"></span>
							<?php if ( $s_title ) : ?>
								<h3><?php echo esc_html( $s_title ); ?></h3>
							<?php endif; ?>
						</div>
						<div class="qs-step-body">
						<?php if ( $s_text ) : ?>
							<p class="qs-text"><?php echo wp_kses_post( $s_text ); ?></p>
						<?php endif; ?>
						<?php foreach ( $s_blocks as $blk ) :
							$b_lang = isset( $blk['lang'] ) ? $blk['lang'] : 'bash';
							$b_code = isset( $blk['code'] ) ? $blk['code'] : '';
							if ( '' === $b_code ) {
								continue;
							}
							?>
							<div class="qs-code">
								<div class="qs-code-head">
									<span class="qs-code-left">
										<span class="qs-dots"><span></span><span></span><span></span></span>
										<span class="qs-lang"><?php echo esc_html( $b_lang ); ?></span>
									</span>
									<button type="button" class="qs-copy" aria-label="Copy code">
										<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
										<span class="qs-copy-label">Copy</span>
									</button>
								</div>
								<pre><code><?php echo esc_html( $b_code ); ?></code></pre>
							</div>
						<?php endforeach; ?>
						<?php if ( $s_note ) : ?>
							<p class="qs-note"><?php echo wp_kses_post( $s_note ); ?></p>
						<?php endif; ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>
		<?php if ( $qs_has_media ) : ?>
			<div class="qs-media" aria-hidden="true">
				<div class="qs-media-stack">
					<?php
					$qs_mi = 0;
					foreach ( $steps as $qs_st ) :
						$qs_mi++;
						$qs_img = isset( $qs_st['image'] ) ? $qs_st['image'] : '';
						if ( '' === $qs_img ) {
							continue;
						}
						if ( false === strpos( $qs_img, '/' ) ) {
							$qs_img = 'images/quick-start/' . $qs_img;
						}
						$qs_img_url = $qs_tpl . '/' . ltrim( $qs_img, '/' );
						$qs_img_alt = isset( $qs_st['image_alt'] ) ? $qs_st['image_alt'] : '';
						?>
						<img class="qs-shot" data-step="<?php echo esc_attr( $qs_mi ); ?>" src="<?php echo esc_url( $qs_img_url ); ?>" alt="<?php echo esc_attr( $qs_img_alt ); ?>" loading="lazy" />
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
		</div>

		<?php if ( $outro ) : ?>
			<p class="qs-outro"><?php echo wp_kses_post( $outro ); ?></p>
		<?php endif; ?>
	</div>
</section>
