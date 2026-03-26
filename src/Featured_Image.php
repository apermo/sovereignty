<?php

declare(strict_types=1);

namespace Apermo\Sovereignty;

use WP_Post;

/**
 * Featured image handling: post thumbnails, post covers, and block editor support.
 *
 * @package Sovereignty
 */
class Featured_Image {

	/**
	 * Output the post thumbnail with microformat classes.
	 *
	 * @param WP_Post $post   The post object.
	 * @param string  $before HTML to output before the thumbnail.
	 * @param string  $after  HTML to output after the thumbnail.
	 *
	 * @return void
	 */
	public static function the_post_thumbnail( WP_Post $post, string $before = '', string $after = '' ): void {
		if ( self::has_full_width( $post ) ) {
			return;
		}

		if ( get_the_post_thumbnail( $post ) !== '' ) {
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post ), 'post-thumbnail' );

			if ( $image['1'] <= Config::int( 'sovereignty.images.inlineThreshold' ) ) {
				return;
			}

			$class = 'photo';

			$post_format = get_post_format( $post );

			if ( \in_array( $post_format, [ 'image', 'gallery' ], true ) ) {
				$class .= ' u-photo';
			} else {
				$class .= ' u-featured';
			}

			// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- $before and $after contain trusted HTML from template.
			echo $before
				. get_the_post_thumbnail(
					$post,
					'post-thumbnail',
					[
						'class'   => $class,
						'loading' => 'lazy',
					],
				)
				. $after;
			// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Prepend small thumbnails to post content.
	 *
	 * Called as a the_content filter — resolves $post internally.
	 *
	 * @param string $content The post content.
	 *
	 * @return string The modified post content.
	 */
	public static function content_post_thumbnail( string $content ): string {
		$post = get_post(); // phpcs:ignore Apermo.WordPress.ImplicitPostFunction -- Hook callback, no $post parameter available.

		if ( ! $post instanceof WP_Post ) {
			return $content;
		}

		if ( get_the_post_thumbnail( $post ) !== '' ) {
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post ), 'post-thumbnail' );

			if ( $image['1'] > Config::int( 'sovereignty.images.inlineThreshold' ) ) {
				return $content;
			}

			$class = 'alignright photo';

			$post_format = get_post_format( $post );

			if ( \in_array( $post_format, [ 'image', 'gallery' ], true ) ) {
				$class .= ' u-photo';
			} else {
				$class .= ' u-featured';
			}

			$thumbnail = get_the_post_thumbnail(
				$post,
				'post-thumbnail',
				[
					'class'   => $class,
					'loading' => 'lazy',
				],
			);

			return \sprintf( '<p>%s</p>%s', $thumbnail, $content );
		}

		return $content;
	}

	/**
	 * Add a checkbox for Post Covers to the featured image metabox.
	 *
	 * @param string $content The admin post thumbnail HTML.
	 * @param int    $post_id The post ID.
	 *
	 * @return string The modified admin post thumbnail HTML.
	 */
	public static function admin_thumbnail_html( string $content, int $post_id ): string {
		$text = esc_html__( 'Use as post cover (full-width)', 'sovereignty' );

		$value   = esc_attr( get_post_meta( $post_id, 'full_width_featured_image', true ) );
		$checked = checked( $value, 1, false );

		$label = \sprintf(
			'<input type="hidden" name="full_width_featured_image" value="0">'
			. '<label for="full_width_featured_image" class="selectit">'
			. '<input name="full_width_featured_image" type="checkbox" id="full_width_featured_image" value="1" %s> %s'
			. '</label>',
			$checked,
			$text,
		);

		return $content . $label;
	}

	/**
	 * Save the Post Cover meta on save_post.
	 *
	 * @param int $post_id The ID of the post being saved.
	 *
	 * @return void
	 */
	public static function save_post( int $post_id ): void {
		if ( \defined( 'DOING_AUTOSAVE' ) && \DOING_AUTOSAVE ) {
			return;
		}

		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce is verified by WordPress core on the save_post hook.

		if ( ! \array_key_exists( 'full_width_featured_image', $_POST ) ) {
			return;
		}

		if ( ! \array_key_exists( 'post_type', $_POST ) ) {
			return;
		}

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

	/**
	 * Check if the post has full-width featured image enabled.
	 *
	 * When called from hook callbacks (no $post available), pass get_post().
	 *
	 * @param WP_Post $post The post object.
	 *
	 * @return bool
	 */
	public static function has_full_width( WP_Post $post ): bool {
		if ( ! is_singular() ) {
			return false;
		}

		if ( ! has_post_thumbnail( $post ) ) {
			return false;
		}

		$full_width_featured_image = get_post_meta( $post->ID, 'full_width_featured_image', true );

		return $full_width_featured_image === '1';
	}

	/**
	 * Enqueue inline CSS for full-width featured image header.
	 *
	 * Hook callback — resolves $post internally.
	 *
	 * @return void
	 */
	public static function enqueue_scripts(): void {
		$post = get_post(); // phpcs:ignore Apermo.WordPress.ImplicitPostFunction -- Hook callback, no $post parameter available.

		if ( ! $post instanceof WP_Post ) {
			return;
		}

		if ( is_singular() && self::has_full_width( $post ) ) {
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post ), 'full' );

			$style = '.entry-header {
				background: linear-gradient(190deg, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.7)), url(' . $image[0] . ') no-repeat center center scroll;
			}' . \PHP_EOL;

			wp_add_inline_style( 'sovereignty-style', $style );
		}
	}

	/**
	 * Add full-width-featured-image to post class.
	 *
	 * Hook callback — resolves $post internally.
	 *
	 * @param array $classes The array of post classes.
	 *
	 * @return array The modified array of post classes.
	 */
	public static function post_class( array $classes ): array {
		$post = get_post(); // phpcs:ignore Apermo.WordPress.ImplicitPostFunction -- Hook callback, no $post parameter available.

		if ( $post instanceof WP_Post && is_singular() && self::has_full_width( $post ) ) {
			$classes[] = 'has-full-width-featured-image';
		}

		return $classes;
	}

	/**
	 * Register the full_width_featured_image meta.
	 *
	 * @return void
	 */
	public static function register_meta(): void {
		register_meta(
			'post',
			'full_width_featured_image',
			[
				'show_in_rest' => true,
				'single'       => true,
				'type'         => 'boolean',
			],
		);
	}

	/**
	 * Enqueue block editor assets for the full-width toggle.
	 *
	 * @return void
	 */
	public static function enqueue_block_editor_assets(): void {
		wp_enqueue_script(
			'sovereignty-block-editor',
			get_template_directory_uri() . '/assets/js/block-editor.js',
			[ 'wp-editor', 'wp-i18n', 'wp-element', 'wp-compose', 'wp-components' ],
			'1.0.0',
			true,
		);
	}
}
