<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Tests\Unit;

use Apermo\Sovereignty\Feed;
use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WP_Post;

/**
 * Tests for the Feed class.
 */
#[CoversClass( Feed::class )]
class FeedTest extends TestCase {

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
	 * Verify that an empty format link returns false.
	 *
	 * @return void
	 */
	public function test_get_post_format_archive_feed_link_returns_false_for_empty_link(): void {
		Functions\expect( 'get_default_feed' )->once()->andReturn( 'rss2' );
		Functions\stubs(
			[
				'get_post'      => $this->make_post(),
				'get_post_type' => 'page',
				'get_permalink' => '',
			],
		);

		$result = Feed::get_post_format_archive_feed_link( 'standard' );

		$this->assertFalse( $result );
	}

	/**
	 * Verify the feed link appends /feed/ with pretty permalinks.
	 *
	 * @return void
	 */
	public function test_get_post_format_archive_feed_link_appends_feed_with_permalinks(): void {
		Functions\expect( 'get_default_feed' )->once()->andReturn( 'rss2' );
		Functions\stubs(
			[
				'get_post'             => $this->make_post(),
				'get_post_type'        => 'post',
				'get_post_format_link' => 'https://example.com/type/standard',
			],
		);
		Functions\expect( 'get_option' )->once()->with( 'permalink_structure' )->andReturn( '/%postname%/' );
		Functions\expect( 'trailingslashit' )->once()->andReturnUsing( static fn ( $url ) => \rtrim( $url, '/' ) . '/' );
		Functions\expect( 'apply_filters' )->once()->andReturnUsing( static fn ( $hook, $value ) => $value );

		$result = Feed::get_post_format_archive_feed_link( 'aside' );

		$this->assertStringContainsString( '/feed/', $result );
	}

	/**
	 * Verify the feed link uses query args without pretty permalinks.
	 *
	 * @return void
	 */
	public function test_get_post_format_archive_feed_link_uses_query_arg_without_permalinks(): void {
		Functions\expect( 'get_default_feed' )->once()->andReturn( 'rss2' );
		Functions\stubs(
			[
				'get_post'             => $this->make_post(),
				'get_post_type'        => 'post',
				'get_post_format_link' => 'https://example.com/?post_format=aside',
			],
		);
		Functions\expect( 'get_option' )->once()->with( 'permalink_structure' )->andReturn( '' );
		Functions\expect( 'add_query_arg' )->once()->andReturn( 'https://example.com/?post_format=aside&feed=rss2' );
		Functions\expect( 'apply_filters' )->once()->andReturnUsing( static fn ( $hook, $value ) => $value );

		$result = Feed::get_post_format_archive_feed_link( 'aside' );

		$this->assertStringContainsString( 'feed=rss2', $result );
	}
}
