<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Tests\Unit;

use Apermo\Sovereignty\Head;
use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Head class.
 */
#[CoversClass( Apermo\Sovereignty\Head::class )]
class HeadTest extends TestCase {

	/**
	 * Set up Brain Monkey before each test.
	 */
	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
	}

	/**
	 * Tear down Brain Monkey after each test.
	 */
	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Verify that pingback outputs a link element on singular pages with pings open.
	 */
	public function test_pingback_outputs_link_on_singular(): void {
		Functions\expect( 'is_singular' )->once()->andReturn( true );
		Functions\expect( 'pings_open' )->once()->andReturn( true );
		Functions\expect( 'get_bloginfo' )->once()->with( 'pingback_url' )->andReturn( 'https://example.com/xmlrpc.php' );
		Functions\stubs( [ 'esc_url' => static fn ( $url ) => $url ] );

		\ob_start();
		Head::pingback();
		$output = \ob_get_clean();

		$this->assertStringContainsString( 'rel="pingback"', $output );
	}

	/**
	 * Verify that pingback outputs nothing on non-singular pages.
	 */
	public function test_pingback_outputs_nothing_when_not_singular(): void {
		Functions\expect( 'is_singular' )->once()->andReturn( false );

		\ob_start();
		Head::pingback();
		$output = \ob_get_clean();

		$this->assertSame( '', $output );
	}

	/**
	 * Verify that color_scheme_meta outputs a meta tag for supported color schemes.
	 */
	public function test_color_scheme_meta_outputs_meta_tag(): void {
		\ob_start();
		Head::color_scheme_meta();
		$output = \ob_get_clean();

		$this->assertStringContainsString( 'supported-color-schemes', $output );
		$this->assertStringContainsString( 'light dark', $output );
	}
}
