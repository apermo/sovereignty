<?php
/**
 * Autonomie back compat handling
 *
 * Some functions to add backwards compatibility to older WordPress versions
 * or to add some new functions to be more (for example)  compatible
 *
 * @package Autonomie
 * @subpackage compat
 * @since Autonomie 1.5.0
 */

/**
 * Adds compat handling for WP versions pre-*.
 *
 * @category pre-all
 */

/**
 * Adds the new  input types to the comment-form
 *
 * @param array $fields The comment form fields.
 * @return array
 */
function autonomie_comment_autocomplete( array $fields ): array { // phpcs:ignore Generic.NamingConventions.CamelCapsFunctionName.NotCamelCaps
	$fields['author'] = preg_replace( '/<input/', '<input autocomplete="nickname name" enterkeyhint="next" ', $fields['author'] );
	$fields['email'] = preg_replace( '/<input/', '<input autocomplete="email" inputmode="email" enterkeyhint="next" ', $fields['email'] );
	$fields['url'] = preg_replace( '/<input/', '<input autocomplete="url" inputmode="url" enterkeyhint="send" ', $fields['url'] );

	return $fields;
}
add_filter( 'comment_form_default_fields', 'autonomie_comment_autocomplete' );

/**
 * Adds the new HTML5 input types to the comment-text-area
 *
 * @param string $field The comment textarea field.
 * @return string
 */
function autonomie_comment_field_input_type( string $field ): string { // phpcs:ignore Generic.NamingConventions.CamelCapsFunctionName.NotCamelCaps
	return preg_replace( '/<textarea/', '<textarea enterkeyhint="next"', $field );
}
add_filter( 'comment_form_field_comment', 'autonomie_comment_field_input_type' );

/**
 * Fix archive for "standard" post type
 *
 * @param WP_Query $query The WordPress query object.
 *
 * @return void
 */
function autonomie_query_format_standard( WP_Query $query ): void { // phpcs:ignore Generic.NamingConventions.CamelCapsFunctionName.NotCamelCaps
	if (
		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps -- WordPress core property.
		isset( $query->query_vars['post_format'] ) &&
		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps -- WordPress core property.
		$query->query_vars['post_format'] === 'post-format-standard'
	) {
		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps -- WordPress core naming convention.
		$post_formats = get_theme_support( 'post-formats' );

		if (
			// phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps -- WordPress core naming convention.
			$post_formats &&
			// phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps -- WordPress core naming convention.
			is_array( $post_formats[0] ) && count( $post_formats[0] )
		) {
			$terms = [];
			// phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps -- WordPress core naming convention.
			foreach ( $post_formats[0] as $format ) {
				$terms[] = 'post-format-' . $format;
			}
			// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps -- WordPress core property.
			$query->is_tax = false;

			unset(
				// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps -- WordPress core property.
				$query->query_vars['post_format'],
				// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps -- WordPress core property.
				$query->query_vars['taxonomy'],
				// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps -- WordPress core property.
				$query->query_vars['term']
			);

			$query->set(
				'tax_query',
				[
					'relation' => 'AND',
					[
						'taxonomy' => 'post_format',
						'terms' => $terms,
						'field' => 'slug',
						'operator' => 'NOT IN',
					],
				]
			);
		}
	}
}
add_action( 'pre_get_posts', 'autonomie_query_format_standard' );

/**
 * Add lazy loading attribute
 *
 * @see https://www.webrocker.de/2019/08/20/wordpress-filter-for-lazy-loading-src/
 *
 * @param string $content The post content.
 *
 * @return string the filtered content
 */
function autonomie_add_lazy_loading( string $content ): string { // phpcs:ignore Generic.NamingConventions.CamelCapsFunctionName.NotCamelCaps
	$content = preg_replace( '/(<[^>]*?)(\ssrc=)(.*?\/?>)/', '\1 loading="lazy" src=\3', $content );

	return $content;
}
add_filter( 'the_content', 'autonomie_add_lazy_loading', 99 );

add_filter(
	'wp_lazy_loading_enabled',
	function ( bool $default, string $tag_name, string $context ): bool { // phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps, Universal.NamingConventions.NoReservedKeywordParameterNames.defaultFound
		if ( $context === 'the_content' ) {
			return false;
		}

		return $default;
	},
	20,
	3
);

if ( ! function_exists( 'get_self_link' ) ) {
	/**
	 * Returns the link for the currently displayed feed.
	 *
	 * @since 5.3.0
	 *
	 * @return string Correct link for the atom:self element.
	 */
	function get_self_link(): string { // phpcs:ignore Generic.NamingConventions.CamelCapsFunctionName.NotCamelCaps
		$host = @parse_url( home_url() );
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Polyfill for WP core function.
		return set_url_scheme( 'http://' . $host['host'] . wp_unslash( $_SERVER['REQUEST_URI'] ) );
	}
}
