# Sovereignty

A semantic, responsive WordPress theme with deep IndieWeb support. Fork of
[Autonomie](https://github.com/pfefferle/Autonomie) by Matthias Pfefferle.

## Requirements

- PHP 8.3+
- WordPress 6.2+

## Installation

```bash
composer require apermo/sovereignty
```

Or download the latest release from [GitHub](https://github.com/apermo/sovereignty/releases).

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
npm run build:sass       # SASS only (no placeholders/minification)
npm run watch            # Watch SCSS and recompile on change
```

### Lint

```bash
composer cs              # PHPCS (errors only)
composer cs:fix          # Auto-fix PHP issues
composer analyse         # PHPStan (level 5)
npm run lint:js          # ESLint
npm run lint:css         # Stylelint
```

### Test

```bash
composer test:unit       # PHPUnit (73 unit tests)
npx playwright test      # E2E tests (31 tests, requires DDEV)
```

### E2E Tests

```bash
ddev start && ddev orchestrate
npx playwright test
```

## Architecture

The theme uses namespaced PHP classes under `Apermo\Sovereignty\` with PSR-4 autoloading:

```
src/
├── Theme.php              # Hook registration (entry point)
├── Config.php             # theme.json config loader
├── Setup.php              # Theme supports, nav menus
├── Assets.php             # Script/style enqueue
├── Semantics.php          # Microformats2, Schema.org
├── Featured_Image.php     # Post thumbnails, post covers
├── Feed.php               # RSS/Atom extensions
├── Template/              # Template tags and helpers
├── Integration/           # Plugin integrations
└── Widget/                # Custom widgets
```

Configuration is centralized in `theme.json` with standard WordPress settings and a custom `sovereignty` section.
The `sovereignty_config` filter allows per-site overrides in multisite.

## Supported Plugins

- [ActivityPub](https://wordpress.org/plugins/activitypub/)
- [Post Kinds](https://wordpress.org/plugins/indieweb-post-kinds/)
- [Syndication Links](https://wordpress.org/plugins/syndication-links/)

## Web Semantics

The theme markup uses microformats, microformats2, and microdata (Schema.org):

### Microformats (v1)

- [hAtom](http://microformats.org/wiki/hatom)
- [hCard](http://microformats.org/wiki/hcard)
- [rel-tag](http://microformats.org/wiki/rel-tag)
- [XFN](http://microformats.org/wiki/xfn)

### Microformats2

- [h-feed](http://microformats.org/wiki/h-feed) / [h-entry](http://microformats.org/wiki/h-entry)
- [h-card](http://microformats.org/wiki/h-card)

### Microdata (Schema.org)

- [Blog](https://schema.org/Blog)
- [BlogPosting](https://schema.org/BlogPosting)
- [Comment](https://schema.org/Comment)
- [WebPage](https://schema.org/WebPage)
- [Person](https://schema.org/Person)

### IndieWeb

- [Webactions](https://indieweb.org/webactions) — reply, like, and repost actions across sites

## License

MIT License — see [LICENSE](LICENSE) for details.

Copyright (c) 2018-2025 Matthias Pfefferle
Copyright (c) 2025-2026 Christoph Daum

### Bundled Resources

- Some Bootstrap CSS, Copyright Twitter, Inc., The Bootstrap Authors — MIT
- [OpenWeb Icons](https://pfefferle.dev/openwebicons/) — SIL OFL 1.1
- Bundled images (starter content), Copyright Hendrik Cvetko — GPL-2.0-or-later
