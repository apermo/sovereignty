<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Tests\Unit\Template;

use Apermo\Sovereignty\Template\Post_Format;
use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Post_Format class.
 */
#[CoversClass( Apermo\Sovereignty\Template\Post_Format::class )]
class PostFormatTest extends TestCase {

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
	 * Verify that get_format returns "standard" when no post format is set.
	 */
	public function test_get_format_returns_standard_when_no_format(): void {
		Functions\expect( 'get_post_format' )->once()->andReturn( false );

		$this->assertSame( 'standard', Post_Format::get_format() );
	}

	/**
	 * Verify that get_format returns the actual post format when one is set.
	 */
	public function test_get_format_returns_actual_format(): void {
		Functions\expect( 'get_post_format' )->once()->andReturn( 'aside' );

		$this->assertSame( 'aside', Post_Format::get_format() );
	}

	/**
	 * Verify that get_format_string returns "Attachment" for attachment post types.
	 */
	public function test_get_format_string_returns_attachment_for_attachment(): void {
		Functions\expect( 'get_post_type' )->once()->andReturn( 'attachment' );
		Functions\stubs( [ '__' => static fn ( $text ) => $text ] );

		$this->assertSame( 'Attachment', Post_Format::get_format_string() );
	}

	/**
	 * Verify that get_format_string returns "Text" for the standard post format.
	 */
	public function test_get_format_string_returns_text_for_standard(): void {
		Functions\stubs(
			[
				'get_post_type' => 'post',
				'get_post_format' => false,
				'__' => static fn ( $text ) => $text,
			],
		);

		$this->assertSame( 'Text', Post_Format::get_format_string() );
	}
}
