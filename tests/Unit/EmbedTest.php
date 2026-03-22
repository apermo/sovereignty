<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Tests\Unit;

use Apermo\Sovereignty\Config;
use Apermo\Sovereignty\Embed;
use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Embed class.
 */
#[CoversClass( Embed::class )]
class EmbedTest extends TestCase {

	/**
	 * Set up Brain Monkey before each test.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
		Config::reset();

		Functions\stubs(
			[
				'get_template_directory' => \dirname( __DIR__, 2 ),
			],
		);
	}

	/**
	 * Tear down Brain Monkey and clean up globals after each test.
	 *
	 * @return void
	 */
	protected function tearDown(): void {
		unset( $GLOBALS['content_width'] );
		Config::reset();
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Verify content_width sets the global via sovereignty_content_width filter.
	 *
	 * @return void
	 */
	public function test_content_width_sets_global(): void {
		Functions\stubs( [ 'apply_filters' => static fn ( $hook, $value ) => $value ] );

		Embed::content_width();

		$this->assertSame( 900, $GLOBALS['content_width'] );
	}

	/**
	 * Verify defaults returns 900x600 dimensions.
	 *
	 * @return void
	 */
	public function test_defaults_returns_dimensions(): void {
		Functions\stubs( [ 'apply_filters' => static fn ( $hook, $value ) => $value ] );

		$result = Embed::defaults();

		$this->assertSame( 900, $result['width'] );
		$this->assertSame( 600, $result['height'] );
	}

	/**
	 * Verify fetch_url appends width and height query args.
	 *
	 * @return void
	 */
	public function test_fetch_url_adds_dimensions(): void {
		Functions\stubs( [ 'apply_filters' => static fn ( $hook, $value ) => $value ] );
		Functions\expect( 'add_query_arg' )
			->twice()
			->andReturnUsing( static fn ( $key, $value, $url ) => $url . "&{$key}={$value}" );

		$result = Embed::fetch_url( 'https://provider.com/oembed' );

		$this->assertStringContainsString( 'width=900', $result );
		$this->assertStringContainsString( 'height=600', $result );
	}
}
