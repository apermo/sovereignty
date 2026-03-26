<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Integration;

use Apermo\Sovereignty\Schema;
use Apermo\Sovereignty\Template\Tags;
use WP_Post;

/**
 * Yoast SEO integration: enrich Schema.org JSON-LD with theme-specific data.
 *
 * @link https://developer.yoast.com/features/schema/api/
 *
 * @package Sovereignty
 */
class Yoast {

	/**
	 * Register integration hooks.
	 *
	 * @return void
	 */
	public static function register(): void {
		add_filter( 'wpseo_schema_article', [ self::class, 'enrich_article' ] );
	}

	/**
	 * Add theme-specific data to the Yoast Article schema piece.
	 *
	 * Adds timeRequired (reading time) and comment[] (approved comments).
	 *
	 * @param array $data The Article schema data from Yoast.
	 *
	 * @return array The enriched Article schema data.
	 */
	public static function enrich_article( array $data ): array {
		$post = get_post(); // phpcs:ignore Apermo.WordPress.ImplicitPostFunction -- Filter callback, no $post parameter available.

		if ( ! $post instanceof WP_Post ) {
			return $data;
		}

		$data['timeRequired'] = \sprintf( 'PT%dM', Tags::get_reading_time( $post ) );

		if ( (int) get_comments_number( $post ) > 0 ) {
			$comments = get_comments(
				[
					'post_id' => $post->ID,
					'status'  => 'approve',
					'number'  => 100,
				],
			);

			if ( $comments !== [] ) {
				$data['comment'] = \array_map( [ Schema::class, 'build_comment' ], $comments );
			}
		}

		return $data;
	}
}
