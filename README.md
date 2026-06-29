# Ethora Theme — Design System Skill

A **[Claude Code](https://claude.com/claude-code) skill** that encodes the design
contract for the Ethora WordPress theme (`ethora-theme`): the non-negotiable design
rules **plus** a catalog of ready-made, reusable section blocks (with screenshots and
copy-paste usage). Install it into a theme and any developer — or Claude — can build
or redesign pages without drifting off-brand.

## What's inside

| File | What it is |
|---|---|
| `SKILL.md` | Entry point — the hard rules (tokens only, max 1200px width, Open Sans, brand blue, 16px spacing) + workflow + block-catalog table. Auto-loaded by Claude Code. |
| `references/BLOCKS.md` | Full catalog: each reusable block with a **screenshot**, prop table and a copy-paste `get_template_part()` snippet. |
| `references/screenshots/` | Rendered screenshots of every block. |
| `references/screenshot-blocks.mjs` | Playwright script to regenerate the screenshots. |

The skill is designed to live **inside the theme** at `.claude/skills/ethora-theme/`.
Its relative links (`../../../css/tokens.css`, `../../../DESIGN.md`,
`../../../template-parts/`) resolve to the theme root once installed there.

## Install

From the **root of your theme** (the folder that contains `css/tokens.css` and
`DESIGN.md`):

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

## Update

```bash
cd .claude/skills/ethora-theme && git pull
```

## How to use

1. Read `SKILL.md` for the hard rules and the workflow.
2. Before building a section, check `references/BLOCKS.md` — reuse a block via
   `get_template_part()` instead of inventing a new layout.
3. Build with `var(--token)` values only (the theme's `css/tokens.css` is the single
   source of truth). Never hardcode hex/px.

## Regenerate screenshots

After editing a block (Local must be running, theme served at `ethora.local`):

```bash
node references/screenshot-blocks.mjs references/screenshots
# then downsize: sips --resampleWidth 1200 references/screenshots/*.png
```

---

Maintained by [Dappros](https://github.com/dappros) for the Ethora theme. The
canonical source of design values is the theme's `css/tokens.css`; this skill
restates the rules so they're enforced everywhere the skill is installed.
