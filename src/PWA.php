<?php

declare(strict_types=1);

namespace Apermo\Sovereignty;

/**
 * PWA support — web manifest, favicons, and app icons.
 *
 * @package Sovereignty
 */
class PWA {

	/**
	 * Register the manifest query variable.
	 *
	 * @param array $vars Existing query vars.
	 *
	 * @return array Modified query vars.
	 */
	public static function manifest_query_var( array $vars ): array {
		$vars[] = 'sovereignty_manifest';
		return $vars;
	}

	/**
	 * Serve the web app manifest as JSON when the query var is set.
	 *
	 * @return void
	 */
	public static function manifest_template_redirect(): void {
		if ( empty( get_query_var( 'sovereignty_manifest' ) ) ) {
			return;
		}

		$theme_uri = get_template_directory_uri();
		$manifest  = [
			'name'             => get_bloginfo( 'name' ),
			'short_name'       => get_bloginfo( 'name' ),
			'description'      => get_bloginfo( 'description' ),
			'icons'            => [
				[
					'src'   => $theme_uri . '/assets/icons/192x192.png',
					'sizes' => '192x192',
					'type'  => 'image/png',
				],
				[
					'src'   => $theme_uri . '/assets/icons/512x512.png',
					'sizes' => '512x512',
					'type'  => 'image/png',
				],
				[
					'src'   => $theme_uri . '/assets/favicon.svg',
					'sizes' => 'any',
					'type'  => 'image/svg+xml',
				],
			],
			'theme_color'      => '#eeeeee',
			'background_color' => '#eeeeee',
			'display'          => 'browser',
		];

		\header( 'Content-Type: application/manifest+json' );
		echo wp_json_encode( $manifest, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES );
		exit();
	}

	/**
	 * Output favicon, app icon, and manifest links in wp_head.
	 *
	 * Skipped when a WordPress Site Icon is configured.
	 *
	 * @return void
	 */
	public static function head(): void {
		if ( has_site_icon() ) {
			return;
		}

		$theme_uri = get_template_directory_uri();

		\printf( '<link rel="manifest" href="%s">' . \PHP_EOL, esc_url( home_url( '?sovereignty_manifest=1' ) ) );
		\printf( '<link rel="icon" href="%s" type="image/svg+xml">' . \PHP_EOL, esc_url( $theme_uri . '/assets/favicon.svg' ) );
		\printf( '<link rel="icon" href="%s" sizes="32x32">' . \PHP_EOL, esc_url( $theme_uri . '/assets/favicon.ico' ) );
		\printf( '<link rel="apple-touch-icon" href="%s">' . \PHP_EOL, esc_url( $theme_uri . '/assets/icons/180x180.png' ) );
		\printf( '<meta name="theme-color" content="#eeeeee" media="(prefers-color-scheme: light)">' . \PHP_EOL );
		\printf( '<meta name="theme-color" content="#222222" media="(prefers-color-scheme: dark)">' . \PHP_EOL );
	}
}
