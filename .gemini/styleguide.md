# Sovereignty Theme - Code Review Style Guide

## Project Context

This is a WordPress theme forked from Autonomie. It uses the WordPress Coding Standards enforced via PHPCS with a custom "Apermo" ruleset.

## PHP

- Follow the [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/).
- Use tabs for indentation (not spaces).
- All user-facing strings must be translatable using `__()`, `_e()`, `esc_html__()`, `esc_html_e()`, `esc_attr__()`, or `esc_attr_e()` with the `autonomie` text domain.
- Translator comments (`/* translators: ... */`) are required before any translation function call that contains placeholders.
- All output must be properly escaped using `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()`, etc.
- Use post-increment (`$i++`) over pre-increment (`++$i`).
- Keep functions pluggable with `function_exists()` checks where the original theme uses them.
- PHP functions use the `autonomie_` prefix.

## Semantic HTML & IndieWeb

- This theme heavily uses Microformats2 (`h-entry`, `h-card`, `h-feed`, `p-name`, `e-content`, `dt-published`) and Schema.org microdata.
- Do not remove or alter microformat/microdata classes without understanding their semantic purpose.
- Review changes to HTML structure carefully for impact on structured data.

## CSS / SCSS

- Never edit `style.css` directly -- it is generated from SCSS sources in `assets/sass/`.
- Follow WordPress CSS coding standards.
- The theme uses three separate responsive stylesheets (narrow, default, wide) instead of inline media queries.

## JavaScript

- Follow WordPress JavaScript coding standards.
- Use ESLint with the WordPress config.

## Security

- All user input must be sanitized and validated.
- All output must be escaped.
- Nonce verification is required for form submissions.

## Commits

- This project uses Conventional Commits with a 50-char subject / 72-char body limit.
- Each commit should address a single concern.
