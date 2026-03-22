# CLAUDE.md — Sovereignty Theme

WordPress theme forked from [Autonomie](https://github.com/pfefferle/Autonomie) by Matthias Pfefferle.

## Overview

Sovereignty is a semantic, responsive WordPress theme with deep IndieWeb support. It provides microformats2,
microdata (Schema.org), and integrates with ActivityPub, Syndication Links, and Post Kinds plugins.

## Development

### Prerequisites

- Node.js + npm
- PHP 8.3+ with Composer
- [DDEV](https://ddev.com/) (for local testing)

### Quick Start

```bash
./bin/install.sh --dev   # Install dependencies + build CSS
ddev start && ddev orchestrate   # Set up local WordPress
```

### Build

```bash
npm run build            # Compile SASS, inject metadata, generate minified CSS
npm run build:sass       # SASS only
npm run watch            # Watch SCSS and recompile on change
```

After editing any `.scss` file, run `npm run build` or `npm run watch`. Do NOT edit `style.css` directly — it is
generated. The build script (`bin/build.js`) injects version/author metadata from `package.json` into the `style.css`
header.

### SASS → CSS

| Source | Output | Purpose |
|---|---|---|
| `assets/sass/style.scss` | `style.css` + `style.min.css` | Main stylesheet |
| `assets/sass/editor-style.scss` | `assets/css/editor-style.css` | Block editor styles |
| `assets/sass/print.scss` | `assets/css/print.css` | Print styles |
| `assets/sass/responsive_narrow.scss` | `assets/css/narrow-width.css` | `< 800px` |
| `assets/sass/responsive_default.scss` | `assets/css/default-width.css` | `800px–1000px` |
| `assets/sass/responsive_wide.scss` | `assets/css/wide-width.css` | `> 1000px` |

### Linting & Static Analysis

```bash
composer cs              # PHPCS (errors only, warnings suppressed)
composer cs:fix          # Auto-fix PHP issues
composer analyse         # PHPStan level 5 with WordPress stubs
npm run lint:js          # ESLint (WordPress standard)
npm run lint:css         # Stylelint (WordPress SCSS config)
```

### Testing

```bash
composer test:unit       # PHPUnit unit tests (73 tests)
npx playwright test      # E2E tests (31 tests, requires DDEV)
```

### Pre-commit Hooks (husky)

- **pre-commit**: lint-staged runs PHPCS, ESLint, and Stylelint on staged files only
- **commit-msg**: commitlint enforces Conventional Commits with 50/72 rule

### IMPORTANT: Always lint before committing

**Before every commit, run the full lint suite on changed files:**

```bash
composer cs
composer analyse
npm run lint:js
npm run lint:css
```

## Architecture

### Namespace & Autoloading

All PHP classes live under `Apermo\Sovereignty\` with PSR-4 autoloading via Composer (`src/` directory).
`functions.php` is a thin bootstrap that loads the autoloader and calls `Theme::init()`.

### File Structure

```
├── functions.php              # Bootstrap: autoloader + Theme::init()
├── theme.json                 # WP settings + sovereignty config section
├── version.php                # Generated version constant
├── header.php / footer.php    # Global wrappers
├── single.php / page.php      # Single post / page
├── archive.php / index.php    # Archives / main loop
├── 404.php / 500.php          # Error pages
├── tombstone.php              # HTTP 410 template
├── page-now.php               # /now page template
├── src/                       # Namespaced PHP classes
│   ├── Theme.php              # Hook registration
│   ├── Config.php             # theme.json config loader
│   ├── Setup.php              # Theme supports, nav menus
│   ├── Assets.php             # Script/style enqueue
│   ├── Semantics.php          # Microformats2, microdata
│   ├── Featured_Image.php     # Thumbnails, post covers
│   ├── Feed.php               # RSS/Atom extensions
│   ├── Tombstone.php          # HTTP 410 support
│   ├── Template/              # Template tags and helpers
│   ├── Integration/           # Plugin integrations
│   └── Widget/                # Custom widget classes
├── templates/                 # Post format templates (content-*.php)
├── template-parts/            # Reusable template parts
├── tests/                     # Unit + E2E tests
├── assets/
│   ├── sass/                  # SCSS source
│   ├── css/                   # Compiled CSS (gitignored)
│   ├── js/                    # navigation.js, share.js
│   ├── font/                  # OpenWeb Icons
│   └── images/                # Starter content images
└── languages/                 # Translation files
```

### Configuration

Theme configuration lives in `theme.json`:
- **`settings.*`**: Standard WordPress settings (color palette, layout, typography) — processed by WP natively
- **`sovereignty.*`**: Custom theme config (breakpoints, embed dimensions, PWA, Schema.org types, etc.)

Access config in PHP via `Config::get('sovereignty.embed.width')`, `Config::int()`, `Config::string()`, etc.

The `sovereignty_config` filter allows per-site overrides in multisite.

### Naming Conventions

- Namespace: `Apermo\Sovereignty\`
- Function prefix: `sovereignty_` (for any remaining global functions)
- Text domain: `sovereignty`
- CSS handles: `sovereignty-style`, `sovereignty-print-style`, etc.
- Hook names: `sovereignty_before`, `sovereignty_entry_footer`, etc.

### Responsive Strategy

Three breakpoint-specific stylesheets loaded via media attributes (configurable in `theme.json`):

- `narrow-width.css` — `(max-width: 800px)`
- `default-width.css` — `(min-width: 800px)`
- `wide-width.css` — `(min-width: 1000px)`

### Dark Mode

CSS-only via `prefers-color-scheme: dark` media query in `_darkmode.scss`. Uses CSS custom properties
(`--color-white`, `--color-text`, etc.) to swap colors.

### IndieWeb / Semantic Web

Heavy use of:
- **Microformats2**: `h-entry`, `h-card`, `h-feed`, `p-name`, `e-content`, `dt-published`, etc.
- **Microdata**: Schema.org `BlogPosting`, `Comment`, `Person`
- **WebActions**: IndieWeb reply/like/repost actions on posts

Do not remove or alter microformat/microdata classes without understanding their purpose.

## Coding Standards

- Tabs for indentation across all file types (see `.editorconfig`)
- All `src/` classes use `declare(strict_types=1)` and fully qualified native PHP functions
- i18n: all user-facing strings through `__()` / `_e()` / `esc_html__()` with `sovereignty` text domain
- Template files use `use` statements and call static class methods directly

## Upstream Attribution

Only add `cc @pfefferle` to the commit message body when fixing **actual bugs** in code inherited from Autonomie
(the original theme by Matthias Pfefferle). Do not tag for style changes, refactoring, linting fixes, or personal
preferences. When in doubt, ask before tagging.

## Local Testing (DDEV)

The project uses DDEV with the `ddev-orchestrate` addon for local WordPress development and E2E testing.

```bash
ddev start && ddev orchestrate   # First-time setup
ddev orchestrate --reset         # Reset and re-provision
npx playwright test              # Run E2E tests
```
