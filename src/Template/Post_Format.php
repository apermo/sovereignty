<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Template;

use WP_Post;

/**
 * Post format helpers: format detection, display strings, archive links.
 *
 * @package Sovereignty
 */
class Post_Format {

	/**
	 * Get the post format, defaulting to 'standard'.
	 *
	 * @param WP_Post $post The post object.
	 *
	 * @return string
	 */
	public static function get_format( WP_Post $post ): string {
		return get_post_format( $post ) ?: 'standard';
	}

	/**
	 * Get a human-readable post format string.
	 *
	 * @param WP_Post $post The post object.
	 *
	 * @return string
	 */
	public static function get_format_string( WP_Post $post ): string {
		if ( get_post_type( $post ) === 'attachment' ) {
			return __( 'Attachment', 'sovereignty' );
		}

		if ( get_post_type( $post ) === 'page' ) {
			return __( 'Page', 'sovereignty' );
		}

		if ( get_post_format( $post ) ) {
			return get_post_format( $post );
		}

		return __( 'Text', 'sovereignty' );
	}

	/**
	 * Get the archive link for a post format.
	 *
	 * @param string  $post_format The post format slug.
	 * @param WP_Post $post        The post object.
	 *
	 * @return string
	 */
	public static function get_format_link( string $post_format, WP_Post $post ): string {
		if ( \in_array( get_post_type( $post ), [ 'page', 'attachment' ], true ) ) {
			return get_permalink( $post );
		}

		if ( $post_format !== 'standard' ) {
			return get_post_format_link( $post_format );
		}

		global $wp_rewrite;

		$term_link = $wp_rewrite->get_extra_permastruct( 'post_format' );

		if ( empty( $term_link ) ) {
			$term_link = '?post_format=standard';
			$term_link = home_url( $term_link );
		} else {
			$term_link = \str_replace( '%post_format%', 'standard', $term_link );
			$term_link = home_url( user_trailingslashit( $term_link, 'category' ) );
		}

		return $term_link;
	}
}
