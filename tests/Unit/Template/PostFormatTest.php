<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Tests\Unit\Template;

use Apermo\Sovereignty\Template\Post_Format;
use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WP_Post;

/**
 * Tests for the Post_Format class.
 */
#[CoversClass( Post_Format::class )]
class PostFormatTest extends TestCase {

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
	 * Create a mock WP_Post.
	 *
	 * @return WP_Post
	 */
	private function make_post(): WP_Post {
		$post     = new WP_Post();
		$post->ID = 1;
		return $post;
	}

	/**
	 * Verify get_format returns 'standard' when no format is set.
	 *
	 * @return void
	 */
	public function test_get_format_returns_standard_when_no_format(): void {
		Functions\expect( 'get_post_format' )->once()->andReturn( false );

		$this->assertSame( 'standard', Post_Format::get_format( $this->make_post() ) );
	}

	/**
	 * Verify get_format returns the actual format when set.
	 *
	 * @return void
	 */
	public function test_get_format_returns_actual_format(): void {
		Functions\expect( 'get_post_format' )->once()->andReturn( 'aside' );

		$this->assertSame( 'aside', Post_Format::get_format( $this->make_post() ) );
	}

	/**
	 * Verify get_format_string returns 'Attachment' for attachment post type.
	 *
	 * @return void
	 */
	public function test_get_format_string_returns_attachment_for_attachment(): void {
		Functions\expect( 'get_post_type' )->once()->andReturn( 'attachment' );
		Functions\stubs( [ '__' => static fn ( $text ) => $text ] );

		$this->assertSame( 'Attachment', Post_Format::get_format_string( $this->make_post() ) );
	}

	/**
	 * Verify get_format_string returns 'Text' for standard posts.
	 *
	 * @return void
	 */
	public function test_get_format_string_returns_text_for_standard(): void {
		Functions\stubs(
			[
				'get_post_type'   => 'post',
				'get_post_format' => false,
				'__'              => static fn ( $text ) => $text,
			],
		);

		$this->assertSame( 'Text', Post_Format::get_format_string( $this->make_post() ) );
	}
}
