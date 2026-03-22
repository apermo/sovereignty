# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/),
and this project adheres to [Semantic Versioning](https://semver.org/).

## [1.3.0] - 2026-03-22

### Added

- Namespace migration: 24 classes under `Apermo\Sovereignty\` with PSR-4 autoloading
- Unit test infrastructure: PHPUnit 11 + Brain Monkey (73 tests, 100 assertions)
- `theme.json` with WP-standard settings and custom `sovereignty` config section
- `Config` class with dot-notation access and `sovereignty_config` filter for multisite
- HTTP 410 tombstone support for deleted posts (custom post status + template)
- System font stack replacing bundled Lato + Merriweather (zero network requests)
- Minified CSS output (`.min.css`) with `SCRIPT_DEBUG` support
- Automated `Tested up to` field via `package.json` + build script
- Install script (`bin/install.sh`) for fresh clone setup
- Title tag helpers (`Tags::site_title_tag()`, `Tags::entry_title_tag()`)
- Page banner render/check helpers (`Functions::has_archive_title()`, `render_archive_title()`, etc.)
- `PostKindsTest` for integration testing
- wpackagist dev dependencies (PWA, Post Kinds) for static analysis
- Playwright E2E test suite expanded from 9 to 31 tests

### Changed

- Replace Grunt with npm scripts (`bin/build.js`) for SASS, placeholders, minification
- Remove generated CSS from git (build via `npm run build`)
- Rename `autonomie_` to `sovereignty_` (text domain, hooks, constants, CSS handles, widget IDs)
- `functions.php` reduced from 600 lines to 50-line bootstrap (`Theme::init()`)
- Template files use `use` statements and static method calls
- Pass explicit `WP_Post` to all post-dependent functions
- Replace `the_content()`/`the_excerpt()` with `get_*` variants for explicit `$post` support
- Rename legacy hooks (`before` → `sovereignty_before`, `before_sidebar` → `sovereignty_before_sidebar`)
- Break down `Semantics::get_semantics()` into per-element helper methods
- Modernize JavaScript: classList API, querySelector, addEventListener, CSS class toggle
- Externalize all hardcoded config values to `theme.json` sovereignty section
- Color palette moved from PHP `add_theme_support` to `theme.json` `settings.color.palette`
- Migrate to `ddev-orchestrate` addon v0.3.0

### Removed

- Grunt and all Grunt dependencies (8 packages)
- Bundled Lato and Merriweather font files (16 files)
- `includes/`, `integrations/`, `widgets/` directories (moved to `src/`)
- All `autonomie_` prefixed functions and hooks
- Hardcoded configuration values across 10 PHP classes
- Unused `entry-nav.php` template part (dead code since 2019)

## [1.2.0] - 2026-03-21

### Added

- GitHub issue templates (bug report, feature request) and PR template
- PR validation workflow (CHANGELOG check, conventional commits)
- CHANGELOG-driven release workflow (automatic GitHub releases)
- Prerelease workflow for release/* branches
- WordPress beta/RC nightly compatibility workflow
- WordPress.org SVN deploy workflow on release
- E2E test workflow using reusable workflows
- Renovate for automated dependency updates (replaces Dependabot)
- PHPStan WordPress rules (`apermo/phpstan-wordpress-rules`)
- PHPStan extension installer for automatic extension discovery
- `.wordpress-org/` assets directory
- ActivityPub plugin as dev dependency for static analysis

### Changed

- Migrate CI to `apermo/reusable-workflows` for PHP linting
- Rename `phpcs.xml` to `phpcs.xml.dist` (allows local overrides)
- Rename `phpstan.neon` to `phpstan.neon.dist` (allows local overrides)
- Add `sovereignty` text domain and `Apermo\Sovereignty` namespace prefix to PHPCS config
- Update `.gitattributes` with comprehensive export-ignore list
- Replace Probot stale config with GitHub Actions stale workflow
- Upgrade `apermo-coding-standards` to v2 with auto-fixes (trailing commas, curly braces)
- Replace deprecated `get_webfinger_resource()` with `Webfinger::get_user_resource()`
- Prefix global variables in template files with `sovereignty_`
- Install WordPress plugin dependencies into `vendor/` instead of `wp-content/`

### Fixed

- All PHPCS errors resolved (missing text domain, unprefixed globals, hook PHPDoc, alignment)
- Legacy hook names documented with `@todo` for future renaming

### Removed

- Dependabot configuration (replaced by Renovate)
- Probot stale bot configuration (replaced by Actions workflow)

## [1.1.1] - 2026-03-14

### Added

- Gemini Code Assist configuration with code review settings and style guide

## [1.1.0] - 2025-02-22

### Added

- GitHub Actions CI workflow (linting, static analysis, E2E tests)
- Playwright E2E test suite with DDEV orchestration
- PHPStan static analysis (level 5 with WordPress stubs)
- PHPCS with custom coding standards (apermo-coding-standards)
- ESLint and Stylelint for JS/SCSS linting
- Husky pre-commit hooks with lint-staged
- Commitlint enforcing Conventional Commits
- DDEV setup replacing Docker Compose for local development
- CLAUDE.md for AI-assisted development

### Changed

- Define AUTONOMIE_VERSION at build time instead of runtime
- Require PHP 8.3 (up from 7.4)
- Enforce strict typing across all PHP files
- Enforce snake_case naming convention (WordPress standard)
- Consolidate redundant template files
- Read asset version from composer.json instead of hardcoding
- Replace `@parse_url` with `wp_parse_url`
- Replace HTML entities with proper apostrophes

### Fixed

- Fatal errors from strict typing enforcement
- Skip link target for accessibility
- Translator comments for i18n compliance
- Inline comment formatting per coding standards

### Removed

- Docker Compose setup (replaced by DDEV)

## [1.0.0] - 2025-01-01

### Added

- Initial fork of [Autonomie](https://github.com/pfefferle/Autonomie)
  by Matthias Pfefferle
- Renamed theme to Sovereignty
- Updated Composer package name to `apermo/sovereignty`

[1.3.0]: https://github.com/apermo/sovereignty/compare/1.2.0...1.3.0
[1.2.0]: https://github.com/apermo/sovereignty/compare/1.1.1...1.2.0
[1.1.1]: https://github.com/apermo/sovereignty/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/apermo/sovereignty/compare/1.0.0...1.1.0
[1.0.0]: https://github.com/apermo/sovereignty/releases/tag/1.0.0
