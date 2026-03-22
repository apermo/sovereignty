<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Tests\Unit;

use Apermo\Sovereignty\Tombstone;
use Brain\Monkey;
use Brain\Monkey\Functions;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Tombstone class.
 */
#[CoversClass( Tombstone::class )]
class TombstoneTest extends TestCase {

	/**
	 * Set up Brain Monkey before each test.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
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
	 * Verify the post status constant value.
	 *
	 * @return void
	 */
	public function test_status_constant(): void {
		$this->assertSame( 'sovereignty_tombstone', Tombstone::STATUS );
	}

	/**
	 * Verify register_post_status calls register_post_status with correct args.
	 *
	 * @return void
	 */
	public function test_register_post_status(): void {
		Functions\stubs( [ '__', '_n_noop' ] );
		Functions\expect( 'register_post_status' )
			->once()
			->with( 'sovereignty_tombstone', Mockery::type( 'array' ) );

		Tombstone::register_post_status();
	}

	/**
	 * Verify template_redirect skips non-singular pages.
	 *
	 * @return void
	 */
	public function test_template_redirect_skips_non_singular(): void {
		Functions\expect( 'is_singular' )->once()->andReturn( false );

		// Should return without calling status_header.
		Tombstone::template_redirect();
	}
}
