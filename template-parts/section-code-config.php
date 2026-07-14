<?php
/**
 * Reusable "code + config covers" section — a syntax-highlighted code editor mockup on the
 * left (window chrome: traffic-light dots + filename tab + Copy button) and a labelled stack
 * of "what this config covers" cards on the right (icon tile + mono chip + description),
 * closing with a footnote. Self-contained (CSS + Copy JS once per request), tokens only,
 * responsive (stacks to one column on narrow screens).
 *
 * The `code` prop is trusted, pre-highlighted HTML: wrap tokens in spans with these classes —
 * `t-kw` (keyword), `t-str` (string), `t-com` (comment), `t-fn` (function/name), `t-tag`
 * (JSX tag), `t-prop` (property/accent), `t-punc` (punctuation). Escape `<`/`>` as `&lt;`/`&gt;`.
 *
 *   get_template_part( 'template-parts/section-code-config', null, array(
 *     'eyebrow' => 'React chat component',
 *     'title'   => 'Customize the chat UI to match your brand',
 *     'lead'    => 'Every aspect… is configurable.',
 *     'file'    => 'App.tsx',
 *     'code'    => '<span class="t-kw">import</span> { … }',   // pre-highlighted HTML
 *     'covers_label' => 'What <span class="ccf-hl">config</span> covers',
 *     'items'   => array(
 *       array( 'icon' => '<svg …>…</svg>', 'label' => 'colors & typography', 'text' => '…' ),
 *       // …
 *     ),
 *     'footnote' => 'The full <code>IConfig</code> exposes 60+ options…',   // HTML
 *   ) );
 *
 * @package ethora-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ccf = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'eyebrow'      => '',
		'title'        => '',
		'lead'         => '',
		'shade'        => false,
		'file'         => 'App.tsx',
		'code'         => '',
		'covers_label' => '',
		'items'        => array(),
		'footnote'     => '',
	)
);

if ( '' === $ccf['code'] && empty( $ccf['items'] ) ) {
	return;
}

$ccf_assets = empty( $GLOBALS['ethora_ccf_assets'] );
if ( $ccf_assets ) {
	$GLOBALS['ethora_ccf_assets'] = true;
}
?>
<?php if ( $ccf_assets ) : ?>
<style>
  /* CODE + CONFIG COVERS — editor mockup + labelled cards. Tokens for UI; literal hex for code syntax. */
  .ccf-section { padding: var(--section-y) var(--section-x); }
  .ccf-section, .ccf-section *, .ccf-section *::before, .ccf-section *::after { box-sizing: border-box; }
  .ccf-section.is-shaded { background: var(--surface-alt); border-top: 1px solid var(--hairline); border-bottom: 1px solid var(--hairline); }
  .ccf-wrap { max-width: var(--content-max); margin: 0 auto; }
  .ccf-head { max-width: var(--container-md); }
  .ccf-eyebrow { font-family: var(--font-mono); font-weight: var(--fw-medium); font-size: var(--fs-eyebrow); letter-spacing: var(--tracking-wide); text-transform: uppercase; color: var(--primary); margin: 0 0 var(--space-16); }
  .ccf-h2 { font-family: var(--font-serif); font-weight: var(--fw-medium); font-size: var(--fs-h2); line-height: 1.08; letter-spacing: -.01em; color: var(--ink); margin: 0; }
  .ccf-lead { font-size: var(--fs-lg); line-height: var(--lh-relaxed); color: var(--text-body); margin: var(--space-16) 0 0; }
  .ccf-grid { display: grid; grid-template-columns: minmax(0, 1.4fr) minmax(0, 1fr); gap: clamp(var(--space-32), 4vw, var(--space-64)); align-items: start; margin-top: var(--space-48); }
  /* editor */
  .ccf-editor { border-radius: var(--radius-2xl); overflow: hidden; background: var(--ink); box-shadow: var(--shadow-lift); }
  .ccf-editor-head { display: flex; align-items: center; gap: var(--space-16); padding: var(--space-16); border-bottom: 1px solid rgba(255,255,255,.08); }
  .ccf-dots { display: flex; gap: var(--space-8); flex: none; }
  .ccf-dots span { width: 12px; height: 12px; border-radius: var(--radius-pill); display: block; }
  .ccf-dots span:nth-child(1) { background: #ff5f57; }
  .ccf-dots span:nth-child(2) { background: #febc2e; }
  .ccf-dots span:nth-child(3) { background: #28c840; }
  .ccf-file { flex: 1 1 auto; min-width: 0; font-family: ui-monospace, "SF Mono", Menlo, Consolas, monospace; font-size: var(--fs-sm); color: #8ea3cc; }
  .ccf-copy { display: inline-flex; align-items: center; gap: var(--space-8); flex: none; background: rgba(255,255,255,.06); color: #b8c6e6; border: 1px solid rgba(255,255,255,.12); border-radius: var(--radius-xs); padding: var(--space-8) var(--space-16); font-family: var(--font-body); font-size: var(--fs-xs); font-weight: var(--fw-semibold); cursor: pointer; transition: background .2s ease, color .2s ease; }
  .ccf-copy:hover { background: rgba(255,255,255,.12); color: var(--white); }
  .ccf-copy.copied { color: #6ee7a0; }
  .ccf-copy svg { width: 14px; height: 14px; }
  .ccf-code { margin: 0; padding: var(--space-32); overflow-x: auto; }
  .ccf-code code { font-family: ui-monospace, "SF Mono", Menlo, Consolas, monospace; font-size: var(--fs-sm); line-height: 1.8; color: var(--text-on-dark); white-space: pre; tab-size: 2; }
  .ccf-code .t-kw { color: #c586e0; }
  .ccf-code .t-str { color: #9ecb8a; }
  .ccf-code .t-com { color: #6a7ba3; font-style: italic; }
  .ccf-code .t-fn, .ccf-code .t-tag { color: #6cb6ff; }
  .ccf-code .t-prop { color: var(--accent-on-dark); }
  .ccf-code .t-punc { color: #8a97b8; }
  /* config covers */
  .ccf-covers-label { font-family: var(--font-mono); font-weight: var(--fw-semibold); font-size: var(--fs-eyebrow); letter-spacing: var(--tracking-wide); text-transform: uppercase; color: var(--text-caption); margin: 0 0 var(--space-16); }
  .ccf-covers-label .ccf-hl { color: var(--primary); }
  .ccf-cards { display: flex; flex-direction: column; gap: var(--space-16); }
  .ccf-card { display: flex; gap: var(--space-16); align-items: flex-start; background: var(--white); border: 1px solid var(--border); border-radius: var(--radius-xl); padding: var(--space-16); transition: box-shadow .25s ease, border-color .25s ease; }
  .ccf-card:hover { box-shadow: var(--shadow-card); border-color: var(--border-strong); }
  .ccf-card-icon { flex: none; width: 40px; height: 40px; border-radius: var(--radius-md); background: var(--tint); display: flex; align-items: center; justify-content: center; color: var(--primary); }
  .ccf-card-icon svg { width: 20px; height: 20px; }
  .ccf-card-body { min-width: 0; }
  .ccf-chip { display: inline-block; font-family: ui-monospace, "SF Mono", Menlo, Consolas, monospace; font-weight: var(--fw-semibold); font-size: var(--fs-xs); color: var(--primary); background: var(--primary-light); border-radius: var(--radius-sm); padding: var(--space-4) var(--space-8); }
  .ccf-card-text { font-size: var(--fs-sm); line-height: var(--lh-relaxed); color: var(--text-body-soft); margin: var(--space-8) 0 0; }
  /* footnote — sits under the editor in the left column, filling the space next to the taller cards column */
  .ccf-footnote { margin: var(--space-32) 0 0; font-size: var(--fs-md); line-height: var(--lh-relaxed); color: var(--text-body); }
  .ccf-footnote code { font-family: ui-monospace, "SF Mono", Menlo, Consolas, monospace; background: var(--primary-light); color: var(--primary-dark); border-radius: var(--radius-sm); padding: var(--space-4) var(--space-8); font-size: .9em; }
  .ccf-footnote a { color: var(--primary); text-decoration: underline; text-underline-offset: 2px; }
  @media (max-width: 960px) { .ccf-grid { grid-template-columns: 1fr; } }
</style>
<script>
  (function () {
    if (window.__ccfCopyBound) return;
    window.__ccfCopyBound = true;
    document.addEventListener('click', function (e) {
      var btn = e.target.closest('.ccf-copy');
      if (!btn) return;
      var wrap = btn.closest('.ccf-editor');
      var code = wrap && wrap.querySelector('.ccf-code code');
      if (!code) return;
      var done = function () {
        btn.classList.add('copied');
        var label = btn.querySelector('.ccf-copy-label');
        var prev = label ? label.textContent : '';
        if (label) label.textContent = 'Copied!';
        setTimeout(function () { btn.classList.remove('copied'); if (label) label.textContent = prev || 'Copy'; }, 1800);
      };
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(code.innerText).then(done).catch(function () {});
      } else {
        var ta = document.createElement('textarea'); ta.value = code.innerText; document.body.appendChild(ta); ta.select();
        try { document.execCommand('copy'); done(); } catch (err) {} document.body.removeChild(ta);
      }
    });
  })();
</script>
<?php endif; ?>

<section class="ccf-section<?php echo $ccf['shade'] ? ' is-shaded' : ''; ?>">
  <div class="ccf-wrap">
    <?php if ( $ccf['eyebrow'] || $ccf['title'] || $ccf['lead'] ) : ?>
    <div class="ccf-head">
      <?php if ( $ccf['eyebrow'] ) : ?><p class="ccf-eyebrow"><?php echo esc_html( $ccf['eyebrow'] ); ?></p><?php endif; ?>
      <?php if ( $ccf['title'] ) : ?><h2 class="ccf-h2"><?php echo wp_kses_post( $ccf['title'] ); ?></h2><?php endif; ?>
      <?php if ( $ccf['lead'] ) : ?><p class="ccf-lead"><?php echo wp_kses_post( $ccf['lead'] ); ?></p><?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="ccf-grid">
      <div class="ccf-left">
      <?php if ( $ccf['code'] ) : ?>
      <div class="ccf-editor">
        <div class="ccf-editor-head">
          <span class="ccf-dots" aria-hidden="true"><span></span><span></span><span></span></span>
          <span class="ccf-file"><?php echo esc_html( $ccf['file'] ); ?></span>
          <button type="button" class="ccf-copy" aria-label="Copy code">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
            <span class="ccf-copy-label">Copy</span>
          </button>
        </div>
        <pre class="ccf-code"><code><?php echo $ccf['code']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted pre-highlighted markup ?></code></pre>
      </div>
      <?php endif; ?>
      <?php if ( $ccf['footnote'] ) : ?><p class="ccf-footnote"><?php echo wp_kses_post( $ccf['footnote'] ); ?></p><?php endif; ?>
      </div>

      <?php if ( ! empty( $ccf['items'] ) ) : ?>
      <div class="ccf-covers">
        <?php if ( $ccf['covers_label'] ) : ?><p class="ccf-covers-label"><?php echo wp_kses_post( $ccf['covers_label'] ); ?></p><?php endif; ?>
        <div class="ccf-cards">
          <?php foreach ( $ccf['items'] as $it ) : $it = wp_parse_args( (array) $it, array( 'icon' => '', 'label' => '', 'text' => '' ) ); ?>
          <div class="ccf-card">
            <?php if ( $it['icon'] ) : ?><span class="ccf-card-icon" aria-hidden="true"><?php echo $it['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted inline SVG ?></span><?php endif; ?>
            <div class="ccf-card-body">
              <?php if ( $it['label'] ) : ?><span class="ccf-chip"><?php echo esc_html( $it['label'] ); ?></span><?php endif; ?>
              <?php if ( $it['text'] ) : ?><p class="ccf-card-text"><?php echo wp_kses_post( $it['text'] ); ?></p><?php endif; ?>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>
