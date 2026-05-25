<?php
/**
 * Sovereignty theme entry point.
 *
 * @package Sovereignty
 */
use Apermo\Sovereignty\Theme;

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

if ( ! class_exists( Theme::class ) ) {
	if ( is_admin() ) {
		add_action(
			'admin_notices',
			// phpcs:ignore Universal.FunctionDeclarations.NoLongClosures.ExceedsMaximum -- Self-contained notice.
			static function (): void {
				$theme = wp_get_theme();
				wp_admin_notice(
					sprintf(
						'<strong>%s:</strong> %s',
						esc_html( $theme->get( 'Name' ) ),
						wp_kses_post(
							sprintf(
								/* translators: %s: composer install command */
								__( 'Please run %s to install the required dependencies.', 'sovereignty' ),
								'<code>composer install</code>',
							),
						),
					),
					[ 'type' => 'error' ],
				);
			},
		);
		return;
	}

	if ( current_user_can( 'edit_posts' ) ) {
		wp_die(
			wp_kses_post(
				sprintf(
					/* translators: %s: composer install command */
					__( 'Sovereignty theme dependencies are missing. Please run %s to install them.', 'sovereignty' ),
					'<code>composer install</code>',
				),
			),
			esc_html__( 'Theme Dependencies Missing', 'sovereignty' ),
			[ 'response' => 500 ],
		);
	}

	wp_die(
		esc_html__( 'Oops! Something went wrong.', 'sovereignty' ),
		esc_html__( 'Something went wrong', 'sovereignty' ),
		[ 'response' => 500 ],
	);
}

if ( file_exists( __DIR__ . '/version.php' ) ) {
	require __DIR__ . '/version.php';
}

if ( ! defined( 'SOVEREIGNTY_VERSION' ) ) {
	define( 'SOVEREIGNTY_VERSION', 'dev' );
}

Theme::init();
