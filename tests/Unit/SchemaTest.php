<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Tests\Unit;

use Apermo\Sovereignty\Config;
use Apermo\Sovereignty\Schema;
use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WP_Comment;

/**
 * Tests for the Schema class.
 */
#[CoversClass( Schema::class )]
class SchemaTest extends TestCase {

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
		Config::reset();
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Verify that output produces no JSON-LD on 404 pages.
	 */
	public function test_output_is_empty_on_404(): void {
		Functions\stubs( [ 'is_404' => true ] );

		\ob_start();
		Schema::output();
		$output = \ob_get_clean();

		$this->assertSame( '', $output );
	}

	/**
	 * Verify that output is skipped when Yoast is active.
	 */
	public function test_output_is_empty_when_yoast_active(): void {
		if ( ! \defined( 'WPSEO_VERSION' ) ) {
			\define( 'WPSEO_VERSION', '27.0' );
		}

		\ob_start();
		Schema::output();
		$output = \ob_get_clean();

		$this->assertSame( '', $output );
	}

	/**
	 * Verify that build_person returns a Person schema piece.
	 */
	public function test_build_person_returns_person_schema(): void {
		Functions\stubs(
			[
				'get_the_author_meta' => 'John Doe',
				'get_author_posts_url' => 'https://example.com/author/john/',
			],
		);

		$result = Schema::build_person( 1 );

		$this->assertSame( 'Person', $result['@type'] );
		$this->assertSame( 'John Doe', $result['name'] );
		$this->assertSame( 'https://example.com/author/john/', $result['url'] );
	}

	/**
	 * Verify that build_comment returns a Comment schema piece.
	 */
	public function test_build_comment_returns_comment_schema(): void {
		Functions\stubs(
			[
				'wp_strip_all_tags' => static fn ( $text ) => \strip_tags( $text ),
				'get_comment_date'  => '2026-01-01T12:00:00+00:00',
			],
		);

		$comment                  = new WP_Comment();
		$comment->comment_content = 'Test comment content';
		$comment->comment_author  = 'Jane Doe';

		$result = Schema::build_comment( $comment );

		$this->assertSame( 'Comment', $result['@type'] );
		$this->assertSame( 'Test comment content', $result['text'] );
		$this->assertSame( 'Jane Doe', $result['author']['name'] );
		$this->assertSame( '2026-01-01T12:00:00+00:00', $result['dateCreated'] );
	}
}
