<?php

declare(strict_types=1);

namespace Apermo\Sovereignty;

/**
 * Featured image handling: post thumbnails, post covers, and block editor support.
 *
 * @package Sovereignty
 */
class Featured_Image {

	/**
	 * Output the post thumbnail with microformat classes.
	 *
	 * @param string $before HTML to output before the thumbnail.
	 * @param string $after  HTML to output after the thumbnail.
	 *
	 * @return void
	 */
	public static function the_post_thumbnail( string $before = '', string $after = '' ): void {
		// phpcs:disable Apermo.WordPress.ImplicitPostFunction
		if ( self::has_full_width() ) {
			return;
		}

		if ( get_the_post_thumbnail() !== '' ) {
			$image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'post-thumbnail' );

			if ( $image['1'] <= '400' ) {
				return;
			}

			$class = 'photo';

			$post_format = get_post_format();

			if ( \in_array( $post_format, [ 'image', 'gallery' ], true ) ) {
				$class .= ' u-photo';
			} else {
				$class .= ' u-featured';
			}

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $before contains trusted HTML from template.
			echo $before;

			the_post_thumbnail(
				'post-thumbnail',
				[
					'class'    => $class,
					'itemprop' => 'image',
					'loading'  => 'lazy',
				],
			);

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $after contains trusted HTML from template.
			echo $after;
		}
		// phpcs:enable Apermo.WordPress.ImplicitPostFunction
	}

	/**
	 * Prepend small thumbnails to post content.
	 *
	 * @param string $content The post content.
	 *
	 * @return string The modified post content.
	 */
	public static function content_post_thumbnail( string $content ): string {
		// phpcs:disable Apermo.WordPress.ImplicitPostFunction
		if ( get_the_post_thumbnail() !== '' ) {
			$image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'post-thumbnail' );

			if ( $image['1'] > '400' ) {
				return $content;
			}

			$class = 'alignright photo';

			$post_format = get_post_format();

			if ( \in_array( $post_format, [ 'image', 'gallery' ], true ) ) {
				$class .= ' u-photo';
			} else {
				$class .= ' u-featured';
			}

			$thumbnail = get_the_post_thumbnail(
				null,
				'post-thumbnail',
				[
					'class'    => $class,
					'itemprop' => 'image',
					'loading'  => 'lazy',
				],
			);

			return \sprintf( '<p>%s</p>%s', $thumbnail, $content );
		}

		// phpcs:enable Apermo.WordPress.ImplicitPostFunction
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
	 * Check if the current post has full-width featured image enabled.
	 *
	 * @return bool
	 */
	public static function has_full_width(): bool {
		if ( ! is_singular() ) {
			return false;
		}

		// phpcs:ignore Apermo.WordPress.ImplicitPostFunction
		if ( ! has_post_thumbnail() ) {
			return false;
		}

		// phpcs:ignore Apermo.WordPress.ImplicitPostFunction
		$full_width_featured_image = get_post_meta( get_the_ID(), 'full_width_featured_image', true );

		return $full_width_featured_image === '1';
	}

	/**
	 * Enqueue inline CSS for full-width featured image header.
	 *
	 * @return void
	 */
	public static function enqueue_scripts(): void {
		if ( is_singular() && self::has_full_width() ) {
			// phpcs:ignore Apermo.WordPress.ImplicitPostFunction
			$image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );

			$css = '.entry-header {
				background: linear-gradient(190deg, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.7)), url(' . $image[0] . ') no-repeat center center scroll;
			}' . \PHP_EOL;

			wp_add_inline_style( 'sovereignty-style', $css );
		}
	}

	/**
	 * Add full-width-featured-image to post class.
	 *
	 * @param array $classes The array of post classes.
	 *
	 * @return array The modified array of post classes.
	 */
	public static function post_class( array $classes ): array {
		if ( is_singular() && self::has_full_width() ) {
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
