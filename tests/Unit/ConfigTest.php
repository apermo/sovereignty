<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Tests\Unit;

use Apermo\Sovereignty\Config;
use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Config class.
 */
#[CoversClass( Config::class )]
class ConfigTest extends TestCase {

	/**
	 * Set up Brain Monkey and load fixture before each test.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
		Config::reset();

		// Point get_template_directory to the repo root where theme.json lives.
		Functions\stubs(
			[
				'get_template_directory' => \dirname( __DIR__, 2 ),
				'apply_filters'          => static fn ( $hook, $val ) => $val,
			],
		);
	}

	/**
	 * Tear down Brain Monkey after each test.
	 *
	 * @return void
	 */
	protected function tearDown(): void {
		Config::reset();
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Verify get returns a value by dot-notation path.
	 *
	 * @return void
	 */
	public function test_get_returns_value_by_dot_path(): void {
		$this->assertSame( 900, Config::get( 'sovereignty.embed.width' ) );
	}

	/**
	 * Verify get returns default for a missing key.
	 *
	 * @return void
	 */
	public function test_get_returns_default_for_missing_key(): void {
		$this->assertSame( 'fallback', Config::get( 'sovereignty.nonexistent.key', 'fallback' ) );
	}

	/**
	 * Verify get returns null for a missing key without default.
	 *
	 * @return void
	 */
	public function test_get_returns_null_for_missing_key(): void {
		$this->assertNull( Config::get( 'sovereignty.nonexistent' ) );
	}

	/**
	 * Verify int returns an integer value.
	 *
	 * @return void
	 */
	public function test_int_returns_integer(): void {
		$this->assertSame( 200, Config::int( 'sovereignty.reading.wordsPerMinute' ) );
	}

	/**
	 * Verify string returns a string value.
	 *
	 * @return void
	 */
	public function test_string_returns_string(): void {
		$this->assertSame( '800px', Config::string( 'sovereignty.layout.breakpoints.narrow' ) );
	}

	/**
	 * Verify array returns an array value.
	 *
	 * @return void
	 */
	public function test_array_returns_array(): void {
		$formats = Config::array( 'sovereignty.postFormats' );

		$this->assertIsArray( $formats );
		$this->assertContains( 'aside', $formats );
		$this->assertContains( 'quote', $formats );
	}

	/**
	 * Verify bool returns a boolean value.
	 *
	 * @return void
	 */
	public function test_bool_returns_boolean(): void {
		$this->assertFalse( Config::bool( 'sovereignty.excerpt.enabled' ) );
	}

	/**
	 * Verify WP-standard settings are accessible.
	 *
	 * @return void
	 */
	public function test_wp_settings_accessible(): void {
		$this->assertSame( '900px', Config::string( 'settings.layout.wideSize' ) );
	}

	/**
	 * Verify nested sovereignty values work.
	 *
	 * @return void
	 */
	public function test_deeply_nested_value(): void {
		$this->assertSame( '#eeeeee', Config::string( 'sovereignty.pwa.themeColor.light' ) );
	}

	/**
	 * Verify sovereignty_config filter is applied.
	 *
	 * @return void
	 */
	public function test_sovereignty_config_filter_applied(): void {
		Config::reset();

		Functions\stubs(
			[
				'get_template_directory' => \dirname( __DIR__, 2 ),
				'apply_filters'          => static function ( $hook, $val ) {
					if ( $hook === 'sovereignty_config' ) {
						$val['sovereignty']['embed']['width'] = 1200;
					}
					return $val;
				},
			],
		);

		$this->assertSame( 1200, Config::int( 'sovereignty.embed.width' ) );
	}

	/**
	 * Verify reset clears the cache.
	 *
	 * @return void
	 */
	public function test_reset_clears_cache(): void {
		// Access a value to populate cache.
		Config::get( 'sovereignty.embed.width' );

		// Reset and change the filter.
		Config::reset();

		Functions\stubs(
			[
				'get_template_directory' => \dirname( __DIR__, 2 ),
				'apply_filters'          => static function ( $hook, $val ) {
					if ( $hook === 'sovereignty_config' ) {
						$val['sovereignty']['embed']['width'] = 999;
					}
					return $val;
				},
			],
		);

		$this->assertSame( 999, Config::int( 'sovereignty.embed.width' ) );
	}

	/**
	 * Verify sidebar definitions are readable as arrays.
	 *
	 * @return void
	 */
	public function test_sidebars_readable(): void {
		$sidebars = Config::array( 'sovereignty.sidebars' );

		$this->assertCount( 4, $sidebars );
		$this->assertSame( 'sidebar-1', $sidebars[0]['id'] );
		$this->assertSame( 'entry-meta', $sidebars[3]['id'] );
	}
}
