<?php

declare(strict_types=1);

namespace Apermo\Sovereignty;

/**
 * Web semantics: microformats2, microdata (Schema.org), and IndieWeb markup.
 *
 * @link https://microformats.org/wiki/microformats2
 * @link https://schema.org
 * @link https://indieweb.org
 *
 * @package Sovereignty
 */
class Semantics {

	/**
	 * Add custom classes to the body element.
	 *
	 * @param string[] $classes Array of body classes.
	 *
	 * @return string[]
	 */
	public static function body_classes( array $classes ): array {
		$classes[] = get_theme_mod( 'sovereignty_columns', 'multi' ) . '-column';

		if ( ! is_singular() && ! is_404() ) {
			$classes[] = 'hfeed';
			$classes[] = 'h-feed';
			$classes[] = 'feed';
		}

		if ( ! is_multi_author() ) {
			$classes[] = 'single-author';
		}

		if ( get_header_image() ) {
			$classes[] = 'custom-header';
		}

		return $classes;
	}

	/**
	 * Add microformat classes to post elements.
	 *
	 * @param string[] $classes Array of post classes.
	 *
	 * @return string[]
	 */
	public static function post_classes( array $classes ): array {
		$classes = \array_diff( $classes, [ 'hentry' ] );

		if ( ! is_singular() ) {
			return self::get_post_classes( $classes );
		}

		return $classes;
	}

	/**
	 * Add microformat classes to comment elements.
	 *
	 * @param string[] $classes Array of comment classes.
	 *
	 * @return string[]
	 */
	public static function comment_classes( array $classes ): array {
		$classes[] = 'h-entry';
		$classes[] = 'h-cite';
		$classes[] = 'p-comment';
		$classes[] = 'comment';

		return \array_unique( $classes );
	}

	/**
	 * Add h-entry and hentry classes to post elements.
	 *
	 * @param string[] $classes Array of post classes.
	 *
	 * @return string[]
	 */
	public static function get_post_classes( array $classes = [] ): array {
		$classes[] = 'h-entry';
		$classes[] = 'hentry';

		return \array_unique( $classes );
	}

	/**
	 * Add u-url class to comment author link.
	 *
	 * @param string $link The comment author link HTML.
	 *
	 * @return string|null
	 */
	public static function author_link( string $link ): ?string {
		return \preg_replace( '/(class\s*=\s*[\"|\'])/i', '${1}u-url ', $link );
	}

	/**
	 * Add microformat classes and alt text to avatar data.
	 *
	 * @param array $args        Arguments passed to get_avatar_data().
	 * @param mixed $id_or_email The Gravatar to retrieve for.
	 *
	 * @return array
	 */
	public static function pre_get_avatar_data( array $args, $id_or_email ): array { // phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
		if ( ! isset( $args['class'] ) ) {
			$args['class'] = [];
		}

		if ( ! \is_array( $args['class'] ) ) {
			$args['class'] = [ $args['class'] ];
		}

		$args['class'] = \array_unique( \array_merge( $args['class'], [ 'u-photo' ] ) );
		$args['extra_attr'] .= ' itemprop="image" loading="lazy"';

		if ( empty( $args['alt'] ) ) {
			$username = get_the_author_meta( 'display_name', $id_or_email );

			if ( $username !== '' ) {
				// translators: %s: username.
				$args['alt'] = \sprintf( __( 'User Avatar of %s' ), $username ); // phpcs:ignore WordPress.WP.I18n.MissingArgDomain -- Intentionally uses default domain.
			} else {
				$args['alt'] = __( 'User Avatar' ); // phpcs:ignore WordPress.WP.I18n.MissingArgDomain -- Intentionally uses default domain.
			}
		}

		return $args;
	}

	/**
	 * Add rel-prev to previous image link.
	 *
	 * @param string $link The a-tag HTML.
	 *
	 * @return string|null
	 */
	public static function semantic_previous_image_link( string $link ): ?string {
		return \preg_replace( '/<a/i', '<a rel="prev"', $link );
	}

	/**
	 * Add rel-next to next image link.
	 *
	 * @param string $link The a-tag HTML.
	 *
	 * @return string|null
	 */
	public static function semantic_next_image_link( string $link ): ?string {
		return \preg_replace( '/<a/i', '<a rel="next"', $link );
	}

	/**
	 * Add rel-prev to next posts link.
	 *
	 * @param string $attr Attributes.
	 *
	 * @return string
	 */
	public static function next_posts_link_attributes( string $attr ): string {
		return $attr . ' rel="prev"';
	}

	/**
	 * Add rel-next to previous posts link.
	 *
	 * @param string $attr Attributes.
	 *
	 * @return string
	 */
	public static function previous_posts_link_attributes( string $attr ): string {
		return $attr . ' rel="next"';
	}

	/**
	 * Wrap search form with semantic markup.
	 *
	 * @param string $form The search form HTML.
	 *
	 * @return string|null
	 */
	public static function get_search_form( string $form ): ?string {
		$form = \preg_replace( '/<form/i', '<search><form itemprop="potentialAction" itemscope itemtype="https://schema.org/SearchAction"', $form );
		$form = \preg_replace( '/<\/form>/i', '<meta itemprop="target" content="' . home_url( '/?s={s} ' ) . '"/></form></search>', $form );
		$form = \preg_replace( '/<input type="search"/i', '<input type="search" enterkeyhint="search" itemprop="query-input"', $form );

		return $form;
	}

	/**
	 * Build semantic attributes array for an element.
	 *
	 * @param ?string $id The element identifier.
	 *
	 * @return array
	 */
	public static function get_semantics( ?string $id = null ): array { // phpcs:ignore SlevomatCodingStandard.Functions.FunctionLength.FunctionLength -- @todo Break into per-element helpers.
		$classes = [];

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
					// phpcs:ignore Apermo.WordPress.ImplicitPostFunction
					$classes['itemid'] = [ get_permalink() ];
				}
				break;
		}

		/**
		 * Filters the semantic attributes for a given element.
		 *
		 * @param array  $classes The semantic attributes.
		 * @param string $id      The element identifier.
		 *
		 * @return array The filtered semantic attributes.
		 */
		$classes = apply_filters( 'sovereignty_semantics', $classes, $id );

		/**
		 * Filters the semantic attributes for a specific element by ID.
		 *
		 * @param array  $classes The semantic attributes.
		 * @param string $id      The element identifier.
		 *
		 * @return array The filtered semantic attributes.
		 */
		$classes = apply_filters( "sovereignty_semantics_{$id}", $classes, $id );

		return $classes;
	}

	/**
	 * Return semantic attributes as a string.
	 *
	 * @param string $id The element identifier.
	 *
	 * @return string
	 */
	public static function get_the_semantics( string $id ): string {
		$classes = self::get_semantics( $id );

		if ( $classes === [] ) {
			return '';
		}

		$class = '';

		foreach ( $classes as $key => $value ) {
			$class .= ' ' . esc_attr( $key ) . '="' . esc_attr( \implode( ' ', $value ) ) . '"';
		}

		return $class;
	}

	/**
	 * Echo semantic attributes for an element.
	 *
	 * @param string $id The element identifier.
	 *
	 * @return void
	 */
	public static function output( string $id ): void {
		$classes = self::get_semantics( $id );

		if ( $classes === [] ) {
			return;
		}

		foreach ( $classes as $key => $value ) {
			echo ' ' . esc_attr( $key ) . '="' . esc_attr( \implode( ' ', $value ) ) . '"';
		}
	}

	/**
	 * Add p-category class to tag links.
	 *
	 * @param array $links Array of term links.
	 *
	 * @return array
	 */
	public static function term_links_tag( array $links ): array {
		// phpcs:ignore Apermo.WordPress.ImplicitPostFunction
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
}
