<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Integration;

/**
 * Multisite Language Switcher integration: renders the switcher after the primary navigation.
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
		add_action( 'sovereignty_after_navigation', [ self::class, 'display' ] );
	}

	/**
	 * Display the language switcher after the primary navigation.
	 *
	 * @return void
	 */
	public static function display(): void {
		if ( ! \function_exists( 'msls_get_switcher' ) ) {
			return;
		}

		$switcher = msls_get_switcher();

		if ( $switcher === '' ) {
			return; // No translation exists for this content — render nothing.
		}

		echo '<nav class="language-switcher" aria-label="' . esc_attr__( 'Languages', 'sovereignty' ) . '">';
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- msls_get_switcher() returns safe link markup.
		echo $switcher;
		echo '</nav>';
	}
}
