<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Tests\Unit\Template;

use Apermo\Sovereignty\Template\Tags;
use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WP_Post;

/**
 * Tests for the Tags class.
 */
#[CoversClass( Tags::class )]
class TagsTest extends TestCase {

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
	 * @param int $post_id The post ID.
	 *
	 * @return WP_Post
	 */
	private function make_post( int $post_id = 42 ): WP_Post {
		$post              = new WP_Post();
		$post->ID          = $post_id;
		$post->post_author = 1;
		return $post;
	}

	/**
	 * Verify get_post_id returns the post ID with a "post-" prefix.
	 *
	 * @return void
	 */
	public function test_get_post_id_returns_prefixed_id(): void {
		Functions\expect( 'apply_filters' )->once()->andReturnUsing( static fn ( $hook, $val ) => $val );

		$result = Tags::get_post_id( $this->make_post( 42 ) );

		$this->assertSame( 'post-42', $result );
	}

	/**
	 * Verify reading_time outputs a duration element with estimated minutes.
	 *
	 * @return void
	 */
	public function test_reading_time_outputs_time_element(): void {
		Functions\expect( 'get_post_field' )->once()->andReturn( \str_repeat( 'word ', 400 ) );
		Functions\stubs(
			[
				'wp_strip_all_tags'  => static fn ( $text ) => $text,
				'_n'                 => static fn ( $s, $p, $n ) => $n > 1 ? $p : $s,
				'esc_html'           => static fn ( $val ) => $val,
				'number_format_i18n' => static fn ( $val ) => (string) $val,
			],
		);

		\ob_start();
		Tags::reading_time( $this->make_post() );
		$output = \ob_get_clean();

		$this->assertStringContainsString( 'dt-duration', $output );
		$this->assertStringContainsString( 'minutes', $output );
	}

	/**
	 * Verify the_content outputs the excerpt on search pages.
	 *
	 * @return void
	 */
	public function test_the_content_uses_excerpt_on_search(): void {
		Functions\expect( 'is_search' )->once()->andReturn( true );
		Functions\expect( 'get_the_excerpt' )->once()->andReturn( 'Excerpt text' );

		\ob_start();
		Tags::the_content( $this->make_post() );
		$output = \ob_get_clean();

		$this->assertSame( 'Excerpt text', $output );
	}

	/**
	 * Verify site_title_tag returns h1 on the homepage.
	 *
	 * @return void
	 */
	public function test_site_title_tag_returns_h1_on_home(): void {
		Functions\expect( 'is_home' )->once()->andReturn( true );

		$this->assertSame( 'h1', Tags::site_title_tag() );
	}

	/**
	 * Verify site_title_tag returns div on non-home pages.
	 *
	 * @return void
	 */
	public function test_site_title_tag_returns_div_on_non_home(): void {
		Functions\expect( 'is_home' )->once()->andReturn( false );

		$this->assertSame( 'div', Tags::site_title_tag() );
	}

	/**
	 * Verify entry_title_tag returns h1 on singular pages.
	 *
	 * @return void
	 */
	public function test_entry_title_tag_returns_h1_on_singular(): void {
		Functions\expect( 'is_singular' )->once()->andReturn( true );

		$this->assertSame( 'h1', Tags::entry_title_tag() );
	}

	/**
	 * Verify entry_title_tag returns h2 on non-singular pages.
	 *
	 * @return void
	 */
	public function test_entry_title_tag_returns_h2_on_non_singular(): void {
		Functions\expect( 'is_singular' )->once()->andReturn( false );

		$this->assertSame( 'h2', Tags::entry_title_tag() );
	}
}
