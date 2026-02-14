# CLAUDE.md — Sovereignty Theme

WordPress theme forked from [Autonomie](https://github.com/pfefferle/Autonomie) by Matthias Pfefferle.

## Overview

Sovereignty is a semantic, responsive WordPress theme with deep IndieWeb support. It provides microformats2, microdata (Schema.org), and integrates with ActivityPub, Syndication Links, and Post Kinds plugins.

**Active migration:** Features are being cherry-picked from a custom _s-based theme into this Autonomie fork. See `plan.md` for the full migration plan.

## Development

### Prerequisites

- Node.js + npm (for Grunt build)
- Sass (`sass` npm package)
- PHP 7.4+ (theme requirement, but project targets PHP 8.3)

### Build

Uses Grunt for SASS compilation and build tasks:

```bash
npm install          # Install dev dependencies
npx grunt            # Compile SASS, generate readme, create .pot
npx grunt watch      # Watch SCSS files and recompile on change
npx grunt build      # Full build + zip for distribution
```

### SASS → CSS

The build compiles these SCSS → CSS outputs:

| Source | Output | Purpose |
|---|---|---|
| `assets/sass/style.scss` | `style.css` | Main stylesheet |
| `assets/sass/editor-style.scss` | `assets/css/editor-style.css` | Block editor styles |
| `assets/sass/print.scss` | `assets/css/print.css` | Print styles |
| `assets/sass/responsive_narrow.scss` | `assets/css/narrow-width.css` | `< 800px` |
| `assets/sass/responsive_default.scss` | `assets/css/default-width.css` | `800px–1000px` |
| `assets/sass/responsive_wide.scss` | `assets/css/wide-width.css` | `> 1000px` |

After editing any `.scss` file, run `npx grunt sass` (or `npx grunt watch`) to recompile. Do NOT edit `style.css` directly — it is generated. The `string-replace` task injects version/author metadata from `package.json` into the `style.css` header.

### Linting & Static Analysis

```bash
composer lint          # PHPCS (WordPress coding standard + Slevomat + Yoast)
composer lint:fix      # Auto-fix PHP lint issues
composer analyse       # PHPStan level 5 with WordPress stubs
npm run lint:js        # ESLint (WordPress standard)
npm run lint:css       # Stylelint (WordPress SCSS config)
npm run lint:js:fix    # Auto-fix JS lint issues
npm run lint:css:fix   # Auto-fix CSS lint issues
```

### Pre-commit Hooks (husky)

- **pre-commit**: lint-staged runs PHPCS, ESLint, and Stylelint on staged files only
- **commit-msg**: commitlint enforces Conventional Commits with 50/72 rule

### IMPORTANT: Always lint before committing

**Before every commit, run the full lint suite on changed files:**

```bash
composer lint
composer analyse
npm run lint:js
npm run lint:css
```

Do NOT skip this step. If linting fails, fix the issues before committing. The pre-commit hook catches staged files, but always run the full suite manually to catch issues early.

## Architecture

### File Structure

```
├── functions.php              # Setup, enqueue, includes
├── header.php / footer.php    # Global wrappers
├── index.php                  # Main query loop
├── single.php / page.php      # Single post / page
├── archive.php                # Archives
├── 404.php / 500.php          # Error pages
├── image.php                  # Image attachment template
├── page-now.php               # /now page template
├── comments.php / sidebar.php
├── includes/                  # PHP includes
│   ├── template-functions.php # Template tags and helpers
│   ├── widgets.php            # Widget registration (4 sidebars)
│   ├── featured-image.php     # Post thumbnail handling
│   ├── customizer.php         # Theme customizer settings
│   ├── semantics.php          # Microformats2, microdata, search form
│   ├── webactions.php         # IndieWeb web actions
│   ├── feed.php               # RSS/Atom feed extensions
│   └── compat.php             # Backwards compatibility
├── integrations/              # Plugin-specific integrations
│   ├── activitypub.php
│   ├── syndication-links.php
│   └── post-kinds.php
├── templates/                 # Post format templates (content-*.php)
├── template-parts/            # Reusable template parts
├── widgets/                   # Custom widget classes
├── assets/
│   ├── sass/                  # SCSS source (see Build section)
│   ├── css/                   # Compiled CSS (do not edit)
│   ├── js/                    # navigation.js, share.js
│   ├── font/                  # Lato, Merriweather, OpenWeb Icons
│   └── images/                # Starter content images
└── languages/                 # Translation files (.pot, .mo, .po)
```

### Naming Conventions

- All PHP functions use `autonomie_` prefix (legacy from fork, will be renamed)
- Text domain: `autonomie`
- CSS classes follow WordPress conventions
- SCSS variables use `$` prefix in `_vars.scss`, CSS custom properties in `_darkmode.scss`

### Responsive Strategy

Three breakpoint-specific stylesheets loaded via media attributes — not mobile-first `@media` blocks in the main CSS. Each responsive sheet overrides widths and layout:

- `narrow-width.css` — `(max-width: 800px)`
- `default-width.css` — `(min-width: 800px)`
- `wide-width.css` — `(min-width: 1000px)`

### Dark Mode

Currently CSS-only via `prefers-color-scheme: dark` media query in `_darkmode.scss`. Uses CSS custom properties (`--color-white`, `--color-text`, etc.) to swap colors. Plan Phase 3 adds an interactive JS toggle.

### IndieWeb / Semantic Web

Heavy use of:
- **Microformats2**: `h-entry`, `h-card`, `h-feed`, `p-name`, `e-content`, `dt-published`, etc.
- **Microdata**: Schema.org `BlogPosting`, `Comment`, `Person`
- **WebActions**: IndieWeb reply/like/repost actions on posts

Do not remove or alter microformat/microdata classes without understanding their purpose.

## Coding Standards

- Follow WordPress-Core coding standard (tabs for indentation in PHP)
- SCSS uses 2-space indentation
- JS uses 2-space indentation
- Keep functions pluggable (`function_exists()` check) where the original theme uses them
- Maintain i18n — all user-facing strings through `__()` / `_e()` / `esc_html__()` with `autonomie` text domain

## Upstream Attribution

When fixing bugs in code inherited from Autonomie (the original theme by Matthias Pfefferle), add `cc @pfefferle` to the commit message body so he has visibility into issues found in the original code.

## Known Issues

See `plan.md` "Sovereignty: Current State & Issues" section for the full bug list and feature gaps being addressed in the migration.