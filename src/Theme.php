<?php

declare(strict_types=1);

namespace Apermo\Sovereignty;

/**
 * Theme bootstrap: registers all hooks, grouped by concern.
 *
 * @package Sovereignty
 */
class Theme {

	/**
	 * Initialize all theme hooks.
	 *
	 * @return void
	 */
	public static function init(): void {
		self::init_setup();
		self::init_assets();
		self::init_head();
		self::init_semantics();
		self::init_featured_image();
		self::init_feed();
		self::init_compat();
		self::init_webactions();
		self::init_pwa();
		self::init_widgets();
		self::init_integrations();
	}

	/**
	 * Theme setup and navigation.
	 *
	 * @return void
	 */
	private static function init_setup(): void {
		add_action( 'after_setup_theme', [ Setup::class, 'setup' ] );
		add_action( 'after_setup_theme', [ Embed::class, 'content_width' ], 0 );
		add_filter( 'embed_defaults', [ Embed::class, 'defaults' ] );
		add_filter( 'oembed_fetch_url', [ Embed::class, 'fetch_url' ], 99 );
		add_filter( 'wp_page_menu_args', [ Setup::class, 'page_menu_args' ] );
	}

	/**
	 * Scripts, styles, and login customization.
	 *
	 * @return void
	 */
	private static function init_assets(): void {
		add_action( 'wp_enqueue_scripts', [ Assets::class, 'enqueue' ] );
		add_action( 'login_enqueue_scripts', [ Login::class, 'logo' ] );
	}

	/**
	 * Head output: pingback, publisher feed, color-scheme meta.
	 *
	 * @return void
	 */
	private static function init_head(): void {
		add_action( 'wp_head', [ Head::class, 'pingback' ] );
		add_action( 'wp_head', [ Head::class, 'publisher_feed' ] );
		add_action( 'wp_head', [ Head::class, 'color_scheme_meta' ] );
	}

	/**
	 * Microformats2, microdata, and semantic markup.
	 *
	 * @return void
	 */
	private static function init_semantics(): void {
		add_filter( 'body_class', [ Semantics::class, 'body_classes' ] );
		add_filter( 'post_class', [ Semantics::class, 'post_classes' ], 99 );
		add_filter( 'comment_class', [ Semantics::class, 'comment_classes' ], 99 );
		add_filter( 'get_comment_author_link', [ Semantics::class, 'author_link' ] );
		add_filter( 'pre_get_avatar_data', [ Semantics::class, 'pre_get_avatar_data' ], 99, 2 );
		add_filter( 'previous_image_link', [ Semantics::class, 'semantic_previous_image_link' ] );
		add_filter( 'next_image_link', [ Semantics::class, 'semantic_next_image_link' ] );
		add_filter( 'next_posts_link_attributes', [ Semantics::class, 'next_posts_link_attributes' ] );
		add_filter( 'previous_posts_link_attributes', [ Semantics::class, 'previous_posts_link_attributes' ] );
		add_filter( 'get_search_form', [ Semantics::class, 'get_search_form' ] );
		add_filter( 'term_links-post_tag', [ Semantics::class, 'term_links_tag' ] );
	}

	/**
	 * Featured image: thumbnails, post covers, block editor.
	 *
	 * @return void
	 */
	private static function init_featured_image(): void {
		add_filter( 'the_content', [ Featured_Image::class, 'content_post_thumbnail' ] );
		add_filter( 'admin_post_thumbnail_html', [ Featured_Image::class, 'admin_thumbnail_html' ], 10, 2 );
		add_action( 'save_post', [ Featured_Image::class, 'save_post' ], 5, 1 );
		add_action( 'wp_enqueue_scripts', [ Featured_Image::class, 'enqueue_scripts' ] );
		add_filter( 'post_class', [ Featured_Image::class, 'post_class' ] );
		add_action( 'init', [ Featured_Image::class, 'register_meta' ] );
		add_action( 'enqueue_block_editor_assets', [ Featured_Image::class, 'enqueue_block_editor_assets' ], 9 );
	}

	/**
	 * Feed discovery and post-format feed links.
	 *
	 * @return void
	 */
	private static function init_feed(): void {
		add_action( 'wp_head', [ Feed::class, 'extend_singular_feed_discovery' ] );
	}

	/**
	 * Backwards compatibility: comment form, lazy loading, post format query.
	 *
	 * @return void
	 */
	private static function init_compat(): void {
		add_filter( 'comment_form_default_fields', [ Compat::class, 'comment_autocomplete' ] );
		add_filter( 'comment_form_field_comment', [ Compat::class, 'comment_field_input_type' ] );
		add_action( 'pre_get_posts', [ Compat::class, 'query_format_standard' ] );
		add_filter( 'the_content', [ Compat::class, 'add_lazy_loading' ], 99 );
		add_filter( 'wp_lazy_loading_enabled', [ Compat::class, 'disable_native_lazy_loading' ], 20, 3 );
	}

	/**
	 * IndieWeb web actions on comment links and forms.
	 *
	 * @return void
	 */
	private static function init_webactions(): void {
		add_filter( 'comment_reply_link', [ Webactions::class, 'comment_reply_link' ], 10, 4 );
		add_action( 'comment_form_before', [ Webactions::class, 'comment_form_before' ], 0 );
		add_action( 'comment_form_after', [ Webactions::class, 'comment_form_after' ], 0 );
	}

	/**
	 * PWA: web manifest, favicons, app icons.
	 *
	 * @return void
	 */
	private static function init_pwa(): void {
		add_filter( 'query_vars', [ PWA::class, 'manifest_query_var' ] );
		add_action( 'template_redirect', [ PWA::class, 'manifest_template_redirect' ] );
		add_action( 'wp_head', [ PWA::class, 'head' ] );
	}

	/**
	 * Sidebars, custom widgets, starter content, activation defaults.
	 *
	 * @return void
	 */
	private static function init_widgets(): void {
		add_action( 'widgets_init', [ Widgets::class, 'widgets_init' ] );
		add_filter( 'get_theme_starter_content', [ Widgets::class, 'starter_content_add_widget' ], 10, 2 );
		add_action( 'after_switch_theme', [ Widgets::class, 'activate' ] );
	}

	/**
	 * Conditional plugin integrations.
	 *
	 * @return void
	 */
	private static function init_integrations(): void {
		if ( \defined( 'SYNDICATION_LINKS_VERSION' ) ) {
			Integration\Syndication_Links::register();
		}

		if ( \class_exists( 'Post_Kinds_Plugin' ) ) {
			Integration\Post_Kinds::register();
		}

		if ( \class_exists( '\Activitypub\Activitypub' ) ) {
			Integration\ActivityPub::register();
		}
	}
}
