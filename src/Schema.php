<?php

declare(strict_types=1);

namespace Apermo\Sovereignty;

use Apermo\Sovereignty\Template\Tags;
use WP_Comment;
use WP_Post;

/**
 * JSON-LD structured data output.
 *
 * Standalone fallback when no SEO plugin handles Schema.org.
 * Skips output when Yoast SEO is active (Yoast owns the JSON-LD).
 *
 * @link https://developers.google.com/search/docs/appearance/structured-data/intro-structured-data
 * @link https://schema.org
 *
 * @package Sovereignty
 */
class Schema {

	/**
	 * Output JSON-LD structured data in <head>.
	 *
	 * Hooked to wp_head at priority 99.
	 *
	 * @return void
	 */
	public static function output(): void {
		if ( \defined( 'WPSEO_VERSION' ) ) {
			return;
		}

		if ( is_404() ) {
			return;
		}

		$graph = self::build_graph();

		if ( $graph === [] ) {
			return;
		}

		/**
		 * Filters the complete JSON-LD graph before output.
		 *
		 * @param array $graph The JSON-LD @graph array.
		 *
		 * @return array The filtered graph.
		 */
		$graph = apply_filters( 'sovereignty_schema', $graph );

		$data = [
			'@context' => 'https://schema.org',
			'@graph'   => $graph,
		];

		$json = wp_json_encode( $data, \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE );

		if ( $json === false ) {
			return;
		}

		echo '<script type="application/ld+json">' . $json . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON-LD is safe, values are escaped during build.
	}

	/**
	 * Build the @graph array based on the current query context.
	 *
	 * @return array[] Array of JSON-LD graph pieces.
	 */
	private static function build_graph(): array {
		$graph = [];

		$graph[] = self::build_website();
		$graph[] = self::build_publisher();

		if ( is_singular() ) {
			$post = get_post(); // phpcs:ignore Apermo.WordPress.ImplicitPostFunction -- Called from wp_head hook, no $post parameter available.

			if ( $post instanceof WP_Post ) {
				if ( is_page() ) {
					$graph[] = self::build_page( $post );
				} elseif ( is_attachment() && wp_attachment_is_image( $post ) ) {
					$graph[] = self::build_image_object( $post );
				} else {
					$graph[] = self::build_single( $post );
				}
			}
		} elseif ( ! is_404() ) {
			$graph[] = self::build_archive();
		}

		return $graph;
	}

	/**
	 * Build the WebSite piece with SearchAction.
	 *
	 * @return array
	 */
	private static function build_website(): array {
		return [
			'@type'           => 'WebSite',
			'@id'             => home_url( '/#website' ),
			'url'             => home_url( '/' ),
			'name'            => get_bloginfo( 'name' ),
			'description'     => get_bloginfo( 'description' ),
			'potentialAction' => [
				'@type'       => 'SearchAction',
				'target'      => [
					'@type'       => 'EntryPoint',
					'urlTemplate' => home_url( '/?s={search_term_string}' ),
				],
				'query-input' => 'required name=search_term_string',
			],
		];
	}

	/**
	 * Build the publisher Organization piece.
	 *
	 * @return array
	 */
	private static function build_publisher(): array {
		$publisher = [
			'@type' => 'Organization',
			'@id'   => home_url( '/#organization' ),
			'name'  => get_bloginfo( 'name' ),
			'url'   => home_url( '/' ),
		];

		if ( has_custom_logo() ) {
			$image = wp_get_attachment_image_src( (int) get_theme_mod( 'custom_logo' ), 'full' );

			if ( \is_array( $image ) ) {
				$publisher['logo'] = [
					'@type'  => 'ImageObject',
					'url'    => $image[0],
					'width'  => $image[1],
					'height' => $image[2],
				];
			}
		}

		return $publisher;
	}

	/**
	 * Build a BlogPosting piece for a single post.
	 *
	 * @param WP_Post $post The post object.
	 *
	 * @return array
	 */
	private static function build_single( WP_Post $post ): array { // phpcs:ignore SlevomatCodingStandard.Functions.FunctionLength.FunctionLength -- Building a complete schema piece.
		$schema_type = self::get_schema_type( 'sovereignty.schema.single' );

		$data = [
			'@type'            => $schema_type,
			'@id'              => get_permalink( $post ),
			'mainEntityOfPage' => [
				'@id' => get_permalink( $post ),
			],
			'headline'         => get_the_title( $post ),
			'datePublished'    => get_the_date( 'c', $post ),
			'dateModified'     => get_the_modified_date( 'c', $post ),
			'author'           => self::build_person( (int) $post->post_author ),
			'publisher'        => [ '@id' => home_url( '/#organization' ) ],
			'timeRequired'     => \sprintf( 'PT%dM', Tags::get_reading_time( $post ) ),
		];

		$excerpt = get_the_excerpt( $post );
		if ( $excerpt !== '' ) {
			$data['description'] = $excerpt;
		}

		if ( has_post_thumbnail( $post ) ) {
			$image = wp_get_attachment_image_url( get_post_thumbnail_id( $post ), 'full' );
			if ( $image !== false ) {
				$data['image'] = $image;
			}
		}

		$tags = get_the_tags( $post );
		if ( \is_array( $tags ) ) {
			$data['keywords'] = wp_list_pluck( $tags, 'name' );
		}

		if ( (int) get_comments_number( $post ) > 0 ) {
			$comments = get_comments(
				[
					'post_id' => $post->ID,
					'status'  => 'approve',
					'number'  => 100,
				],
			);

			if ( $comments !== [] ) {
				$data['comment'] = \array_map( [ self::class, 'build_comment' ], $comments );
			}
		}

		return $data;
	}

	/**
	 * Build a WebPage piece for a page.
	 *
	 * @param WP_Post $post The page object.
	 *
	 * @return array
	 */
	private static function build_page( WP_Post $post ): array {
		$schema_type = self::get_schema_type( 'sovereignty.schema.page' );

		$data = [
			'@type'            => $schema_type,
			'@id'              => get_permalink( $post ),
			'mainEntityOfPage' => [
				'@id' => get_permalink( $post ),
			],
			'name'             => get_the_title( $post ),
			'datePublished'    => get_the_date( 'c', $post ),
			'dateModified'     => get_the_modified_date( 'c', $post ),
			'publisher'        => [ '@id' => home_url( '/#organization' ) ],
		];

		$excerpt = get_the_excerpt( $post );
		if ( $excerpt !== '' ) {
			$data['description'] = $excerpt;
		}

		return $data;
	}

	/**
	 * Build an archive/search/author page piece.
	 *
	 * @return array
	 */
	private static function build_archive(): array {
		if ( is_search() ) {
			$schema_type = self::get_schema_type( 'sovereignty.schema.search' );
		} elseif ( is_author() ) {
			$schema_type = self::get_schema_type( 'sovereignty.schema.author' );
		} else {
			$schema_type = self::get_schema_type( 'sovereignty.schema.archive' );
		}

		$data = [
			'@type' => $schema_type,
			'@id'   => get_self_link(),
			'url'   => get_self_link(),
		];

		$name = wp_get_document_title();
		if ( $name !== '' ) {
			$data['name'] = $name;
		}

		$description = get_the_archive_description();
		if ( $description !== '' ) {
			$data['description'] = wp_strip_all_tags( $description );
		}

		return $data;
	}

	/**
	 * Build an ImageObject piece for image attachments.
	 *
	 * @param WP_Post $post The attachment post object.
	 *
	 * @return array
	 */
	private static function build_image_object( WP_Post $post ): array {
		$data = [
			'@type' => 'ImageObject',
			'@id'   => get_permalink( $post ),
			'url'   => wp_get_attachment_url( $post->ID ),
			'name'  => get_the_title( $post ),
		];

		$caption = wp_get_attachment_caption( $post->ID );
		if ( $caption !== '' ) {
			$data['caption'] = $caption;
		}

		$metadata = wp_get_attachment_metadata( $post->ID );
		if ( \is_array( $metadata ) ) {
			$data['width']  = $metadata['width'];
			$data['height'] = $metadata['height'];
		}

		return $data;
	}

	/**
	 * Build a Person piece.
	 *
	 * @param int $author_id The author user ID.
	 *
	 * @return array
	 */
	public static function build_person( int $author_id ): array {
		return [
			'@type' => 'Person',
			'name'  => get_the_author_meta( 'display_name', $author_id ),
			'url'   => get_author_posts_url( $author_id ),
		];
	}

	/**
	 * Build a Comment piece.
	 *
	 * @param WP_Comment $comment The comment object.
	 *
	 * @return array
	 */
	public static function build_comment( WP_Comment $comment ): array {
		return [
			'@type'       => 'Comment',
			'text'        => wp_strip_all_tags( $comment->comment_content ),
			'dateCreated' => get_comment_date( 'c', $comment ),
			'author'      => [
				'@type' => 'Person',
				'name'  => $comment->comment_author,
			],
		];
	}

	/**
	 * Get Schema.org type(s) from theme config.
	 *
	 * Returns a string for single types, an array for multiple.
	 *
	 * @param string $config_key Dot-notation config key (e.g. 'sovereignty.schema.single').
	 *
	 * @return string|string[]
	 */
	private static function get_schema_type( string $config_key ): string|array {
		$types = Config::array( $config_key );

		$short = \array_map(
			static fn ( string $url ): string => \str_replace( 'https://schema.org/', '', $url ),
			$types,
		);

		if ( \count( $short ) === 1 ) {
			return $short[0];
		}

		return $short;
	}
}
