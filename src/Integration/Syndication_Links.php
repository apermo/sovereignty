<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Integration;

/**
 * Syndication Links integration: custom display in entry footer.
 *
 * @link https://github.com/dshanske/syndication-links
 *
 * @package Sovereignty
 */
class Syndication_Links {

	/**
	 * Register integration hooks.
	 *
	 * @return void
	 */
	public static function register(): void {
		add_action( 'init', [ self::class, 'remove_content_filter' ] );
		add_action( 'wp_print_styles', [ self::class, 'dequeue_styles' ], 100 );
		add_action( 'sovereignty_entry_footer', [ self::class, 'display' ] );
	}

	/**
	 * Remove the plugin's the_content filter.
	 *
	 * @return void
	 */
	public static function remove_content_filter(): void {
		remove_filter( 'the_content', [ 'Syn_Config', 'the_content' ], 30 );
	}

	/**
	 * Dequeue the plugin's CSS.
	 *
	 * @return void
	 */
	public static function dequeue_styles(): void {
		wp_dequeue_style( 'syndication-style' );
	}

	/**
	 * Display syndication links in the entry footer.
	 *
	 * @return void
	 */
	public static function display(): void {
		if ( \function_exists( 'get_syndication_links' ) ) {
			echo '<div class="syndication-links">';
			esc_html_e( 'Syndication Links', 'sovereignty' );
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_syndication_links() returns safe HTML.
			echo get_syndication_links( null, [ 'show_text_before' => null ] );
			echo '</div>';
		}
	}
}
