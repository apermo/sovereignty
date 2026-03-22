<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Tests\Unit\Template;

use Apermo\Sovereignty\Template\Tags;
use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Tags class.
 */
#[CoversClass( Apermo\Sovereignty\Template\Tags::class )]
class TagsTest extends TestCase {

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
	 * Verify that get_post_id returns the post ID with a "post-" prefix.
	 */
	public function test_get_post_id_returns_prefixed_id(): void {
		Functions\expect( 'get_the_ID' )->andReturn( 42 );
		Functions\expect( 'apply_filters' )->once()->andReturnUsing( static fn ( $hook, $val ) => $val );

		$result = Tags::get_post_id();

		$this->assertSame( 'post-42', $result );
	}

	/**
	 * Verify that reading_time outputs a duration element with the estimated minutes.
	 */
	public function test_reading_time_outputs_time_element(): void {
		Functions\expect( 'get_post_field' )->once()->andReturn( \str_repeat( 'word ', 400 ) );
		Functions\stubs(
			[
				'wp_strip_all_tags' => static fn ( $text ) => $text,
				'_n' => static fn ( $s, $p, $n ) => $n > 1 ? $p : $s,
				'esc_html' => static fn ( $val ) => $val,
				'number_format_i18n' => static fn ( $val ) => (string) $val,
			],
		);

		\ob_start();
		Tags::reading_time();
		$output = \ob_get_clean();

		$this->assertStringContainsString( 'dt-duration', $output );
		$this->assertStringContainsString( 'minutes', $output );
	}

	/**
	 * Verify that the_content uses the excerpt on search result pages.
	 */
	public function test_the_content_uses_excerpt_on_search(): void {
		Functions\expect( 'is_search' )->once()->andReturn( true );
		Functions\expect( 'the_excerpt' )->once();

		Tags::the_content();
	}
}
