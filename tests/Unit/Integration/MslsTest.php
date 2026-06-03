<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Tests\Unit\Integration;

use Apermo\Sovereignty\Integration\Msls;
use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Msls integration class.
 */
#[CoversClass( Msls::class )]
class MslsTest extends TestCase {

	/**
	 * Set up Brain Monkey before each test.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
		Functions\when( 'esc_attr__' )->returnArg();
	}

	/**
	 * Tear down Brain Monkey after each test.
	 *
	 * @return void
	 */
	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Verify register() hooks the switcher into the search action.
	 *
	 * @return void
	 */
	public function test_register_adds_hook(): void {
		Functions\expect( 'add_action' )->once()->with( 'sovereignty_after_search', [ Msls::class, 'display' ] );

		Msls::register();
	}

	/**
	 * Verify display() wraps the switcher markup in a labelled nav.
	 *
	 * @return void
	 */
	public function test_display_wraps_switcher_when_present(): void {
		Functions\when( 'msls_get_switcher' )->justReturn( '<a href="https://example.tld/de/">DE</a>' );

		$this->expectOutputString(
			'<nav class="language-switcher" aria-label="Languages"><a href="https://example.tld/de/">DE</a></nav>',
		);

		Msls::display();
	}

	/**
	 * Verify display() renders nothing when no translation exists.
	 *
	 * @return void
	 */
	public function test_display_renders_nothing_without_translation(): void {
		Functions\when( 'msls_get_switcher' )->justReturn( '' );

		$this->expectOutputString( '' );

		Msls::display();
	}

	/**
	 * Verify display() renders nothing when the switcher is not a string.
	 *
	 * @return void
	 */
	public function test_display_renders_nothing_when_not_a_string(): void {
		Functions\when( 'msls_get_switcher' )->justReturn( false );

		$this->expectOutputString( '' );

		Msls::display();
	}
}
