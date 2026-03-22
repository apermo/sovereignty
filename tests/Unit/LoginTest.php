<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Tests\Unit;

use Apermo\Sovereignty\Login;
use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
	}

	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

	public function test_logo_outputs_nothing_without_site_icon(): void {
		Functions\expect( 'has_site_icon' )->once()->andReturn( false );

		$this->expectOutputString( '' );
		Login::logo();
	}

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
