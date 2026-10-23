<?php
/**
 * Shared styles for case-study pages (page-case-study-*.php).
 *
 * Every case study is built from the theme's reusable blocks plus a few prose pieces
 * that only case studies use: overview prose, captioned screenshots, the "early engine"
 * era note, a before/after table, a single customer quote and the light outcome statement. They live here so
 * a new case study is a template of block calls, not another copy of this CSS.
 *
 *   <main class="cs-page">
 *     <?php get_template_part( 'template-parts/case-study-styles' ); ?>
 *
 * Tokens only (css/tokens.css). Prints once per request.
 *
 * @package ethora-theme
 */

if ( ! defined( 'ABSPATH' ) || ! empty( $GLOBALS['ethora_cs_styles'] ) ) {
	return;
}
$GLOBALS['ethora_cs_styles'] = true;
?>
<style>
  /* Section vertical rhythm — collapse the seam between two adjacent light sections. */
  .cs-page .shs-sec:has(+ .shs-sec) > * { padding-bottom: 0 !important; }
  .cs-page .shs-sec + .shs-sec > *      { padding-top: var(--section-y-sm) !important; }

  /* Prose block. */
  .cs-prose { padding: var(--section-y) var(--section-x); }
  .cs-prose-wrap { max-width: var(--container-md); margin: 0 auto; }
  .cs-eyebrow { font-family: var(--font-mono); font-weight: var(--fw-medium); font-size: var(--fs-eyebrow); letter-spacing: var(--tracking-wide); text-transform: uppercase; color: var(--primary); margin: 0 0 var(--space-16); }
  .cs-prose h2 { font-weight: var(--fw-semibold); font-size: var(--fs-h2); line-height: 1.1; letter-spacing: -0.01em; color: var(--ink); margin: 0; }
  .cs-prose p { font-size: var(--fs-lg); line-height: var(--lh-relaxed); color: var(--text-body); margin: var(--space-16) 0 0; }
  .cs-prose ul { margin: var(--space-16) 0 0; padding-left: 1.25em; }
  .cs-prose li { font-size: var(--fs-lg); line-height: var(--lh-relaxed); color: var(--text-body); margin: 0 0 var(--space-8); }
  .cs-prose p a, .cs-prose li a { color: var(--primary); text-decoration: underline; text-underline-offset: 2px; }
  .cs-prose code { font-family: var(--font-mono); font-size: 0.9em; }
  .cs-prose .cs-logo { height: 40px; width: auto; display: block; }

  /* Figure — a captioned screenshot inside the prose column. */
  .cs-fig { margin: var(--space-32) 0 0; }
  .cs-fig img { display: block; width: 100%; height: auto; border: 1px solid var(--border, #e4e9f2); border-radius: var(--radius-2xl, 24px); background: var(--white); }
  .cs-fig figcaption { margin: var(--space-16) 0 0; font-size: var(--fs-sm, .9rem); line-height: var(--lh-relaxed); color: var(--text-muted, #5b6b8c); text-align: center; }

  /* Era note — a quiet band flagging a historical project. */
  .cs-era { padding: 0 var(--section-x); }
  .cs-era-wrap { max-width: var(--container-md); margin: 0 auto; border-left: 3px solid var(--primary); padding: var(--space-16) var(--space-16); background: var(--surface-shade, #f6f8fc); border-radius: 0 var(--radius-lg, 12px) var(--radius-lg, 12px) 0; }
  .cs-era-wrap p { margin: 0; font-size: var(--fs-sm, .95rem); line-height: var(--lh-relaxed); color: var(--text-muted, #5b6b8c); }
  .cs-era-wrap a { color: var(--primary); }

  /* Before / after table — the same question asked of two setups (scrolls sideways on phones). */
  .cs-ba-scroll { margin: var(--space-32) 0 0; overflow-x: auto; -webkit-overflow-scrolling: touch; }
  .cs-ba { width: 100%; min-width: 560px; border-collapse: collapse; font-size: var(--fs-sm, .95rem); line-height: var(--lh-relaxed); }
  .cs-ba th, .cs-ba td { text-align: left; vertical-align: top; padding: var(--space-12, 12px) var(--space-16); border-bottom: 1px solid var(--border, #e4e9f2); }
  .cs-ba thead th { font-family: var(--font-mono); font-size: var(--fs-eyebrow); letter-spacing: var(--tracking-wide); text-transform: uppercase; color: var(--text-muted, #5b6b8c); font-weight: var(--fw-medium); }
  .cs-ba tbody th { color: var(--ink); font-weight: var(--fw-semibold); width: 22%; }
  .cs-ba td { color: var(--text-body); }
  .cs-ba td.cs-ba-after { color: var(--ink); }

  /* Single customer quote. */
  .cs-quote { padding: var(--section-y) var(--section-x); }
  .cs-quote figure { max-width: var(--container-md); margin: 0 auto; text-align: center; }
  .cs-quote blockquote { margin: 0; font-size: var(--fs-h3); line-height: var(--lh-relaxed); color: var(--ink); font-weight: var(--fw-medium); }
  .cs-quote figcaption { margin-top: var(--space-32); display: inline-flex; align-items: center; gap: var(--space-16); color: var(--text-body); text-align: left; }
  .cs-quote figcaption img { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; }
  .cs-quote figcaption strong { display: block; color: var(--ink); }

  /* Outcome — a LIGHT centred statement (never a second blue band before the blue CTA). */
  .cs-prose.cs-outcome { text-align: center; }
  .cs-prose.cs-outcome p { margin-left: auto; margin-right: auto; max-width: 62ch; }
</style>
