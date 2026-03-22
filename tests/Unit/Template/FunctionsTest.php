<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Tests\Unit\Template;

use Apermo\Sovereignty\Template\Functions;
use Brain\Monkey;
use Brain\Monkey\Functions as BrainFunctions;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Functions class.
 */
#[CoversClass( Apermo\Sovereignty\Template\Functions::class )]
class FunctionsTest extends TestCase {

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
	 * Verify that get_the_archive_title returns the search query as the title on search pages.
	 */
	public function test_get_the_archive_title_returns_search_title(): void {
		BrainFunctions\expect( 'is_archive' )->once()->andReturn( false );
		BrainFunctions\expect( 'is_search' )->once()->andReturn( true );
		BrainFunctions\expect( 'get_search_query' )->once()->andReturn( 'test' );
		BrainFunctions\stubs( [ '__' => static fn ( $text ) => $text ] );

		$result = Functions::get_the_archive_title();

		$this->assertStringContainsString( 'test', $result );
	}

	/**
	 * Verify that show_page_banner returns true on archive pages.
	 */
	public function test_show_page_banner_true_on_archive(): void {
		BrainFunctions\stubs(
			[
				'is_home' => false,
				'is_archive' => true,
				'is_search' => false,
			],
		);

		$this->assertTrue( Functions::show_page_banner() );
	}

	/**
	 * Verify that get_archive_type returns "author" on author archive pages.
	 */
	public function test_get_archive_type_returns_author_on_author_page(): void {
		BrainFunctions\expect( 'is_author' )->once()->andReturn( true );
		BrainFunctions\expect( 'apply_filters' )->once()->andReturnUsing( static fn ( $hook, $val ) => $val );

		$this->assertSame( 'author', Functions::get_archive_type() );
	}
}
