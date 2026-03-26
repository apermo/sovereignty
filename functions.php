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
