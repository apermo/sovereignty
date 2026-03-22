<?php

declare(strict_types=1);

namespace Apermo\Sovereignty;

use Apermo\Sovereignty\Widget\Author;
use Apermo\Sovereignty\Widget\Taxonomy;

/**
 * Widget and sidebar registration.
 *
 * @package Sovereignty
 */
class Widgets {

	/**
	 * Register custom widgets and sidebars.
	 *
	 * @return void
	 */
	public static function widgets_init(): void {
		register_widget( Author::class );
		register_widget( Taxonomy::class );

		foreach ( Config::array( 'sovereignty.sidebars' ) as $sidebar ) {
			register_sidebar(
				[
					'name'          => $sidebar['name'],
					'id'            => $sidebar['id'],
					'description'   => $sidebar['description'],
					'before_widget' => $sidebar['beforeWidget'],
					'after_widget'  => $sidebar['afterWidget'],
					'before_title'  => $sidebar['beforeTitle'],
					'after_title'   => $sidebar['afterTitle'],
				],
			);
		}
	}

	/**
	 * Add entry-meta default widgets to starter content.
	 *
	 * @param array $content Array of starter content.
	 * @param array $config  Array of theme-specific starter content configuration.
	 *
	 * @return array Filtered starter content.
	 */
	public static function starter_content_add_widget( array $content, array $config ): array {
		if ( ! isset( $content['widgets']['entry-meta'] ) ) {
			$content['widgets']['entry-meta'] = [];
		}

		$content['widgets']['entry-meta'][] = [
			'sovereignty-author',
			[],
		];
		$content['widgets']['entry-meta'][] = [
			'sovereignty-taxonomy',
			[],
		];

		return $content;
	}

	/**
	 * Set up default widgets for the theme on activation.
	 *
	 * @return void
	 */
	public static function activate(): void {
		update_option(
			'widget_sovereignty-author',
			[
				2              => [ 'title' => '' ],
				'_multiwidget' => 1,
			],
		);

		update_option(
			'widget_sovereignty-taxonomy',
			[
				2              => [ 'title' => '' ],
				'_multiwidget' => 1,
			],
		);

		update_option(
			'sidebars_widgets',
			[
				'wp_inactive_widgets' => [],
				'sidebar-1'           => [
					0 => 'search-2',
					1 => 'recent-posts-2',
					2 => 'recent-comments-2',
				],
				'sidebar-2'           => [
					0 => 'archives-2',
				],
				'sidebar-3'           => [
					0 => 'categories-2',
					1 => 'meta-2',
				],
				'entry-meta'          => [
					0 => 'sovereignty-author-2',
					1 => 'sovereignty-taxonomy-2',
				],
				'array_version'       => 3,
			],
		);
	}
}
