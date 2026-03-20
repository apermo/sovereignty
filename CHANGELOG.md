# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/),
and this project adheres to [Semantic Versioning](https://semver.org/).

## [1.2.0] - Unreleased

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

### Changed

- Migrate CI to `apermo/reusable-workflows` for PHP linting
- Rename `phpcs.xml` to `phpcs.xml.dist` (allows local overrides)
- Rename `phpstan.neon` to `phpstan.neon.dist` (allows local overrides)
- Add `sovereignty` text domain and `Apermo\Sovereignty` namespace prefix to PHPCS config
- Update `.gitattributes` with comprehensive export-ignore list
- Replace Probot stale config with GitHub Actions stale workflow

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

[1.2.0]: https://github.com/apermo/sovereignty/compare/1.1.1...1.2.0
[1.1.1]: https://github.com/apermo/sovereignty/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/apermo/sovereignty/compare/1.0.0...1.1.0
[1.0.0]: https://github.com/apermo/sovereignty/releases/tag/1.0.0
