<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals, PSR1.Classes.ClassDeclaration, Universal.Files.SeparateFunctionsFromOO, Squiz.Commenting, PSR1.Files.SideEffects, Generic.Files.OneObjectStructurePerFile
if ( ! class_exists( 'WP_Post' ) ) {
	class WP_Post {

		public int $ID = 0;

		public int $post_author = 0;

		public string $post_content = '';

		public string $post_type = 'post';
	}
}

if ( ! class_exists( 'WP_Comment' ) ) {
	class WP_Comment {

		public int $comment_ID = 0;

		public string $comment_type = '';

		public string $comment_approved = '1';
	}
}
// Stub WP functions that use named parameters (Brain Monkey mocks don't support named params).
if ( ! function_exists( 'get_the_content' ) ) {
	// phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint, SlevomatCodingStandard.TypeHints.ReturnTypeHint -- Must match WP core's untyped signature.
	function get_the_content( $more_link_text = null, $strip_teaser = false, $post = null ) {
		return $GLOBALS['_test_get_the_content'] ?? '';
	}
}
// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals, PSR1.Classes.ClassDeclaration, Squiz.Commenting, PSR1.Files.SideEffects, Generic.Files.OneObjectStructurePerFile

$wp_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( $wp_tests_dir === false ) {
	$vendor_dir = dirname( __DIR__ ) . '/vendor/wp-phpunit/wp-phpunit';
	if ( is_dir( $vendor_dir ) ) {
		$wp_tests_dir = $vendor_dir;
	}
}

if ( $wp_tests_dir !== false && is_dir( $wp_tests_dir ) ) {
	if ( getenv( 'WP_MULTISITE' ) ) {
		define( 'WP_TESTS_MULTISITE', true );
	}

	require_once $wp_tests_dir . '/includes/functions.php';

	tests_add_filter( 'muplugins_loaded', 'sovereignty_tests_load_theme' );

	require_once $wp_tests_dir . '/includes/bootstrap.php';
}

/**
 * Load the theme under test.
 *
 * @return void
 */
function sovereignty_tests_load_theme(): void {
	register_theme_directory( dirname( __DIR__, 2 ) );
	switch_theme( basename( dirname( __DIR__ ) ) );
}
