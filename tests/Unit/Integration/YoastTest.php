<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Tests\Unit\Integration;

use Apermo\Sovereignty\Config;
use Apermo\Sovereignty\Integration\Yoast;
use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WP_Comment;
use WP_Post;

/**
 * Tests for the Yoast integration class.
 */
#[CoversClass( Yoast::class )]
class YoastTest extends TestCase {

	/**
	 * Set up Brain Monkey before each test.
	 */
	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
		Config::reset();
	}

	/**
	 * Tear down Brain Monkey after each test.
	 */
	protected function tearDown(): void {
		unset( $GLOBALS['_test_get_the_content'] );
		Config::reset();
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Verify that enrich_article adds timeRequired to article data.
	 */
	public function test_enrich_article_adds_time_required(): void {
		$post = new WP_Post();
		$post->ID = 1;

		$GLOBALS['_test_get_the_content'] = \str_repeat( 'word ', 500 );

		Functions\stubs(
			[
				'get_post'               => $post,
				'get_comments_number'    => 0,
				'wp_strip_all_tags'      => static fn ( $text ) => \strip_tags( $text ),
				'get_template_directory' => \dirname( __DIR__, 3 ),
			],
		);

		$data   = [
			'@type' => 'Article',
			'headline' => 'Test',
		];
		$result = Yoast::enrich_article( $data );

		$this->assertArrayHasKey( 'timeRequired', $result );
		$this->assertMatchesRegularExpression( '/^PT\d+M$/', $result['timeRequired'] );
	}

	/**
	 * Verify that enrich_article adds comments when present.
	 */
	public function test_enrich_article_adds_comments(): void {
		$post = new WP_Post();
		$post->ID = 1;

		$GLOBALS['_test_get_the_content'] = 'Short post.';

		$comment                  = new WP_Comment();
		$comment->comment_content = 'Great post!';
		$comment->comment_author  = 'Commenter';

		Functions\stubs(
			[
				'get_post'               => $post,
				'get_comments_number'    => 1,
				'get_comments'           => [ $comment ],
				'wp_strip_all_tags'      => static fn ( $text ) => \strip_tags( $text ),
				'get_comment_date'       => '2026-01-01T12:00:00+00:00',
				'get_template_directory' => \dirname( __DIR__, 3 ),
			],
		);

		$data   = [
			'@type' => 'Article',
			'headline' => 'Test',
		];
		$result = Yoast::enrich_article( $data );

		$this->assertArrayHasKey( 'comment', $result );
		$this->assertCount( 1, $result['comment'] );
		$this->assertSame( 'Comment', $result['comment'][0]['@type'] );
		$this->assertSame( 'Great post!', $result['comment'][0]['text'] );
	}

	/**
	 * Verify that enrich_article returns data unchanged when no post.
	 */
	public function test_enrich_article_returns_unchanged_without_post(): void {
		Functions\stubs( [ 'get_post' => static fn () => null ] );

		$data   = [
			'@type' => 'Article',
			'headline' => 'Test',
		];
		$result = Yoast::enrich_article( $data );

		$this->assertSame( $data, $result );
	}
}
