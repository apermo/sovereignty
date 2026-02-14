<?php
/**
 * Adds post-thumbnail support :)
 *
 * @param string $before HTML to output before the thumbnail.
 * @param string $after  HTML to output after the thumbnail.
 *
 * @since Autonomie 1.0.0
 */
function autonomie_the_post_thumbnail( string $before = '', string $after = '' ): void {
	if ( autonomie_has_full_width_featured_image() ) {
		return;
	}

	if ( get_the_post_thumbnail() !== '' ) {
		$image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'post-thumbnail' );

		if ( $image['1'] <= '400' ) {
			return;
		}

		$class = 'photo';

		$post_format = get_post_format();

		// Use `u-photo` on photo/gallery posts.
		if ( in_array( $post_format, [ 'image', 'gallery' ], true ) ) {
			$class .= ' u-photo';
		} else { // Otherwise use `u-featured`.
			$class .= ' u-featured';
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $before contains trusted HTML from template.
		echo $before;

		the_post_thumbnail(
			'post-thumbnail',
			[
				'class' => $class,
				'itemprop' => 'image',
				'loading' => 'lazy',
			]
		);

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $after contains trusted HTML from template.
		echo $after;
	}
}

/**
 * Adds post-thumbnail support :)
 *
 * @param string $content The post content.
 *
 * @return string The modified post content.
 *
 * @since Autonomie 1.0.0
 */
function autonomie_content_post_thumbnail( string $content ): string {
	if ( get_the_post_thumbnail() !== '' ) {
		$image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'post-thumbnail' );

		if ( $image['1'] > '400' ) {
			return $content;
		}

		$class = 'alignright photo';

		$post_format = get_post_format();

		// Use `u-photo` on photo/gallery posts.
		if ( in_array( $post_format, [ 'image', 'gallery' ], true ) ) {
			$class .= ' u-photo';
		} else { // Otherwise use `u-featured`.
			$class .= ' u-featured';
		}

		$thumbnail = get_the_post_thumbnail(
			null,
			'post-thumbnail',
			[
				'class' => $class,
				'itemprop' => 'image',
				'loading' => 'lazy',
			]
		);

		return sprintf( '<p>%s</p>%s', $thumbnail, $content );
	}

	return $content;
}
add_filter( 'the_content', 'autonomie_content_post_thumbnail' );

/**
 * Add a checkbox for Post Covers to the featured image metabox.
 *
 * @param string $content The admin post thumbnail HTML.
 * @param int    $post_id  The post ID.
 *
 * @return string The modified admin post thumbnail HTML.
 */
function autonomie_featured_image_meta( string $content, int $post_id ): string {
	$text = esc_html__( 'Use as post cover (full-width)', 'autonomie' );

	$value = esc_attr( get_post_meta( $post_id, 'full_width_featured_image', true ) );
	$label = '<input type="hidden" name="full_width_featured_image" value="0">';
	$label .= '<label for="full_width_featured_image" class="selectit"><input name="full_width_featured_image" type="checkbox" id="full_width_featured_image" value="1" ' . checked( $value, 1, false ) . '> ' . $text . '</label>';

	return $content . $label;
}
add_filter( 'admin_post_thumbnail_html', 'autonomie_featured_image_meta', 10, 2 );

/**
 * Safe the Post Covers
 *
 * @param int $post_id The ID of the post being saved.
 *
 * @return void
 */
function autonomie_save_post( int $post_id ): void {
	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce is verified by WordPress core on the save_post hook.

	if ( ! array_key_exists( 'full_width_featured_image', $_POST ) ) {
		return;
	}

	if ( ! array_key_exists( 'post_type', $_POST ) ) {
		return;
	}

	// Check the user's permissions.
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- post_type is only compared, not stored.
	if ( $_POST['post_type'] === 'page' ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}
	} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
	}

	$full_width_featured_image = sanitize_text_field( wp_unslash( $_POST['full_width_featured_image'] ) );
	update_post_meta( $post_id, 'full_width_featured_image', $full_width_featured_image );

	// phpcs:enable WordPress.Security.NonceVerification.Missing
}
add_action( 'save_post', 'autonomie_save_post', 5, 1 );

/**
 * Return true if Auto-Set Featured Image as Post Cover is enabled and it hasn't
 * been disabled for this post.
 *
 * Returns true if the current post has Full Width Featured Image enabled.
 *
 * Returns false if not a Single post type or there is no Featured Image selected
 * or none of the above conditions are true.
 *
 * @return bool Whether the post has a full-width featured image.
 */
function autonomie_has_full_width_featured_image(): bool {
	// If this isn't a Single post type, or we don't have a Featured Image set.
	if ( ! ( is_single() || is_page() ) || ! has_post_thumbnail() ) {
		return false;
	}

	$full_width_featured_image = get_post_meta( get_the_ID(), 'full_width_featured_image', true );

	// If "Use featured image as Post Cover" has been checked in the Featured Image meta box, return true.
	return $full_width_featured_image === '1';
}

/**
 * Enqueue theme scripts
 *
 * @uses wp_enqueue_scripts() To enqueue scripts
 *
 * @since Autonomie 1.0.0
 */
function autonomie_enqueue_featured_image_scripts(): void {
	if ( is_singular() && autonomie_has_full_width_featured_image() ) {
		$image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );

		$css = '.entry-header {
			background: linear-gradient(190deg, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.7)), url(' . $image[0] . ') no-repeat center center scroll;
		}' . PHP_EOL;

		wp_add_inline_style( 'autonomie-style', $css );
	}
}
add_action( 'wp_enqueue_scripts', 'autonomie_enqueue_featured_image_scripts' );

/**
 * Add full-width-featured-image to body class when displaying a post with Full Width Featured Image enabled.
 *
 * @param array $classes The array of post classes.
 *
 * @return array The modified array of post classes.
 */
function autonomie_full_width_featured_image_post_class( array $classes ): array {
	if ( is_singular() && autonomie_has_full_width_featured_image() ) {
		$classes[] = 'has-full-width-featured-image';
	}
	return $classes;
}
add_filter( 'post_class', 'autonomie_full_width_featured_image_post_class' );

/**
 * Register the `full_width_featured_image` meta
 *
 * @return void
 */
function autonomie_register_meta(): void {
	register_meta(
		'post',
		'full_width_featured_image',
		[
			'show_in_rest' => true,
			'single' => true,
			'type' => 'boolean',
		]
	);
}
add_action( 'init', 'autonomie_register_meta' );

/**
 * Enqueue the required block editor assets/JS files
 *
 * @return void
 */
function autonomie_enqueue_block_editor_assets(): void {
	wp_enqueue_script(
		'autonomie-block-editor',
		get_template_directory_uri() . '/assets/js/block-editor.js',
		[ 'wp-editor', 'wp-i18n', 'wp-element', 'wp-compose', 'wp-components' ],
		'1.0.0',
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'autonomie_enqueue_block_editor_assets', 9 );
