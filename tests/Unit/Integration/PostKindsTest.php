<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Tests\Unit\Integration;

use Apermo\Sovereignty\Integration\Post_Kinds;
use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Post_Kinds integration class.
 */
#[CoversClass( Post_Kinds::class )]
class PostKindsTest extends TestCase {

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
	 * Verify format() returns original HTML when no post kind is set.
	 *
	 * @return void
	 */
	public function test_format_returns_original_when_no_kind(): void {
		Functions\expect( 'get_post_kind_slug' )->once()->andReturn( '' );

		$result = Post_Kinds::format( '<a>Original</a>' );

		$this->assertSame( '<a>Original</a>', $result );
	}

	/**
	 * Verify register() hooks into the correct actions and filters.
	 *
	 * @return void
	 */
	public function test_register_adds_hooks(): void {
		Functions\expect( 'add_action' )->once()->with( 'init', [ Post_Kinds::class, 'remove_defaults' ] );
		Functions\expect( 'add_action' )->once()->with( 'sovereignty_before_entry_content', [ Post_Kinds::class, 'content' ] );
		Functions\expect( 'add_filter' )->once()->with( 'sovereignty_post_format', [ Post_Kinds::class, 'format' ] );

		Post_Kinds::register();
	}
}
