# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/),
and this project adheres to [Semantic Versioning](https://semver.org/).

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

[1.1.0]: https://github.com/apermo/sovereignty/compare/1.0.0...1.1.0
[1.0.0]: https://github.com/apermo/sovereignty/releases/tag/1.0.0
