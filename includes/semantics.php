<?php
/**
 * Autonomie Websemantics polyfill
 *
 * Some functions to add backwards compatibility to older WordPress versions
 * Adds some awesome websemantics like microformats(2) and microdata
 *
 * @link https://microformats.org/wiki/microformats
 * @link https://microformats.org/wiki/microformats2
 * @link https://schema.org
 * @link https://indieweb.org
 *
 * @package Autonomie
 * @subpackage semantics
 * @since Autonomie 1.5.0
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @since Autonomie 1.0.0
 *
 * @param string[] $classes Array of body classes.
 *
 * @return string[]
 */
function autonomie_body_classes( array $classes ): array {
	$classes[] = get_theme_mod( 'autonomie_columns', 'multi' ) . '-column';

	if ( ! is_singular() && ! is_404() ) {
		$classes[] = 'hfeed';
		$classes[] = 'h-feed';
		$classes[] = 'feed';
	}

	// Adds a class of single-author to blogs with only 1 published author
	if ( ! is_multi_author() ) {
		$classes[] = 'single-author';
	}

	if ( get_header_image() ) {
		$classes[] = 'custom-header';
	}

	return $classes;
}
add_filter( 'body_class', 'autonomie_body_classes' );

/**
 * Adds custom classes to the array of post classes.
 *
 * @since Autonomie 1.0.0
 *
 * @param string[] $classes Array of post classes.
 *
 * @return string[]
 */
function autonomie_post_classes( array $classes ): array {
	$classes = array_diff( $classes, [ 'hentry' ] );

	if ( ! is_singular() ) {
		return autonomie_get_post_classes( $classes );
	}

	return $classes;
}
add_filter( 'post_class', 'autonomie_post_classes', 99 );

/**
 * Adds custom classes to the array of comment classes.
 *
 * @since Autonomie 1.4.0
 *
 * @param string[] $classes Array of comment classes.
 *
 * @return string[]
 */
function autonomie_comment_classes( array $classes ): array {
	$classes[] = 'h-entry';
	$classes[] = 'h-cite';
	$classes[] = 'p-comment';
	$classes[] = 'comment';

	return array_unique( $classes );
}
add_filter( 'comment_class', 'autonomie_comment_classes', 99 );

/**
 * Encapsulates post-classes to use them on different tags.
 *
 * @param string[] $classes Array of post classes.
 *
 * @return string[]
 */
function autonomie_get_post_classes( array $classes = [] ): array {
	// Adds a class for microformats v2
	$classes[] = 'h-entry';

	// add hentry to the same tag as h-entry
	$classes[] = 'hentry';

	return array_unique( $classes );
}

/**
 * Adds microformats v2 support to the comment_author_link.
 *
 * @since Autonomie 1.0.0
 *
 * @param string $link The comment author link HTML.
 *
 * @return string|null
 */
function autonomie_author_link( string $link ): ?string {
	// Adds a class for microformats v2
	return preg_replace( '/(class\s*=\s*[\"|\'])/i', '${1}u-url ', $link );
}
add_filter( 'get_comment_author_link', 'autonomie_author_link' );

/**
 * Adds microformats v2 support to the get_avatar() method.
 *
 * @since Autonomie 1.0.0
 *
 * @param array $args        Arguments passed to get_avatar_data().
 * @param mixed $id_or_email The Gravatar to retrieve for (user ID, email, WP_User, WP_Post, or WP_Comment).
 *
 * @return array
 */
function autonomie_pre_get_avatar_data( array $args, $id_or_email ): array { // phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	if ( ! isset( $args['class'] ) ) {
		$args['class'] = [];
	}

	if ( ! is_array( $args['class'] ) ) {
		$args['class'] = [ $args['class'] ];
	}

	// Adds a class for microformats v2
	$args['class'] = array_unique( array_merge( $args['class'], [ 'u-photo' ] ) );
	$args['extra_attr'] .= ' itemprop="image" loading="lazy"';

	// Adds default alt attribute
	if ( empty( $args['alt'] ) ) {
		$username = get_the_author_meta( 'display_name', $id_or_email );

		if ( $username ) {
			// translators: %s: username
			$args['alt'] = sprintf( __( 'User Avatar of %s' ), $username );
		} else {
			$args['alt'] = __( 'User Avatar' );
		}
	}

	return $args;
}
add_filter( 'pre_get_avatar_data', 'autonomie_pre_get_avatar_data', 99, 2 );

/**
 * Add rel-prev attribute to previous_image_link.
 *
 * @param string $link The a-tag to filter.
 *
 * @return string|null
 */
function autonomie_semantic_previous_image_link( string $link ): ?string {
	return preg_replace( '/<a/i', '<a rel="prev"', $link );
}
add_filter( 'previous_image_link', 'autonomie_semantic_previous_image_link' );

/**
 * Add rel-next attribute to next_image_link.
 *
 * @param string $link The a-tag to filter.
 *
 * @return string|null
 */
function autonomie_semantic_next_image_link( string $link ): ?string {
	return preg_replace( '/<a/i', '<a rel="next"', $link );
}
add_filter( 'next_image_link', 'autonomie_semantic_next_image_link' );

/**
 * Add rel-prev attribute to next_posts_link_attributes.
 *
 * @param string $attr Attributes.
 *
 * @return string
 */
function autonomie_next_posts_link_attributes( string $attr ): string {
	return $attr . ' rel="prev"';
}
add_filter( 'next_posts_link_attributes', 'autonomie_next_posts_link_attributes' );

/**
 * Add rel-next attribute to previous_posts_link.
 *
 * @param string $attr Attributes.
 *
 * @return string
 */
function autonomie_previous_posts_link_attributes( string $attr ): string {
	return $attr . ' rel="next"';
}
add_filter( 'previous_posts_link_attributes', 'autonomie_previous_posts_link_attributes' );

/**
 * Wraps the search form with semantic markup.
 *
 * @param string $form The search form HTML.
 *
 * @return string|null
 */
function autonomie_get_search_form( string $form ): ?string {
	$form = preg_replace( '/<form/i', '<search><form itemprop="potentialAction" itemscope itemtype="https://schema.org/SearchAction"', $form );
	$form = preg_replace( '/<\/form>/i', '<meta itemprop="target" content="' . home_url( '/?s={s} ' ) . '"/></form></search>', $form );
	$form = preg_replace( '/<input type="search"/i', '<input type="search" enterkeyhint="search" itemprop="query-input"', $form );

	return $form;
}
add_filter( 'get_search_form', 'autonomie_get_search_form' );

/**
 * Add semantics.
 *
 * @param ?string $id The class identifier.
 *
 * @return array
 */
function autonomie_get_semantics( ?string $id = null ): array {
	$classes = [];

	// add default values
	switch ( $id ) {
		case 'body':
			if ( is_search() ) {
				$classes['itemscope'] = [ '' ];
				$classes['itemtype'] = [ 'https://schema.org/Blog', 'https://schema.org/SearchResultsPage' ];
			} elseif ( is_author() ) {
				$classes['itemscope'] = [ '' ];
				$classes['itemtype'] = [ 'https://schema.org/Blog', 'https://schema.org/ProfilePage' ];
			} elseif ( is_single() ) {
				$classes['itemscope'] = [ '' ];
				$classes['itemtype'] = [ 'https://schema.org/BlogPosting' ];
				$classes['itemref'] = [ 'site-publisher' ];
			} elseif ( is_page() ) {
				$classes['itemscope'] = [ '' ];
				$classes['itemtype'] = [ 'https://schema.org/WebPage' ];
			} elseif ( ! is_singular() ) {
				$classes['itemscope'] = [ '' ];
				$classes['itemtype'] = [ 'https://schema.org/Blog', 'https://schema.org/WebPage' ];
			}

			$classes['itemid'] = [ get_self_link() ];

			break;
		case 'main':
			break;
		case 'site-title':
			if ( is_home() ) {
				$classes['itemprop'] = [ 'name' ];
				$classes['class'] = [ 'p-name' ];
			}
			break;
		case 'page-title':
			if ( ! is_singular() && ! is_home() ) {
				$classes['itemprop'] = [ 'name' ];
				$classes['class'] = [ 'p-name' ];
			}
			break;
		case 'page-description':
			if ( ! is_singular() ) {
				$classes['itemprop'] = [ 'description' ];
				$classes['class'] = [ 'p-summary', 'e-content' ];
			}
			break;
		case 'site-url':
			if ( ! is_singular() ) {
				$classes['itemprop'] = [ 'url' ];
				$classes['class'] = [ 'u-url', 'url' ];
			}
			break;
		case 'post':
			if ( ! is_singular() ) {
				$classes['itemprop'] = [ 'blogPost' ];
				$classes['itemscope'] = [ '' ];
				$classes['itemtype'] = [ 'https://schema.org/BlogPosting' ];
				$classes['itemref'] = [ 'site-publisher' ];
				$classes['itemid'] = [ get_permalink() ];
			}
			break;
	}

	$classes = apply_filters( 'autonomie_semantics', $classes, $id );
	$classes = apply_filters( "autonomie_semantics_{$id}", $classes, $id );

	return $classes;
}

/**
 * Returns the semantic attributes as a string.
 *
 * @param string $id The class identifier.
 *
 * @return string
 */
function autonomie_get_the_semantics( string $id ): string {
	$classes = autonomie_get_semantics( $id );

	if ( ! $classes ) {
		return '';
	}

	$class = '';

	foreach ( $classes as $key => $value ) {
		$class .= ' ' . esc_attr( $key ) . '="' . esc_attr( implode( ' ', $value ) ) . '"';
	}

	return $class;
}

/**
 * Echos the semantic classes added via the "autonomie_semantics" filters.
 *
 * @param string $id The class identifier.
 *
 * @return void
 */
function autonomie_semantics( string $id ): void {
	$classes = autonomie_get_semantics( $id );

	if ( ! $classes ) {
		return;
	}

	foreach ( $classes as $key => $value ) {
		echo ' ' . esc_attr( $key ) . '="' . esc_attr( implode( ' ', $value ) ) . '"';
	}
}

/**
 * Add `p-category` to tags links.
 *
 * @link https://www.webrocker.de/2016/05/13/add-class-attribute-to-wordpress-the_tags-markup/
 *
 * @param array $links Array of term links.
 *
 * @return array
 */
function autonomie_term_links_tag( array $links ): array {
	$post = get_post();

	$terms = get_the_terms( $post->ID, 'post_tag' );

	if ( is_wp_error( $terms ) ) {
		return [];
	}

	if ( empty( $terms ) ) {
		return [];
	}

	$links = [];

	foreach ( $terms as $term ) {
		$link = get_term_link( $term );
		if ( is_wp_error( $link ) ) {
			return [];
		}
		$links[] = '<a class="p-category" href="' . esc_url( $link ) . '" rel="tag">' . $term->name . '</a>';
	}
	return $links;
}
add_filter( 'term_links-post_tag', 'autonomie_term_links_tag' );
