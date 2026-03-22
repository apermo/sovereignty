<?php

declare(strict_types=1);

namespace Apermo\Sovereignty;

use Apermo\Sovereignty\Template\Post_Format;
use WP_Post;

/**
 * RSS/Atom feed extensions.
 *
 * Adds post-format archive feed links and additional
 * feed discovery headers.
 *
 * @package Sovereignty
 */
class Feed {

	/**
	 * Build the feed link for a post-format archive.
	 *
	 * @param string $post_format The post format slug.
	 * @param string $feed        The feed type.
	 *
	 * @return string|false
	 */
	public static function get_post_format_archive_feed_link( string $post_format, string $feed = '' ): string|false {
		$default_feed = get_default_feed();
		if ( empty( $feed ) ) {
			$feed = $default_feed;
		}

		// phpcs:ignore Apermo.WordPress.ImplicitPostFunction -- Utility called from hook context.
		$current_post = get_post();
		$link         = $current_post instanceof WP_Post
			? Post_Format::get_format_link( $post_format, $current_post )
			: '';
		if ( empty( $link ) ) {
			return false;
		}

		if ( (bool) get_option( 'permalink_structure' ) ) {
			$link = trailingslashit( $link );
			$link .= 'feed/';
			if ( $feed !== $default_feed ) {
				$link .= "$feed/";
			}
		} else {
			$link = add_query_arg( 'feed', $feed, $link );
		}

		/**
		 * Filters the post type archive feed link.
		 *
		 * @param string $link The post type archive feed link.
		 * @param string $feed Feed type. Possible values include 'rss2', 'atom'.
		 *
		 * @return string The filtered feed link.
		 */
		return apply_filters( 'sovereignty_post_format_archive_feed_link', $link, $feed );
	}

	/**
	 * Output additional feed discovery headers in wp_head.
	 *
	 * @see https://notiz.blog/2019/02/21/untitled/
	 * @see https://notiz.blog/2019/09/18/eine-posse/
	 * @see https://github.com/dshanske/extra-links
	 *
	 * @return void
	 */
	public static function extend_singular_feed_discovery(): void { // phpcs:ignore SlevomatCodingStandard.Functions.FunctionLength.FunctionLength -- @todo Refactor feed discovery logic.
		$args = [
			/* translators: Separator between blog name and feed type in feed links */
			'separator'     => _x( '&raquo;', 'feed link', 'sovereignty' ),
			/* translators: 1: blog name, 2: separator(raquo), 3: post title */
			'singletitle'   => __( '%1$s %2$s %3$s Comments Feed', 'sovereignty' ),
			/* translators: 1: blog name, 2: separator(raquo), 3: category name */
			'cattitle'      => __( '%1$s %2$s %3$s Category Feed', 'sovereignty' ),
			/* translators: 1: blog name, 2: separator(raquo), 3: tag name */
			'tagtitle'      => __( '%1$s %2$s %3$s Tag Feed', 'sovereignty' ),
			/* translators: 1: blog name, 2: separator(raquo), 3: term name, 4: taxonomy singular name */
			'taxtitle'      => __( '%1$s %2$s %3$s %4$s Feed', 'sovereignty' ),
			/* translators: 1: blog name, 2: separator(raquo), 3: author name  */
			'authortitle'   => __( '%1$s %2$s Posts by %3$s Feed', 'sovereignty' ),
			/* translators: 1: blog name, 2: separator(raquo), 3: search phrase */
			'searchtitle'   => __( '%1$s %2$s Search Results for &#8220;%3$s&#8221; Feed', 'sovereignty' ),
			/* translators: 1: blog name, 2: separator(raquo), 3: post type */
			'posttypetitle' => __( '%1$s %2$s %3$s Post-Type Feed', 'sovereignty' ),
		];
		$feeds = [];

		// Post/Page feeds.
		if ( is_singular() ) {
			$post = get_post(); // phpcs:ignore Apermo.WordPress.ImplicitPostFunction -- Hook callback, no $post parameter available.

			foreach ( wp_get_post_terms( $post->ID, [ 'post_tag', 'category' ] ) as $term ) {
				$tax = get_taxonomy( $term->taxonomy );

				$feeds[] = [
					'title' => \sprintf( $args['taxtitle'], get_bloginfo( 'name' ), $args['separator'], $term->name, $tax->labels->singular_name ),
					'href'  => get_term_feed_link( $term->term_id, $term->taxonomy ),
				];
			}

			$author_id = $post->post_author;
			$feeds[]   = [
				'title' => \sprintf( $args['authortitle'], get_bloginfo( 'name' ), $args['separator'], get_the_author_meta( 'display_name', (int) $author_id ) ),
				'href'  => get_author_feed_link( (int) $author_id ),
			];

			$feeds[] = [
				'title' => \sprintf( $args['posttypetitle'], get_bloginfo( 'name' ), $args['separator'], get_post_format_string( Post_Format::get_format( $post ) ) ),
				'href'  => self::get_post_format_archive_feed_link( Post_Format::get_format( $post ) ),
			];
		}

		// Homepage feeds.
		if ( is_home() ) {
			$post_formats = get_theme_support( 'post-formats' );

			if ( \is_array( $post_formats ) ) {
				$post_formats = \current( $post_formats );
			} else {
				$post_formats = [];
			}

			$post_formats[] = 'standard';

			foreach ( $post_formats as $post_format ) {
				$feeds[] = [
					'title' => \sprintf( $args['posttypetitle'], get_bloginfo( 'name' ), $args['separator'], get_post_format_string( $post_format ) ),
					'href'  => self::get_post_format_archive_feed_link( $post_format ),
				];
			}
		}

		// Add "standard" post-format feed discovery.
		global $wp_query;
		if (
			isset( $wp_query->query['post_format'] ) &&
			$wp_query->query['post_format'] === 'post-format-standard' &&
			is_archive()
		) {
			$feeds[] = [
				'title' => \sprintf( $args['posttypetitle'], get_bloginfo( 'name' ), $args['separator'], get_post_format_string( 'standard' ) ),
				'href'  => self::get_post_format_archive_feed_link( 'standard' ),
			];
		}

		foreach ( $feeds as $feed ) {
			if ( \array_key_exists( 'href', $feed ) && \array_key_exists( 'title', $feed ) ) { // @phpstan-ignore function.alreadyNarrowedType, function.alreadyNarrowedType, booleanAnd.alwaysTrue
				\printf(
					'<link rel="alternate" type="%s" title="%s" href="%s" />',
					esc_attr( feed_content_type() ),
					esc_attr( $feed['title'] ),
					esc_url( $feed['href'] ),
				);
				echo \PHP_EOL;
			}
		}
	}
}
