<?php
/**
 * Autonomie Syndication Links
 *
 * Adds support for Syndication Links
 *
 * @link https://github.com/dshanske/syndication-links
 *
 * @package Autonomie
 * @subpackage indieweb
 */

/**
 * Remove the integration of `the_content` filter.
 *
 * @return void
 */
function autonomie_syndication_links_init(): void {
	remove_filter( 'the_content', [ 'Syn_Config', 'the_content' ], 30 );
}
add_action( 'init', 'autonomie_syndication_links_init' );

/**
 * Remove the Syndication-Links CSS.
 *
 * @return void
 */
function autonomie_syndication_links_print_scripts(): void {
	wp_dequeue_style( 'syndication-style' );
}
add_action( 'wp_print_styles', 'autonomie_syndication_links_print_scripts', 100 );

/**
 * Added links to the post-footer.
 *
 * @return void
 */
function autonomie_syndication_links(): void {
	if ( function_exists( 'get_syndication_links' ) ) {
		echo '<div class="syndication-links">';
		esc_html_e( 'Syndication Links', 'autonomie' );
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_syndication_links() returns safe HTML.
		echo get_syndication_links( null, [ 'show_text_before' => null ] );
		echo '</div>';
	}
}
add_action( 'autonomie_entry_footer', 'autonomie_syndication_links' );
