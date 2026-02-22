# Sovereignty

A semantic, responsive WordPress theme with deep IndieWeb support. Fork of [Autonomie](https://github.com/pfefferle/Autonomie) by Matthias Pfefferle.

## Requirements

- PHP 8.3+
- WordPress 6.8+

## Installation

```bash
composer require apermo/sovereignty
```

## Development

### Prerequisites

- Node.js + npm
- PHP 8.3+ with Composer
- [DDEV](https://ddev.com/) (for local testing)

### Setup

```bash
npm install
composer install
```

### Build

```bash
npx grunt            # Compile SASS, inject metadata, generate .pot
npx grunt watch      # Watch SCSS and recompile on change
npx grunt build      # Full build + zip for distribution
```

### Lint

```bash
composer lint          # PHPCS
composer lint:fix      # Auto-fix PHP issues
composer analyse       # PHPStan (level 5)
npm run lint:js        # ESLint
npm run lint:css       # Stylelint
```

### E2E Tests

```bash
ddev start && ddev orchestrate
npx playwright test
```

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

Copyright (c) 2018 Matthias Pfefferle
Copyright (c) 2025 Christoph Daum

### Bundled Resources

- Some Bootstrap CSS, Copyright Twitter, Inc., The Bootstrap Authors — MIT
- Bundled images (starter content), Copyright Hendrik Cvetko — GPL-2.0-or-later
