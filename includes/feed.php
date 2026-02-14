<?php
/**
 * Autonomie Feeds class
 *
 * @package Autonomie
 * @subpackage Feeds
 * @since Autonomie 1.0.0
 */

/**
 * Adds support for "standard" Post-Format.
 *
 * @param string $post_format The post format slug.
 * @param string $feed        The feed type.
 *
 * @return string|false
 */
function autonomie_get_post_format_archive_feed_link( string $post_format, string $feed = '' ): string|false { // phpcs:ignore Generic.NamingConventions.CamelCapsFunctionName.NotCamelCaps, Squiz.NamingConventions.ValidVariableName.NotCamelCaps
	// phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps -- WordPress core naming convention.
	$default_feed = get_default_feed();
	if ( empty( $feed ) ) {
		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps -- WordPress core naming convention.
		$feed = $default_feed;
	}

	// phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps -- WordPress core naming convention.
	$link = autonomie_get_post_format_link( $post_format );
	if ( ! $link ) {
		return false;
	}

	if ( get_option( 'permalink_structure' ) ) {
		$link  = trailingslashit( $link );
		$link .= 'feed/';
		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps -- WordPress core naming convention.
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
	 */
	return apply_filters( 'post_format_archive_feed_link', $link, $feed );
}

/**
 * Adds some more feeds discovery headers
 *
 * @see https://notiz.blog/2019/02/21/untitled/
 * @see https://notiz.blog/2019/09/18/eine-posse/
 * @see https://github.com/dshanske/extra-links
 *
 * @param array $args Optional arguments.
 *
 * @return void
 */
function autonomie_extend_singular_feed_discovery( array $args = [] ): void { // phpcs:ignore Generic.NamingConventions.CamelCapsFunctionName.NotCamelCaps
	$defaults = [
		/* translators: Separator between blog name and feed type in feed links */
		'separator'   => _x( '&raquo;', 'feed link', 'autonomie' ),
		/* translators: 1: blog name, 2: separator(raquo), 3: post title */
		'singletitle' => __( '%1$s %2$s %3$s Comments Feed', 'autonomie' ),
		/* translators: 1: blog name, 2: separator(raquo), 3: category name */
		'cattitle'    => __( '%1$s %2$s %3$s Category Feed', 'autonomie' ),
		/* translators: 1: blog name, 2: separator(raquo), 3: tag name */
		'tagtitle'    => __( '%1$s %2$s %3$s Tag Feed', 'autonomie' ),
		/* translators: 1: blog name, 2: separator(raquo), 3: term name, 4: taxonomy singular name */
		'taxtitle'    => __( '%1$s %2$s %3$s %4$s Feed', 'autonomie' ),
		/* translators: 1: blog name, 2: separator(raquo), 3: author name  */
		'authortitle' => __( '%1$s %2$s Posts by %3$s Feed', 'autonomie' ),
		/* translators: 1: blog name, 2: separator(raquo), 3: search phrase */
		'searchtitle' => __( '%1$s %2$s Search Results for &#8220;%3$s&#8221; Feed', 'autonomie' ),
		/* translators: 1: blog name, 2: separator(raquo), 3: post type */
		'posttypetitle' => __( '%1$s %2$s %3$s Post-Type Feed', 'autonomie' ),
	];

	$args  = wp_parse_args( $args, $defaults );
	$feeds = [];

	// Post/Page feeds
	if ( is_singular() ) {
		// add tag feeds
		foreach ( wp_get_post_terms( get_the_ID(), [ 'post_tag', 'category' ] ) as $term ) {
			// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps -- WordPress core property.
			$tax = get_taxonomy( $term->taxonomy );

			$feeds[] = [
				'title' => sprintf( $args['taxtitle'], get_bloginfo( 'name' ), $args['separator'], $term->name, $tax->labels->singular_name ),
				// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps -- WordPress core property.
				'href'  => get_term_feed_link( $term->term_id, $term->taxonomy ),
			];
		}

		$post = get_post();

		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps, Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps -- WordPress core property.
		$author_id = $post->post_author;
		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps -- WordPress core naming convention.
		$feeds[]   = [
			// phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps -- WordPress core naming convention.
			'title' => sprintf( $args['authortitle'], get_bloginfo( 'name' ), $args['separator'], get_the_author_meta( 'display_name', (int) $author_id ) ),
			// phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps -- WordPress core naming convention.
			'href'  => get_author_feed_link( (int) $author_id ),
		];

		$feeds[] = [
			'title' => sprintf( $args['posttypetitle'], get_bloginfo( 'name' ), $args['separator'], get_post_format_string( autonomie_get_post_format() ) ),
			'href'  => autonomie_get_post_format_archive_feed_link( autonomie_get_post_format() ),
		];
	}

	// Homepage feeds
	if ( is_home() ) {
		// does theme support post formats
		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps -- WordPress core naming convention.
		$post_formats = get_theme_support( 'post-formats' );

		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps -- WordPress core naming convention.
		if ( $post_formats ) {
			// phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps -- WordPress core naming convention.
			$post_formats = current( $post_formats );
		} else {
			// phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps -- WordPress core naming convention.
			$post_formats = [];
		}

		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps -- WordPress core naming convention.
		$post_formats[] = 'standard';

		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps -- WordPress core naming convention.
		foreach ( $post_formats as $post_format ) {
			$feeds[] = [
				// phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps -- WordPress core naming convention.
				'title' => sprintf( $args['posttypetitle'], get_bloginfo( 'name' ), $args['separator'], get_post_format_string( $post_format ) ),
				// phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps -- WordPress core naming convention.
				'href'  => autonomie_get_post_format_archive_feed_link( $post_format ),
			];
		}
	}

	// Add "standard" post-format feed discovery
	// phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps -- WordPress core global.
	global $wp_query;
	if (
		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps -- WordPress core global.
		isset( $wp_query->query['post_format'] ) &&
		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps -- WordPress core global.
		$wp_query->query['post_format'] === 'post-format-standard' &&
		is_archive()
	) {
		$feeds[] = [
			'title' => sprintf( $args['posttypetitle'], get_bloginfo( 'name' ), $args['separator'], get_post_format_string( 'standard' ) ),
			'href'  => autonomie_get_post_format_archive_feed_link( 'standard' ),
		];
	}

	foreach ( $feeds as $feed ) {
		if ( array_key_exists( 'href', $feed ) && array_key_exists( 'title', $feed ) ) { // @phpstan-ignore function.alreadyNarrowedType, function.alreadyNarrowedType, booleanAnd.alwaysTrue
			printf(
				'<link rel="alternate" type="%s" title="%s" href="%s" />',
				esc_attr( feed_content_type() ),
				esc_attr( $feed['title'] ),
				esc_url( $feed['href'] )
			);
			echo PHP_EOL;
		}
	}
}
add_action( 'wp_head', 'autonomie_extend_singular_feed_discovery' );
