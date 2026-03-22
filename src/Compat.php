<?php

declare(strict_types=1);

namespace Apermo\Sovereignty;

use WP_Query;

/**
 * Backwards compatibility and enhancement layer.
 *
 * Adds HTML5 input attributes to comment forms,
 * lazy loading for images, and WP version polyfills.
 *
 * @package Sovereignty
 */
class Compat {

	/**
	 * Add autocomplete and enterkeyhint attributes to comment form fields.
	 *
	 * @param array $fields The comment form fields.
	 *
	 * @return array
	 */
	public static function comment_autocomplete( array $fields ): array {
		$fields['author'] = \preg_replace( '/<input/', '<input autocomplete="nickname name" enterkeyhint="next" ', $fields['author'] );
		$fields['email']  = \preg_replace( '/<input/', '<input autocomplete="email" inputmode="email" enterkeyhint="next" ', $fields['email'] );
		$fields['url']    = \preg_replace( '/<input/', '<input autocomplete="url" inputmode="url" enterkeyhint="send" ', $fields['url'] );

		return $fields;
	}

	/**
	 * Add enterkeyhint attribute to the comment textarea.
	 *
	 * @param string $field The comment textarea field.
	 *
	 * @return string
	 */
	public static function comment_field_input_type( string $field ): string {
		return \preg_replace( '/<textarea/', '<textarea enterkeyhint="next"', $field );
	}

	/**
	 * Fix archive for "standard" post type.
	 *
	 * @param WP_Query $query The WordPress query object.
	 *
	 * @return void
	 */
	public static function query_format_standard( WP_Query $query ): void {
		if (
			isset( $query->query_vars['post_format'] ) &&
			$query->query_vars['post_format'] === 'post-format-standard'
		) {
			$post_formats = get_theme_support( 'post-formats' );

			if (
				\is_array( $post_formats ) &&
				\is_array( $post_formats[0] ) && \count( $post_formats[0] ) > 0
			) {
				$terms = [];
				foreach ( $post_formats[0] as $format ) {
					$terms[] = 'post-format-' . $format;
				}
				$query->is_tax = false;

				unset(
					$query->query_vars['post_format'],
					$query->query_vars['taxonomy'],
					$query->query_vars['term'],
				);

				$query->set(
					'tax_query',
					[
						'relation' => 'AND',
						[
							'taxonomy' => 'post_format',
							'terms'    => $terms,
							'field'    => 'slug',
							'operator' => 'NOT IN',
						],
					],
				);
			}
		}
	}

	/**
	 * Add lazy loading attribute to images in content.
	 *
	 * @param string $content The post content.
	 *
	 * @return string
	 */
	public static function add_lazy_loading( string $content ): string {
		return \preg_replace( '/(<(?![^>]*\bloading=)[^>]*?)(\ssrc=)(.*?\/?>)/', '\1 loading="lazy" src=\3', $content );
	}

	/**
	 * Disable WP native lazy loading for the_content.
	 *
	 * @param bool   $is_enabled Whether lazy loading is enabled.
	 * @param string $tag_name   The HTML tag name.
	 * @param string $context    The context where lazy loading is applied.
	 *
	 * @return bool
	 */
	public static function disable_native_lazy_loading( bool $is_enabled, string $tag_name, string $context ): bool {
		if ( $context === 'the_content' ) {
			return false;
		}

		return $is_enabled;
	}
}
