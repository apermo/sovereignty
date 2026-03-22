<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Tests\Unit;

use Apermo\Sovereignty\Feed;
use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

class FeedTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
	}

	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

	public function test_get_post_format_archive_feed_link_returns_false_for_empty_link(): void {
		Functions\expect( 'get_default_feed' )->once()->andReturn( 'rss2' );
		// Post_Format::get_format_link() for 'standard' with page type returns permalink.
		Functions\stubs(
			[
				'get_post_type' => 'page',
				'get_permalink' => '',
			],
		);

		$result = Feed::get_post_format_archive_feed_link( 'standard' );

		$this->assertFalse( $result );
	}

	public function test_get_post_format_archive_feed_link_appends_feed_with_permalinks(): void {
		Functions\expect( 'get_default_feed' )->once()->andReturn( 'rss2' );
		Functions\stubs(
			[
				'get_post_type' => 'post',
				'get_post_format_link' => 'https://example.com/type/standard',
			],
		);
		Functions\expect( 'get_option' )->once()->with( 'permalink_structure' )->andReturn( '/%postname%/' );
		Functions\expect( 'trailingslashit' )->once()->andReturnUsing( static fn ( $url ) => \rtrim( $url, '/' ) . '/' );
		Functions\expect( 'apply_filters' )->once()->andReturnUsing( static fn ( $hook, $value ) => $value );

		$result = Feed::get_post_format_archive_feed_link( 'aside' );

		$this->assertStringContainsString( '/feed/', $result );
	}

	public function test_get_post_format_archive_feed_link_uses_query_arg_without_permalinks(): void {
		Functions\expect( 'get_default_feed' )->once()->andReturn( 'rss2' );
		Functions\stubs(
			[
				'get_post_type' => 'post',
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
