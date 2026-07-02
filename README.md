# Ethora Theme — Design System Skill

A **[Claude Code](https://claude.com/claude-code) skill** that encodes the design
contract for the Ethora WordPress theme (`ethora-theme`): the non-negotiable design
rules **plus** a catalog of ready-made, reusable section blocks (with screenshots and
copy-paste usage). Any developer — or Claude — can build or redesign pages without
drifting off-brand.

This repository is **self-contained** — it bundles everything the skill references
(tokens, human guide, rules, and the actual block code), so you can read and use it
standalone, not only from inside the theme.

## What's inside

| File / dir | What it is |
|---|---|
| `SKILL.md` | Entry point — the hard rules (tokens only, max 1200px width, Open Sans, brand blue, 16px spacing) + workflow + block-catalog table. Auto-loaded by Claude Code. |
| `DESIGN.md` | Human-readable design guide: palette, type scale, spacing, radius, containers, button/toggle/slider rules, quality floor. |
| `CLAUDE.md` | Project instructions (the same hard rules, restated as an always-on contract for Claude Code working in the theme). |
| `css/tokens.css` | **The machine source of truth** — every colour/size/spacing/radius/width token in one `:root`. Build only with `var(--token)`. |
| `css/primitives.css` | **The canonical interactive primitives** — CTA buttons (`.btn-*`), slider/nav buttons (`.slider-btn`), toggle switch (`.ppc-toggle`), all token-based. Load after `tokens.css` and use these classes; never restyle. |
| `template-parts/` | The actual reusable block partials (`section-*.php`). Each is self-contained (ships its own `<style>`) and driven by `get_template_part()` args. |
| `references/BLOCKS.md` | Full catalog: each reusable block + the **Core UI primitives** with a **screenshot**, prop table and a copy-paste `get_template_part()` snippet. |
| `references/screenshots/` | Rendered screenshots of every block and primitive. |
| `references/screenshot-blocks.mjs` | Playwright script to regenerate the screenshots. |

All in-repo links resolve locally, so the docs are navigable straight from a
standalone clone.

## Install into a theme

Drop the skill into your theme's `.claude/skills/` so Claude Code auto-discovers it:

```bash
git clone https://github.com/dappros/ethora-design-system.git .claude/skills/ethora-theme
```

That places `SKILL.md` at `.claude/skills/ethora-theme/SKILL.md`. Claude Code
discovers it automatically — it triggers on any UI / frontend / layout work in the
theme. To confirm it's loaded, run `/skills` (or just ask Claude to "use the
ethora-theme skill").

> Prefer not to keep a nested git repo? Download the files instead:
> ```bash
> curl -L https://github.com/dappros/ethora-design-system/archive/refs/heads/main.tar.gz \
>   | tar -xz --strip-components=1 -C .claude/skills/ethora-theme
> ```

The bundled `css/tokens.css` and `template-parts/` are the canonical copies. In a live
Ethora theme the same files also exist at the theme root — treat this repo as the
source and keep the theme in sync with it (or vice-versa) when tokens or blocks change.

## Update

```bash
cd .claude/skills/ethora-theme && git pull
```

## How to use

1. Read `SKILL.md` for the hard rules and the workflow; skim `DESIGN.md` for the values.
2. Before building a section, check `references/BLOCKS.md` — reuse a block from
   `template-parts/` via `get_template_part()` instead of inventing a new layout.
3. Build with `var(--token)` values only (`css/tokens.css` is the single source of
   truth). Never hardcode hex/px. Reuse the locked **Core UI primitives** (CTA buttons,
   slider/nav buttons, toggle switch, `--gradient-brand` blue section) exactly — their
   CSS ships in `css/primitives.css`, loaded right after `css/tokens.css`:
   ```html
   <link rel="stylesheet" href="css/tokens.css">
   <link rel="stylesheet" href="css/primitives.css">
   ```

## Regenerate screenshots

After editing a block (Local must be running, theme served at `ethora.local`):

```bash
node references/screenshot-blocks.mjs references/screenshots
# then downsize: sips --resampleWidth 1200 references/screenshots/*.png
```

---

Maintained by [Dappros](https://github.com/dappros) for the Ethora theme. The
canonical source of design values is `css/tokens.css`; this skill restates the rules
so they're enforced everywhere the skill is installed.
