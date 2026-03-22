<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Tests\Unit;

use Apermo\Sovereignty\Config;
use Apermo\Sovereignty\Login;
use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Login class.
 */
#[CoversClass( Apermo\Sovereignty\Login::class )]
class LoginTest extends TestCase {

	/**
	 * Set up Brain Monkey before each test.
	 */
	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
		Config::reset();

		Functions\stubs(
			[
				'get_template_directory' => \dirname( __DIR__, 2 ),
				'apply_filters'          => static fn ( $hook, $value ) => $value,
			],
		);
	}

	/**
	 * Tear down Brain Monkey after each test.
	 */
	protected function tearDown(): void {
		Config::reset();
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Verify that logo outputs nothing when no site icon is configured.
	 */
	public function test_logo_outputs_nothing_without_site_icon(): void {
		Functions\expect( 'has_site_icon' )->once()->andReturn( false );

		\ob_start();
		Login::logo();
		$output = \ob_get_clean();

		$this->assertSame( '', $output );
	}

	/**
	 * Verify that logo outputs a background-image style when a site icon is available.
	 */
	public function test_logo_outputs_style_with_site_icon(): void {
		Functions\expect( 'has_site_icon' )->once()->andReturn( true );
		Functions\expect( 'get_site_icon_url' )->once()->with( 84 )->andReturn( 'https://example.com/icon.png' );
		Functions\stubs( [ 'esc_url' => static fn ( $url ) => $url ] );

		\ob_start();
		Login::logo();
		$output = \ob_get_clean();

		$this->assertStringContainsString( 'background-image', $output );
		$this->assertStringContainsString( 'icon.png', $output );
	}
}
