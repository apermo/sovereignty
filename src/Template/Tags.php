<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Template;

use Apermo\Sovereignty\Config;
use Apermo\Sovereignty\Semantics;
use WP_Post;

/**
 * Template tags: output functions called from template files.
 *
 * @package Sovereignty
 */
class Tags {

	/**
	 * Display navigation to next/previous pages.
	 *
	 * @param string $nav_id The navigation element ID.
	 *
	 * @return void
	 */
	public static function content_nav( string $nav_id ): void {
		?>
		<?php if ( is_home() || is_archive() || is_search() ) { ?>
		<nav id="archive-nav">
			<div class="assistive-text"><?php esc_html_e( 'Post navigation', 'sovereignty' ); ?></div>
			<?php // @phpstan-ignore-next-line paginate_links() returns null on single-page results ?>
			<?php echo wp_kses_post( paginate_links() ?? '' ); ?>
		</nav><!-- #<?php echo esc_html( $nav_id ); ?> -->
		<?php } ?>
		<?php
	}

	/**
	 * Print HTML with meta information for the current author.
	 *
	 * @param WP_Post $post The post object.
	 *
	 * @return void
	 */
	public static function posted_by( WP_Post $post ): void {
		$author_id   = (int) $post->post_author;
		$author_name = get_the_author_meta( 'display_name', $author_id );
		$author_url  = get_author_posts_url( $author_id );
		$avatar_size = Config::int( 'sovereignty.avatar.size' );

		\printf(
			'<address class="byline">
				<span class="author p-author vcard hcard h-card" itemprop="author" itemscope itemtype="https://schema.org/Person">
					%1$s
					<a class="url uid u-url u-uid fn p-name" href="%2$s" title="%3$s" rel="author">
						<span itemprop="name">%4$s</span>
					</a>
					<link itemprop="url" href="%2$s" />
				</span>
			</address>',
			get_avatar( $author_id, $avatar_size ),
			esc_url( $author_url ),
			/* translators: %s is the author name. */
			esc_attr( \sprintf( __( 'View all posts by %s', 'sovereignty' ), $author_name ) ),
			esc_html( $author_name ),
		);
	}

	/**
	 * Print HTML with meta information for the current post date/time.
	 *
	 * @param WP_Post $post The post object.
	 * @param string  $type The date type: 'published' or 'updated'.
	 *
	 * @return void
	 */
	public static function posted_on( WP_Post $post, string $type = 'published' ): void {
		if ( ! \in_array( $type, [ 'published', 'updated' ], true ) ) {
			$type = 'published';
		}

		if ( (bool) get_query_var( 'is_now', false ) ) {
			$type = 'updated';
		}

		if ( $type === 'updated' ) {
			$time      = get_the_modified_time( post: $post );
			$date_c    = get_the_modified_date( format: 'c', post: $post );
			$date      = get_the_modified_date( post: $post );
			$item_prop = 'dateModified';
		} else {
			$time      = get_the_time( post: $post );
			$date_c    = get_the_date( format: 'c', post: $post );
			$date      = get_the_date( post: $post );
			$item_prop = 'datePublished';
		}

		\printf(
			// translators: %1$s = post permalink, %2$s = post date, %3$s = ISO 8601 date, %4$s = display date, %5$s = date type, %6$s = itemprop.
			'<a href="%1$s" title="%2$s" rel="bookmark" class="url u-url" itemprop="mainEntityOfPage"><time class="entry-date %5$s dt-%5$s" datetime="%3$s" itemprop="%6$s">%4$s</time></a>',
			esc_url( get_permalink( $post ) ),
			esc_attr( $time ),
			esc_attr( $date_c ),
			esc_html( $date ),
			esc_html( $type ),
			esc_html( $item_prop ),
		);
	}

	/**
	 * Display the id attribute for the post div.
	 *
	 * @param WP_Post     $post    The post object.
	 * @param string|null $post_id Optional override for the post id attribute.
	 *
	 * @return void
	 */
	public static function post_id( WP_Post $post, ?string $post_id = null ): void {
		if ( $post_id !== null ) {
			echo 'id="' . esc_attr( $post_id ) . '"';
		} else {
			echo 'id="' . esc_attr( self::get_post_id( $post ) ) . '"';
		}
	}

	/**
	 * Retrieve the HTML id attribute value for the post element.
	 *
	 * @param WP_Post $post The post object.
	 *
	 * @return string The HTML id attribute value (e.g. "post-42").
	 */
	public static function get_post_id( WP_Post $post ): string {
		$html_id = 'post-' . $post->ID;

		/**
		 * Filters the post HTML id attribute value.
		 *
		 * @param string $html_id The HTML id attribute value.
		 * @param int    $id      The numeric post ID.
		 *
		 * @return string The filtered HTML id attribute value.
		 */
		return apply_filters( 'sovereignty_post_id', $html_id, $post->ID );
	}

	/**
	 * Display the CSS class for the main element.
	 *
	 * @param string $class Additional CSS class names.
	 *
	 * @return void
	 */
	public static function main_class( string $class = '' ): void { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.classFound
		echo 'class="' . esc_attr( \implode( ' ', self::get_main_class( $class ) ) ) . '"';
	}

	/**
	 * Retrieve the CSS classes for the main element.
	 *
	 * @param string|string[] $class Additional CSS class names.
	 *
	 * @return string[] An array of CSS class names.
	 */
	public static function get_main_class( string|array $class = '' ): array { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.classFound
		$classes = [];

		if ( is_singular() ) {
			$classes = Semantics::get_post_classes( $classes );
		}

		if ( ! empty( $class ) ) {
			if ( ! \is_array( $class ) ) {
				$class = \preg_split( '#\s+#', $class );
			}
			$classes = \array_merge( $classes, $class );
		} else {
			$class = [];
		}

		$classes = \array_map( 'esc_attr', $classes );

		/**
		 * Filters the list of CSS main class names.
		 *
		 * @param string[] $classes An array of main class names.
		 * @param string[] $class   An array of additional class names.
		 *
		 * @return string[] The filtered class names.
		 */
		$classes = apply_filters( 'sovereignty_main_class', $classes, $class );

		return \array_unique( $classes );
	}

	/**
	 * Get the estimated reading time in minutes.
	 *
	 * @see wp-includes/blocks/post-time-to-read.php render_block_core_post_time_to_read()
	 *
	 * @param WP_Post $post The post object.
	 *
	 * @return int Reading time in minutes (minimum 1).
	 */
	public static function get_reading_time( WP_Post $post ): int {
		$content    = get_the_content( null, false, $post );
		$word_count = \str_word_count( wp_strip_all_tags( $content ) );

		return (int) \max( 1, (int) \round( $word_count / Config::int( 'sovereignty.reading.wordsPerMinute' ) ) );
	}

	/**
	 * Display estimated reading time with microformat markup.
	 *
	 * @param WP_Post $post The post object.
	 *
	 * @return void
	 */
	public static function reading_time( WP_Post $post ): void {
		$minutes   = self::get_reading_time( $post );
		$formatted = number_format_i18n( $minutes );

		/* translators: %s: number of minutes. */
		$duration = \sprintf( _n( '%s minute', '%s minutes', $minutes, 'sovereignty' ), $formatted );

		\printf(
			'<span class="entry-duration"><time datetime="PT%dM" class="dt-duration" itemprop="timeRequired">%s</time> %s</span>',
			absint( $minutes ),
			esc_html( $duration ),
			esc_html( __( 'to read', 'sovereignty' ) ),
		);
	}

	/**
	 * Display the_content or the_excerpt based on context.
	 *
	 * Uses get_the_content/get_the_excerpt with explicit $post instead of
	 * the_content/the_excerpt which don't accept $post.
	 *
	 * @param WP_Post $post The post object.
	 *
	 * @return void
	 */
	public static function the_content( WP_Post $post ): void {
		if ( is_search() ) {
			echo get_the_excerpt( $post ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Excerpt is filtered by WP.
			return;
		}

		$more_text = __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'sovereignty' );

		if ( is_singular() ) {
			self::echo_the_content( $more_text, $post );
			return;
		}

		$count = \str_word_count( wp_strip_all_tags( get_the_content( post: $post ) ) );

		if ( \defined( 'SOVEREIGNTY_EXCERPT' ) && \SOVEREIGNTY_EXCERPT && ( get_post_format( $post ) === false || $count > \SOVEREIGNTY_EXCERPT_COUNT ) ) { // @phpstan-ignore constant.notFound
			echo get_the_excerpt( $post ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Excerpt is filtered by WP.
		} else {
			self::echo_the_content( $more_text, $post );
		}
	}

	/**
	 * Echo the_content with explicit $post, applying the_content filter.
	 *
	 * @see wp-includes/post-template.php the_content()
	 *
	 * @param string  $more_text The "read more" link text.
	 * @param WP_Post $post      The post object.
	 *
	 * @return void
	 */
	private static function echo_the_content( string $more_text, WP_Post $post ): void {
		/**
		 * Filters the post content.
		 *
		 * @see wp-includes/post-template.php
		 *
		 * @param string $content Content of the current post.
		 *
		 * @return string Filtered content.
		 */
		echo apply_filters( 'the_content', get_the_content( more_link_text: $more_text, post: $post ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WP core filter.
	}

	/**
	 * Echo the HTML tag name for the site title element.
	 *
	 * Outputs 'h1' on the homepage (main heading), 'div' elsewhere.
	 *
	 * @return void
	 */
	public static function site_title_tag(): void {
		echo is_home() ? 'h1' : 'div'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Hardcoded tag names.
	}

	/**
	 * Echo the HTML tag name for the entry title element.
	 *
	 * Outputs 'h1' on singular pages, 'h2' in listings.
	 *
	 * @return void
	 */
	public static function entry_title_tag(): void {
		echo is_singular() ? 'h1' : 'h2'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Hardcoded tag names.
	}
}
