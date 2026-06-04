<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Integration;

use lloc\Msls\MslsBlog;
use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsOptions;
use lloc\Msls\OptionsInterface;

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
		if ( ! \function_exists( 'msls_blog_collection' ) ) {
			return;
		}

		$languages = self::get_languages( msls_blog_collection(), MslsOptions::create() );

		if ( empty( $languages ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- render() escapes every dynamic value.
		echo self::render( $languages );
	}

	/**
	 * Collects every language from the MSLS blog collection.
	 *
	 * Each alternate language links to the translated content when it exists, otherwise
	 * to that language's home page, so the full set of languages is always offered.
	 * Returns an empty array when no other language exists, so single-language sites
	 * render nothing.
	 *
	 * @param MslsBlogCollection $collection The plugin's blog collection.
	 * @param OptionsInterface   $options    Request-aware options used to resolve URLs.
	 *
	 * @return array<int, array{label: string, lang: string, url: string, current: bool}>
	 */
	private static function get_languages( MslsBlogCollection $collection, OptionsInterface $options ): array {
		$languages = [];
		$has_other = false;

		foreach ( $collection->get_objects() as $blog ) {
			if ( $collection->is_current_blog( $blog ) ) {
				$languages[] = self::language( $blog, '', true );
				continue;
			}

			$url = $blog->get_url( $options );

			if ( empty( $url ) ) {
				$url = get_home_url( (int) $blog->userblog_id );
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
	 * @return array{label: string, lang: string, url: string, current: bool}
	 */
	private static function language( MslsBlog $blog, string $url, bool $current ): array {
		return [
			'label'   => \strtoupper( $blog->get_alpha2() ),
			'lang'    => \strtolower( $blog->get_alpha2() ),
			'url'     => $url,
			'current' => $current,
		];
	}

	/**
	 * Renders the switcher markup.
	 *
	 * @param array<int, array{label: string, lang: string, url: string, current: bool}> $languages Language entries.
	 *
	 * @return string
	 */
	private static function render( array $languages ): string {
		$html = '<nav class="language-switcher" aria-label="' . esc_attr__( 'Languages', 'sovereignty' ) . '">';

		foreach ( $languages as $language ) {
			if ( $language['current'] ) {
				$html .= \sprintf(
					'<span class="current" aria-current="page" lang="%s">%s</span>',
					esc_attr( $language['lang'] ),
					esc_html( $language['label'] ),
				);
				continue;
			}

			$html .= \sprintf(
				'<a href="%s" hreflang="%s">%s</a>',
				esc_url( $language['url'] ),
				esc_attr( $language['lang'] ),
				esc_html( $language['label'] ),
			);
		}

		return $html . '</nav>';
	}
}
