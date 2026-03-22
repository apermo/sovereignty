<?php

declare(strict_types=1);

namespace Apermo\Sovereignty;

/**
 * Wp_head output: pingback, publisher feed link, color-scheme meta.
 *
 * @package Sovereignty
 */
class Head {

	/**
	 * Add a pingback URL auto-discovery header.
	 *
	 * @return void
	 */
	public static function pingback(): void {
		if ( is_singular() && pings_open() ) {
			\printf( '<link rel="pingback" href="%1$s" />', esc_url( get_bloginfo( 'pingback_url' ) ) );
		}
	}

	/**
	 * Add a rel-feed link if the front page is not a list of posts.
	 *
	 * @return void
	 */
	public static function publisher_feed(): void {
		if ( is_front_page() && (int) get_option( 'page_for_posts', 0 ) !== 0 ) {
			\printf(
				\PHP_EOL . '<link rel="feed" type="text/html" href="%1$s" title="%2$s" />' . \PHP_EOL,
				esc_url( get_post_type_archive_link( 'post' ) ),
				esc_attr__( 'POSH Feed', 'sovereignty' ),
			);
		}
	}

	/**
	 * Add color-scheme meta tag.
	 *
	 * @return void
	 */
	public static function color_scheme_meta(): void {
		\printf( \PHP_EOL . '<meta name="supported-color-schemes" content="light dark">' . \PHP_EOL );
	}
}
