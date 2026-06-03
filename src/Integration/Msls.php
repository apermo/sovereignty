<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Integration;

/**
 * Multisite Language Switcher integration: renders the switcher next to the search form.
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
		if ( ! \function_exists( 'msls_get_switcher' ) ) {
			return;
		}

		$switcher = msls_get_switcher( [] );

		if ( ! \is_string( $switcher ) || \trim( $switcher ) === '' ) {
			return; // No translation, or empty/whitespace markup — render nothing.
		}

		echo '<nav class="language-switcher" aria-label="' . esc_attr__( 'Languages', 'sovereignty' ) . '">';
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- msls_get_switcher() returns safe link markup.
		echo $switcher;
		echo '</nav>';
	}
}
