<?php
if ( ! function_exists( 'autonomie_content_nav' ) ) :
	/**
	 * Display navigation to next/previous pages when applicable.
	 *
	 * @since Autonomie 1.0.0
	 *
	 * @param string $nav_id The navigation element ID.
	 */
	function autonomie_content_nav( string $nav_id ): void {
		?>
		<?php if ( is_home() || is_archive() || is_search() ) : // Navigation links for home, archive, and search pages. ?>
		<nav id="archive-nav">
			<div class="assistive-text"><?php esc_html_e( 'Post navigation', 'autonomie' ); ?></div>
			<?php echo wp_kses_post( paginate_links() ); ?>
		</nav><!-- #<?php echo esc_html( $nav_id ); ?> -->
		<?php endif; ?>
		<?php
	}
endif; // End autonomie_content_nav.

if ( ! function_exists( 'autonomie_posted_by' ) ) :
	/**
	 * Prints HTML with meta information for the current author.
	 * Create your own autonomie_posted_by to override in a child theme.
	 *
	 * @since Autonomie 1.0.0
	 */
	function autonomie_posted_by(): void {
		printf(
			'<address class="byline">
				<span class="author p-author vcard hcard h-card" itemprop="author" itemscope itemtype="https://schema.org/Person">
					%1$s
					<a class="url uid u-url u-uid fn p-name" href="%2$s" title="%3$s" rel="author">
						<span itemprop="name">%4$s</span>
					</a>
					<link itemprop="url" href="%2$s" />
				</span>
			</address>',
			get_avatar( get_the_author_meta( 'ID' ), 40 ),
			esc_url( get_author_posts_url( (int) get_the_author_meta( 'ID' ) ) ),
			// translators: %s is the author name.
			esc_attr( sprintf( __( 'View all posts by %s', 'autonomie' ), get_the_author() ) ),
			esc_html( get_the_author() )
		);
	}
endif;

if ( ! function_exists( 'autonomie_posted_on' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time.
	 * Create your own autonomie_posted_on to override in a child theme.
	 *
	 * @since Autonomie 1.0.0
	 *
	 * @param string $type The date type: 'published' or 'updated'.
	 */
	function autonomie_posted_on( string $type = 'published' ): void {

		if ( ! in_array( $type, [ 'published', 'updated' ], true ) ) {
			$type = 'published';
		}

		if ( (bool) get_query_var( 'is_now', false ) ) {
			$type = 'updated';
		}

		// phpcs:disable Apermo.WordPress.ImplicitPostFunction
		if ( $type === 'updated' ) {
			// Updated.
			$time = get_the_modified_time();
			$date_c    = get_the_modified_date( 'c' );
			$date      = get_the_modified_date();
			$item_prop = 'dateModified';
		} else {
			// Published.
			$time = get_the_time();
			$date_c    = get_the_date( 'c' );
			$date      = get_the_date();
			$item_prop = 'datePublished';
		}

		// translators: the author byline.
		printf(
			// translators: %1$s = post permalink, %2$s = post date, %3$s = post date in ISO 8601 format, %4$s = post date, %5$s = date type (published or updated), %6$s = itemprop value (datePublished or dateModified).
			'<a href="%1$s" title="%2$s" rel="bookmark" class="url u-url" itemprop="mainEntityOfPage"><time class="entry-date %5$s dt-%5$s" datetime="%3$s" itemprop="%6$s">%4$s</time></a>',
			esc_url( get_permalink() ),
			// phpcs:enable Apermo.WordPress.ImplicitPostFunction
			esc_attr( $time ),
			esc_attr( $date_c ),
			esc_html( $date ),
			esc_html( $type ),
			esc_html( $item_prop ),
		);
	}
endif;

/**
 * Display the id for the post div.
 *
 * @param string|null $post_id The post id.
 */
function autonomie_post_id( ?string $post_id = null ): void {
	if ( $post_id !== null ) {
		echo 'id="' . esc_attr( $post_id ) . '"';
	} else {
		echo 'id="' . esc_attr( autonomie_get_post_id() ) . '"';
	}
}

/**
 * Retrieve the id for the post div.
 *
 * @return string The post-id.
 */
function autonomie_get_post_id(): string {
	// phpcs:ignore Apermo.WordPress.ImplicitPostFunction
	$post_id = 'post-' . get_the_ID();

	// phpcs:ignore Apermo.WordPress.ImplicitPostFunction
	return apply_filters( 'autonomie_post_id', $post_id, get_the_ID() );
}

/**
 * Display the CSS class for the main element.
 *
 * @param string $class Additional CSS class names.
 */
function autonomie_main_class( string $class = '' ): void { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.classFound
	// Separates class names with a single space, collates class names for body element.
	echo 'class="' . esc_attr( implode( ' ', autonomie_get_main_class( $class ) ) ) . '"';
}

/**
 * Retrieve the CSS classes for the main element.
 *
 * @param string|string[] $class Additional CSS class names.
 *
 * @return string[] An array of CSS class names.
 */
function autonomie_get_main_class( string|array $class = '' ): array { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.classFound
	$classes = [];

	if ( is_singular() ) {
		$classes = autonomie_get_post_classes( $classes );
	}

	if ( ! empty( $class ) ) {
		if ( ! is_array( $class ) ) {
			$class = preg_split( '#\s+#', $class );
		}
		$classes = array_merge( $classes, $class );
	} else {
		// Ensure that we always coerce class to being an array.
		$class = [];
	}

	$classes = array_map( 'esc_attr', $classes );

	/**
	 * Filters the list of CSS main class names for the current post or page.
	 *
	 * @since 2.8.0
	 *
	 * @param string[] $classes An array of main class names.
	 * @param string[] $class   An array of additional class names added to the main.
	 */
	$classes = apply_filters( 'autonomie_main_class', $classes, $class );

	return array_unique( $classes );
}

/**
 * Retrieve the archive title.
 *
 * @return string The archive title.
 */
function autonomie_get_the_archive_title(): string {
	if ( is_archive() ) {
		return get_the_archive_title();
	}

	if ( is_search() ) {
		// translators: The title of the search results page.
		return sprintf( __( 'Search Results for: %s', 'autonomie' ), '<span>' . get_search_query() . '</span>' );
	}

	// TODO: Consider to add a default return value.
	return '';
}

/**
 * Check if page banner is enabled.
 *
 * @return bool
 */
function autonomie_show_page_banner(): bool {
	if ( is_home() && ! display_header_text() ) {
		return false;
	}

	if ( is_home() || is_archive() || is_search() ) {
		return true;
	}

	return false;
}

/**
 * Adds support for standard post-format
 *
 * TODO: Consider to rename this function, as it returns the post format slug, not a string representation of the post format.
 *
 * @return string
 */
function autonomie_get_post_format(): string {
	return get_post_format() ?: 'standard';
}

/**
 * Add support for Attachment and Article.
 *
 * @return string
 */
function autonomie_get_post_format_string(): string {
	if ( get_post_type() === 'attachment' ) {
		return __( 'Attachment', 'autonomie' );
	}

	if ( get_post_type() === 'page' ) {
		return __( 'Page', 'autonomie' );
	}

	if ( get_post_format() ) {
		return get_post_format();
	}

	return __( 'Text', 'autonomie' );
}

/**
 * Adds support for "standard" post-format archive links.
 *
 * @param string $post_format The post format slug.
 *
 * @return string
 */
function autonomie_get_post_format_link( string $post_format ): string {
	if ( in_array( get_post_type(), [ 'page', 'attachment' ], true ) ) {
		// phpcs:ignore Apermo.WordPress.ImplicitPostFunction
		return get_permalink();
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
		$term_link = str_replace( '%post_format%', 'standard', $term_link );
		$term_link = home_url( user_trailingslashit( $term_link, 'category' ) );
	}

	return $term_link;
}

/**
 * Check archive type.
 *
 * @return string
 */
function autonomie_get_archive_type(): string {
	$type = '';

	if ( is_author() ) {
		$type = 'author';
	}

	return (string) apply_filters( 'autonomie_archive_type', $type );
}

/**
 * Returns Meta-Data like "number of posts" and "subscribe buttons" for the Author.
 *
 * @return string
 */
function autonomie_get_archive_author_meta(): string {
	$meta = [];

	$meta[] = sprintf(
		// translators: list of followers.
		__( '%s Followers', 'autonomie' ),
		apply_filters( 'autonomie_archive_author_followers', 0, get_the_author_meta( 'ID' ) )
	);
	$meta[] = sprintf(
		// translators: a post counter.
		__( '%s Posts', 'autonomie' ),
		count_user_posts( (int) get_the_author_meta( 'ID' ) )
	);
	$meta[] = sprintf(
		'<indie-action do="follow" with="%1$s"><a rel="alternate" class="feed u-feed openwebicons-feed" href="%1$s">%2$s</a></indie-action>',
		get_author_feed_link( (int) get_the_author_meta( 'ID' ) ),
		__( 'Subscribe', 'autonomie' )
	);

	$meta = apply_filters( 'autonomie_archive_author_meta', $meta, get_the_author_meta( 'ID' ) );

	return implode( ' | ', $meta );
}

/**
 * Returns the page description
 *
 * @return string The page description
 */
function autonomie_get_the_archive_description(): string {
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
		// @see https://github.com/raamdev/independent-publisher/blob/513e7ff71312f585f13eb1460b4d9bc74d0b59bd/inc/template-tags.php#L674
		global $wp_query;
		$total = $wp_query->found_posts;
		// translators: Description for search results.
		$stats_text = sprintf( _n( 'Found %1$s search result for <strong>%2$s</strong>.', 'Found %1$s search results for <strong>%2$s</strong>.', $total, 'autonomie' ), number_format_i18n( $total ), get_search_query() );

		return wpautop( $stats_text );
	}

	// TODO: Consider to add a default return value.
	return '';
}

/**
 * Estimated reading time
 */
function autonomie_reading_time(): void {
	$content = get_post_field( 'post_content' );
	$word_count = str_word_count( wp_strip_all_tags( $content ) );
	$readingtime = (int) ceil( $word_count / 200 );

	printf(
		// translators: %1$s = reading time in minutes.
		_n( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output contains intentional HTML markup from translation strings.
			'<span class="entry-duration"><time datetime="PT%1$sM" class="dt-duration" itemprop="timeRequired">%1$s minute</time> to read</span>', // phpcs:ignore WordPress.WP.I18n.NoHtmlWrappedStrings
			'<span class="entry-duration"><time datetime="PT%1$sM" class="dt-duration" itemprop="timeRequired">%1$s minutes</time> to read</span>', // phpcs:ignore WordPress.WP.I18n.NoHtmlWrappedStrings
			$readingtime,
			'autonomie'
		),
		esc_html( number_format_i18n( $readingtime ) )
	);
}

/**
 * Add possibility to use `the_excerpt` instead of `the_content` for longer posts
 *
 * @return void
 */
function autonomie_the_content(): void {
	// phpcs:disable Apermo.WordPress.ImplicitPostFunction
	if ( is_search() ) {
		the_excerpt();
		return;
	}

	if ( is_singular() ) {
		the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'autonomie' ) );
		return;
	}

	$count = str_word_count( wp_strip_all_tags( get_the_content() ) );

	if ( defined( 'AUTONOMIE_EXCERPT' ) && AUTONOMIE_EXCERPT && ( get_post_format() === false || $count > AUTONOMIE_EXCERPT_COUNT ) ) { // @phpstan-ignore constant.notFound
		the_excerpt();
	} else {
		the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'autonomie' ) );
	}
	// phpcs:enable Apermo.WordPress.ImplicitPostFunction
}
