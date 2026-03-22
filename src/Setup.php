<?php

declare(strict_types=1);

namespace Apermo\Sovereignty;

/**
 * Theme setup: theme supports, nav menus, starter content.
 *
 * @package Sovereignty
 */
class Setup {

	/**
	 * Set up theme defaults and register support for WordPress features.
	 *
	 * @return void
	 */
	public static function setup(): void { // phpcs:ignore SlevomatCodingStandard.Functions.FunctionLength.FunctionLength -- Theme setup is inherently long.
		\defined( 'SOVEREIGNTY_EXCERPT' ) || \define( 'SOVEREIGNTY_EXCERPT', false );
		\defined( 'SOVEREIGNTY_EXCERPT_COUNT' ) || \define( 'SOVEREIGNTY_EXCERPT_COUNT', 100 );

		$content_width = 900;

		load_theme_textdomain( 'sovereignty', get_template_directory() . '/languages' );

		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'post-thumbnails' );

		set_post_thumbnail_size( $content_width, 9999 );
		add_image_size( 'sovereignty-image-post', $content_width, 1250 );

		add_theme_support(
			'html5',
			[
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'widgets',
				'script',
			],
		);

		add_theme_support( 'align-wide' );

		add_theme_support(
			'editor-color-palette',
			[
				[
					'name'  => __( 'Blue', 'sovereignty' ),
					'slug'  => 'blue',
					'color' => '#0073aa',
				],
				[
					'name'  => __( 'Lighter blue', 'sovereignty' ),
					'slug'  => 'lighter-blue',
					'color' => '#229fd8',
				],
				[
					'name'  => __( 'Blue jeans', 'sovereignty' ),
					'slug'  => 'blue-jeans',
					'color' => '#5bc0eb',
				],
				[
					'name'  => __( 'Orioles orange', 'sovereignty' ),
					'slug'  => 'orioles-orange',
					'color' => '#fa5b0f',
				],
				[
					'name'  => __( 'USC gold', 'sovereignty' ),
					'slug'  => 'usc-gold',
					'color' => '#ffcc00',
				],
				[
					'name'  => __( 'Gargoyle gas', 'sovereignty' ),
					'slug'  => 'gargoyle-gas',
					'color' => '#fde74c',
				],
				[
					'name'  => __( 'Yellow', 'sovereignty' ),
					'slug'  => 'yellow',
					'color' => '#fff9c0',
				],
				[
					'name'  => __( 'Android green', 'sovereignty' ),
					'slug'  => 'android-green',
					'color' => '#9bc53d',
				],
				[
					'name'  => __( 'White', 'sovereignty' ),
					'slug'  => 'white',
					'color' => '#fff',
				],
				[
					'name'  => __( 'Very light gray', 'sovereignty' ),
					'slug'  => 'very-light-gray',
					'color' => '#eee',
				],
				[
					'name'  => __( 'Very dark gray', 'sovereignty' ),
					'slug'  => 'very-dark-gray',
					'color' => '#444',
				],
			],
		);

		register_nav_menus(
			[
				'primary' => __( 'Primary Menu', 'sovereignty' ),
			],
		);

		add_theme_support(
			'post-formats',
			[
				'aside',
				'gallery',
				'link',
				'status',
				'image',
				'video',
				'audio',
				'quote',
				'chat',
			],
		);

		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'title-tag' );

		add_theme_support(
			'custom-logo',
			[
				'height' => 30,
				'width'  => 30,
			],
		);

		add_theme_support(
			'custom-header',
			[
				'width'       => 1250,
				'height'      => 600,
				'header-text' => true,
			],
		);

		add_theme_support( 'microformats2' );
		add_theme_support( 'microformats' );
		add_theme_support( 'microdata' );
		add_theme_support( 'indieweb' );
		add_theme_support( 'service_worker', true );

		add_theme_support(
			'starter-content',
			[
				'widgets' => [
					'sidebar-1' => [ 'text_business_info', 'search', 'text_about' ],
					'sidebar-2' => [ 'text_business_info' ],
					'sidebar-3' => [ 'text_about', 'search' ],
					'entry-meta' => [],
				],
				'posts' => [
					'home',
					'about'            => [ 'thumbnail' => '{{image-sea}}' ],
					'contact'          => [ 'thumbnail' => '{{image-lights}}' ],
					'blog'             => [ 'thumbnail' => '{{image-beach}}' ],
					'homepage-section' => [ 'thumbnail' => '{{image-lights}}' ],
				],
				'attachments' => [
					'image-beach'  => [
						'post_title' => _x( 'Beach', 'Theme starter content', 'sovereignty' ),
						'file'       => 'assets/images/beach.jpeg',
					],
					'image-sea'    => [
						'post_title' => _x( 'Sea', 'Theme starter content', 'sovereignty' ),
						'file'       => 'assets/images/sea.jpeg',
					],
					'image-lights' => [
						'post_title' => _x( 'Lights', 'Theme starter content', 'sovereignty' ),
						'file'       => 'assets/images/lights.jpeg',
					],
				],
				'options' => [
					'show_on_front'  => 'page',
					'page_on_front'  => '{{home}}',
					'page_for_posts' => '{{blog}}',
					'header_image'   => get_theme_file_uri( 'assets/images/beach.jpeg' ),
				],
				'theme_mods' => [
					'panel_1' => '{{homepage-section}}',
					'panel_2' => '{{about}}',
					'panel_3' => '{{blog}}',
					'panel_4' => '{{contact}}',
				],
				'nav_menus' => [
					'primary' => [
						'name'  => __( 'Top Menu', 'sovereignty' ),
						'items' => [ 'page_home', 'page_about', 'page_blog', 'page_contact' ],
					],
				],
			],
		);
	}

	/**
	 * Show a home link in wp_page_menu fallback.
	 *
	 * @param array $args Page menu arguments.
	 *
	 * @return array
	 */
	public static function page_menu_args( array $args ): array {
		$args['show_home'] = true;

		return $args;
	}
}
