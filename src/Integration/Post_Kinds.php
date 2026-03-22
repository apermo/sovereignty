<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Integration;

use Kind_Taxonomy;
use Kind_View;

/**
 * Post Kinds integration: custom display for IndieWeb post kinds.
 *
 * @link https://github.com/dshanske/indieweb-post-kinds
 *
 * @package Sovereignty
 */
class Post_Kinds {

	/**
	 * Register integration hooks.
	 *
	 * @return void
	 */
	public static function register(): void {
		add_action( 'init', [ self::class, 'remove_defaults' ] );
		add_action( 'sovereignty_before_entry_content', [ self::class, 'content' ] );
		add_filter( 'sovereignty_post_format', [ self::class, 'format' ] );
	}

	/**
	 * Remove native Post-Kinds implementation.
	 *
	 * @return void
	 */
	public static function remove_defaults(): void {
		if ( \method_exists( 'Kind_Taxonomy', 'get_icon' ) ) { // @phpstan-ignore function.impossibleType
			add_filter( 'kind_icon_display', '__return_false', 10 );
		}

		remove_filter( 'the_content', [ 'Kind_View', 'content_response' ], 9 );
		remove_filter( 'the_excerpt', [ 'Kind_View', 'excerpt_response' ], 9 );
		remove_action( 'wp_enqueue_scripts', [ 'Post_Kinds_Plugin', 'style_load' ] );
	}

	/**
	 * Add the reply-context above the article body.
	 *
	 * @return void
	 */
	public static function content(): void {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Kind_View::get_display() returns safe plugin HTML.
		\printf( '<div class="entry-reaction">%s</div>', Kind_View::get_display() ); // @phpstan-ignore class.notFound
	}

	/**
	 * Replace the Post-Format header with the Post-Kinds header.
	 *
	 * @param string $post_format_html Post-Format HTML.
	 *
	 * @return string Post-Kind HTML.
	 */
	public static function format( string $post_format_html ): string {
		if ( ! get_post_kind_slug() ) { // @phpstan-ignore function.notFound (Post Kinds plugin)
			return $post_format_html;
		}

		$kind_slug   = get_post_kind_slug(); // @phpstan-ignore function.notFound
		$kind_icon   = Kind_Taxonomy::get_icon( $kind_slug ); // @phpstan-ignore class.notFound
		$kind_string = get_post_kind_string( $kind_slug ); // @phpstan-ignore function.notFound
		$kind_link   = esc_url( get_post_kind_link( get_post_kind() ) ); // @phpstan-ignore function.notFound, function.notFound

		return \sprintf( '<a class="kind kind-%s" href="%s">%s %s</a>', $kind_slug, $kind_link, $kind_icon, $kind_string );
	}
}
