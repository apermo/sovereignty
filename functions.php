<?php
/**
 * Sovereignty theme entry point.
 *
 * @package Sovereignty
 */
use Apermo\Sovereignty\Theme;

if ( ! file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	add_action(
		'admin_notices',
		// phpcs:ignore Universal.FunctionDeclarations.NoLongClosures.ExceedsMaximum -- Self-contained notice.
		static function (): void {
			$theme = wp_get_theme();
			printf(
				'<div class="notice notice-error"><p><strong>%s:</strong> %s</p></div>',
				esc_html( $theme->get( 'Name' ) ),
				wp_kses_post(
					sprintf(
						/* translators: %s: composer install command */
						__( 'Please run %s to install the required dependencies.', 'sovereignty' ),
						'<code>composer install</code>',
					),
				),
			);
		},
	);
	return;
}

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/version.php';

Theme::init();

/**
 * WP core polyfill: get_self_link() for WP < 5.3.
 */
if ( ! function_exists( 'get_self_link' ) ) {
	/**
	 * Returns the link for the currently displayed feed.
	 *
	 * @since 5.3.0
	 *
	 * @return string Correct link for the atom:self element.
	 */
	function get_self_link(): string { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- WP core polyfill.
		$host = wp_parse_url( home_url() );
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Polyfill for WP core function.
		return set_url_scheme( 'http://' . $host['host'] . wp_unslash( $_SERVER['REQUEST_URI'] ) );
	}
}
