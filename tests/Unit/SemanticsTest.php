<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Tests\Unit;

use Apermo\Sovereignty\Config;
use Apermo\Sovereignty\Semantics;
use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Semantics class.
 */
#[CoversClass( Apermo\Sovereignty\Semantics::class )]
class SemanticsTest extends TestCase {

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
	 * Verify that body_classes adds h-feed and hfeed classes on non-singular pages.
	 */
	public function test_body_classes_adds_hfeed_on_non_singular(): void {
		Functions\stubs(
			[
				'get_theme_mod' => 'multi',
				'is_singular' => false,
				'is_404' => false,
				'is_multi_author' => false,
				'get_header_image' => false,
			],
		);

		$result = Semantics::body_classes( [] );

		$this->assertContains( 'hfeed', $result );
		$this->assertContains( 'h-feed', $result );
	}

	/**
	 * Verify that body_classes omits hfeed on singular pages.
	 */
	public function test_body_classes_skips_hfeed_on_singular(): void {
		Functions\stubs(
			[
				'get_theme_mod' => 'multi',
				'is_singular' => true,
				'is_404' => false,
				'is_multi_author' => true,
				'get_header_image' => false,
			],
		);

		$result = Semantics::body_classes( [] );

		$this->assertNotContains( 'hfeed', $result );
	}

	/**
	 * Verify that post_classes replaces WP's hentry with microformat classes on non-singular pages.
	 */
	public function test_post_classes_removes_hentry_and_adds_microformats(): void {
		Functions\expect( 'is_singular' )->once()->andReturn( false );

		$result = Semantics::post_classes( [ 'post', 'hentry' ] );

		$this->assertContains( 'h-entry', $result );
		$this->assertContains( 'hentry', $result );
	}

	/**
	 * Verify that comment_classes adds microformat classes for comments.
	 */
	public function test_comment_classes_adds_microformat_classes(): void {
		$result = Semantics::comment_classes( [ 'comment' ] );

		$this->assertContains( 'h-entry', $result );
		$this->assertContains( 'h-cite', $result );
		$this->assertContains( 'p-comment', $result );
	}

	/**
	 * Verify that author_link adds the u-url microformat class to the link.
	 */
	public function test_author_link_adds_u_url_class(): void {
		$link = '<a class="url fn" href="https://example.com">Author</a>';

		$result = Semantics::author_link( $link );

		$this->assertStringContainsString( 'u-url', $result );
	}

	/**
	 * Verify that next_posts_link_attributes adds rel="prev" for pagination.
	 */
	public function test_next_posts_link_attributes_adds_rel_prev(): void {
		$result = Semantics::next_posts_link_attributes( '' );

		$this->assertStringContainsString( 'rel="prev"', $result );
	}

	/**
	 * Verify that previous_posts_link_attributes adds rel="next" for pagination.
	 */
	public function test_previous_posts_link_attributes_adds_rel_next(): void {
		$result = Semantics::previous_posts_link_attributes( '' );

		$this->assertStringContainsString( 'rel="next"', $result );
	}

	/**
	 * Verify that get_search_form wraps the form in a search element.
	 */
	public function test_get_search_form_adds_search_wrapper(): void {
		$form = '<form><input type="search" /></form>';
		$result = Semantics::get_search_form( $form );

		$this->assertStringContainsString( '<search>', $result );
		$this->assertStringContainsString( 'enterkeyhint="search"', $result );
		$this->assertStringNotContainsString( 'itemprop', $result );
		$this->assertStringNotContainsString( 'SearchAction', $result );
	}

	/**
	 * Verify that body output is empty after microdata removal.
	 */
	public function test_body_output_is_empty(): void {
		Functions\stubs(
			[
				'apply_filters' => static fn ( $hook, $value ) => $value,
			],
		);

		\ob_start();
		Semantics::output( 'body' );
		$output = \ob_get_clean();

		$this->assertSame( '', $output );
	}

	/**
	 * Verify that get_semantics returns an empty array for an unknown element ID.
	 */
	public function test_get_semantics_returns_empty_for_unknown_id(): void {
		Functions\stubs(
			[
				'apply_filters' => static fn ( $hook, $value ) => $value,
			],
		);

		$result = Semantics::get_semantics( 'unknown-element' );

		$this->assertSame( [], $result );
	}
}
