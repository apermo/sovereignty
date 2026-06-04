<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Integration;

use lloc\Msls\MslsBlog;

/**
 * Multisite Language Switcher integration: renders the switcher next to the search form.
 *
 * Builds the theme's own markup from the plugin's structured blog collection so the
 * current language is shown and labels are friendly two-letter codes, independent of
 * the MSLS Display admin setting.
 *
 * @link https://github.com/lloc/Multisite-Language-Switcher
 *
 * @package Sovereignty
 */
class Msls {

	/**
	 * Register integration hooks.
	 *
	 * @return void
	 */
	public static function register(): void {
		add_action( 'sovereignty_after_search', [ self::class, 'display' ] );
	}

	/**
	 * Display the language switcher next to the search form.
	 *
	 * @return void
	 */
	public static function display(): void {
		if ( ! \function_exists( 'msls_blog_collection' ) || ! \function_exists( 'msls_options' ) ) {
			return;
		}

		$languages = self::get_languages();

		if ( empty( $languages ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- render() escapes every dynamic value.
		echo self::render( $languages );
	}

	/**
	 * Collects the current and translated languages from the MSLS blog collection.
	 *
	 * Returns an empty array when no other language has a translation for the current
	 * content, so single-language pages render nothing.
	 *
	 * @return array<int, array{label: string, locale: string, url: string, current: bool}>
	 */
	private static function get_languages(): array {
		$collection = msls_blog_collection();
		$options    = msls_options();

		$languages = [];
		$has_other = false;

		foreach ( $collection->get_objects() as $blog ) {
			if ( $collection->is_current_blog( $blog ) ) {
				$languages[] = self::language( $blog, '', true );
				continue;
			}

			$url = $blog->get_url( $options );

			if ( empty( $url ) ) {
				continue;
			}

			$has_other   = true;
			$languages[] = self::language( $blog, (string) $url, false );
		}

		return $has_other ? $languages : [];
	}

	/**
	 * Builds a single language entry from an MSLS blog.
	 *
	 * @param MslsBlog $blog    The blog representing a language.
	 * @param string   $url     Translation URL, empty for the current language.
	 * @param bool     $current Whether this is the current language.
	 *
	 * @return array{label: string, locale: string, url: string, current: bool}
	 */
	private static function language( MslsBlog $blog, string $url, bool $current ): array {
		return [
			'label'   => \strtoupper( $blog->get_alpha2() ),
			'locale'  => $blog->get_language(),
			'url'     => $url,
			'current' => $current,
		];
	}

	/**
	 * Renders the switcher markup.
	 *
	 * @param array<int, array{label: string, locale: string, url: string, current: bool}> $languages Language entries.
	 *
	 * @return string
	 */
	private static function render( array $languages ): string {
		$html = '<nav class="language-switcher" aria-label="' . esc_attr__( 'Languages', 'sovereignty' ) . '">';

		foreach ( $languages as $language ) {
			$alpha2 = \strtolower( \substr( $language['locale'], 0, 2 ) );

			if ( $language['current'] ) {
				$html .= \sprintf(
					'<span class="current" aria-current="page" lang="%s">%s</span>',
					esc_attr( $alpha2 ),
					esc_html( $language['label'] ),
				);
				continue;
			}

			$html .= \sprintf(
				'<a href="%s" hreflang="%s">%s</a>',
				esc_url( $language['url'] ),
				esc_attr( $alpha2 ),
				esc_html( $language['label'] ),
			);
		}

		return $html . '</nav>';
	}
}
