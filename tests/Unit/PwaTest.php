<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Tests\Unit;

use Apermo\Sovereignty\PWA;
use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the PWA class.
 */
#[CoversClass( Apermo\Sovereignty\PWA::class )]
class PwaTest extends TestCase {

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
	 * Verify that manifest_query_var adds the sovereignty_manifest query variable.
	 */
	public function test_manifest_query_var_adds_sovereignty_manifest(): void {
		$vars   = [ 'existing_var' ];
		$result = PWA::manifest_query_var( $vars );

		$this->assertContains( 'sovereignty_manifest', $result );
		$this->assertContains( 'existing_var', $result );
	}

	/**
	 * Verify that PWA head outputs nothing when a site icon is already set.
	 */
	public function test_pwa_head_skips_when_site_icon_set(): void {
		Functions\expect( 'has_site_icon' )->once()->andReturn( true );

		\ob_start();
		PWA::head();
		$output = \ob_get_clean();

		$this->assertSame( '', $output );
	}

	/**
	 * Verify that PWA head outputs manifest, icon, and theme-color links when no site icon is set.
	 */
	public function test_pwa_head_outputs_links_when_no_site_icon(): void {
		Functions\expect( 'has_site_icon' )->once()->andReturn( false );
		Functions\expect( 'get_template_directory_uri' )->once()->andReturn( 'https://example.com/theme' );
		Functions\expect( 'home_url' )->once()->andReturn( 'https://example.com/?sovereignty_manifest=1' );
		Functions\stubs( [ 'esc_url' => static fn ( $url ) => $url ] );

		\ob_start();
		PWA::head();
		$output = \ob_get_clean();

		$this->assertStringContainsString( 'rel="manifest"', $output );
		$this->assertStringContainsString( 'rel="icon"', $output );
		$this->assertStringContainsString( 'rel="apple-touch-icon"', $output );
		$this->assertStringContainsString( 'theme-color', $output );
	}
}
