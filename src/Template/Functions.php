<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Template;

/**
 * Template utility functions: archive titles, descriptions, page banners.
 *
 * @package Sovereignty
 */
class Functions {

	/**
	 * Retrieve the archive title.
	 *
	 * @return string
	 */
	public static function get_the_archive_title(): string {
		if ( is_archive() ) {
			return get_the_archive_title();
		}

		if ( is_search() ) {
			// translators: The title of the search results page.
			return \sprintf( __( 'Search Results for: %s', 'sovereignty' ), '<span>' . get_search_query() . '</span>' );
		}

		return '';
	}

	/**
	 * Check if page banner should be displayed.
	 *
	 * @return bool
	 */
	public static function show_page_banner(): bool {
		if ( is_home() && ! display_header_text() ) {
			return false;
		}

		if ( is_home() || is_archive() || is_search() ) {
			return true;
		}

		return false;
	}

	/**
	 * Get the archive type identifier.
	 *
	 * @return string
	 */
	public static function get_archive_type(): string {
		$type = '';

		if ( is_author() ) {
			$type = 'author';
		}

		/**
		 * Filters the archive type identifier.
		 *
		 * @param string $type The archive type.
		 *
		 * @return string The filtered archive type.
		 */
		return (string) apply_filters( 'sovereignty_archive_type', $type );
	}

	/**
	 * Get author archive meta data (followers, posts, subscribe link).
	 *
	 * @return string
	 */
	public static function get_archive_author_meta(): string {
		$meta = [];

		$meta[] = \sprintf(
			// translators: list of followers.
			__( '%s Followers', 'sovereignty' ),
			/**
			 * Filters the follower count for an author archive.
			 *
			 * @param int    $count     The follower count.
			 * @param string $author_id The author user ID.
			 *
			 * @return int The filtered follower count.
			 */
			apply_filters( 'sovereignty_archive_author_followers', 0, get_the_author_meta( 'ID' ) ),
		);
		$meta[] = \sprintf(
			// translators: a post counter.
			__( '%s Posts', 'sovereignty' ),
			count_user_posts( (int) get_the_author_meta( 'ID' ) ),
		);
		$meta[] = \sprintf(
			'<indie-action do="follow" with="%1$s"><a rel="alternate" class="feed u-feed openwebicons-feed" href="%1$s">%2$s</a></indie-action>',
			get_author_feed_link( (int) get_the_author_meta( 'ID' ) ),
			__( 'Subscribe', 'sovereignty' ),
		);

		/**
		 * Filters the author archive meta items.
		 *
		 * @param string[] $meta      The meta items.
		 * @param string   $author_id The author user ID.
		 *
		 * @return string[] The filtered meta items.
		 */
		$meta = apply_filters( 'sovereignty_archive_author_meta', $meta, get_the_author_meta( 'ID' ) );

		return \implode( ' | ', $meta );
	}

	/**
	 * Get the archive/search page description.
	 *
	 * @return string
	 */
	public static function get_the_archive_description(): string {
		if ( is_home() ) {
			return get_bloginfo( 'description' );
		}

		if ( is_author() ) {
			return get_the_author_meta( 'description' );
		}

		if ( is_archive() ) {
			return get_the_archive_description();
		}

		if ( is_search() ) {
			global $wp_query;
			$total = $wp_query->found_posts;
			// translators: Description for search results.
			$stats_text = \sprintf( _n( 'Found %1$s search result for <strong>%2$s</strong>.', 'Found %1$s search results for <strong>%2$s</strong>.', $total, 'sovereignty' ), number_format_i18n( $total ), get_search_query() );

			return wpautop( $stats_text );
		}

		return '';
	}

	/**
	 * Check if an archive title is available.
	 *
	 * @return bool
	 */
	public static function has_archive_title(): bool {
		return self::get_the_archive_title() !== '';
	}

	/**
	 * Check if an archive description is available.
	 *
	 * @return bool
	 */
	public static function has_archive_description(): bool {
		return self::get_the_archive_description() !== '';
	}

	/**
	 * Echo the archive title.
	 *
	 * @return void
	 */
	public static function render_archive_title(): void {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- May contain HTML from WP core.
		echo self::get_the_archive_title();
	}

	/**
	 * Echo the archive description.
	 *
	 * @return void
	 */
	public static function render_archive_description(): void {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Contains HTML from WP core.
		echo self::get_the_archive_description();
	}

	/**
	 * Echo the author archive meta.
	 *
	 * @return void
	 */
	public static function render_archive_author_meta(): void {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Contains safe HTML.
		echo self::get_archive_author_meta();
	}
}
