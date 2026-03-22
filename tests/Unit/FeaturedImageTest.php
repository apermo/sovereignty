<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Tests\Unit;

use Apermo\Sovereignty\Featured_Image;
use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WP_Post;

/**
 * Tests for the Featured_Image class.
 */
#[CoversClass( Featured_Image::class )]
class FeaturedImageTest extends TestCase {

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
	 * Verify has_full_width returns false on non-singular pages.
	 *
	 * @return void
	 */
	public function test_has_full_width_returns_false_when_not_singular(): void {
		Functions\expect( 'is_singular' )->once()->andReturn( false );

		$this->assertFalse( Featured_Image::has_full_width( $this->make_post() ) );
	}

	/**
	 * Verify has_full_width returns false when no post thumbnail is set.
	 *
	 * @return void
	 */
	public function test_has_full_width_returns_false_when_no_thumbnail(): void {
		Functions\expect( 'is_singular' )->once()->andReturn( true );
		Functions\expect( 'has_post_thumbnail' )->once()->andReturn( false );

		$this->assertFalse( Featured_Image::has_full_width( $this->make_post() ) );
	}

	/**
	 * Verify has_full_width returns true when meta is enabled.
	 *
	 * @return void
	 */
	public function test_has_full_width_returns_true_when_meta_enabled(): void {
		Functions\expect( 'is_singular' )->once()->andReturn( true );
		Functions\expect( 'has_post_thumbnail' )->once()->andReturn( true );
		Functions\expect( 'get_post_meta' )->once()->with( 1, 'full_width_featured_image', true )->andReturn( '1' );

		$this->assertTrue( Featured_Image::has_full_width( $this->make_post() ) );
	}

	/**
	 * Verify post_class adds the full-width class when enabled.
	 * Hook callback — resolves $post internally.
	 *
	 * @return void
	 */
	public function test_post_class_adds_class_when_full_width(): void {
		$post = $this->make_post();

		Functions\expect( 'get_post' )->once()->andReturn( $post );
		Functions\expect( 'is_singular' )->twice()->andReturn( true );
		Functions\expect( 'has_post_thumbnail' )->once()->andReturn( true );
		Functions\expect( 'get_post_meta' )->once()->with( 1, 'full_width_featured_image', true )->andReturn( '1' );

		$result = Featured_Image::post_class( [ 'post' ] );

		$this->assertContains( 'has-full-width-featured-image', $result );
	}

	/**
	 * Verify post_class does not add the class when not singular.
	 *
	 * @return void
	 */
	public function test_post_class_unchanged_when_not_full_width(): void {
		Functions\expect( 'get_post' )->once()->andReturn( $this->make_post() );
		Functions\expect( 'is_singular' )->once()->andReturn( false );

		$result = Featured_Image::post_class( [ 'post' ] );

		$this->assertNotContains( 'has-full-width-featured-image', $result );
	}

	/**
	 * Verify admin_thumbnail_html appends a full-width checkbox.
	 *
	 * @return void
	 */
	public function test_featured_image_meta_appends_checkbox(): void {
		Functions\expect( 'get_post_meta' )->once()->with( 42, 'full_width_featured_image', true )->andReturn( '1' );
		Functions\stubs(
			[
				'esc_html__' => static fn ( $text ) => $text,
				'esc_attr'   => static fn ( $val ) => $val,
				'checked'    => static fn ( $val, $current, $echo ) => $val === $current ? 'checked' : '',
			],
		);

		$result = Featured_Image::admin_thumbnail_html( '<p>existing</p>', 42 );

		$this->assertStringContainsString( '<p>existing</p>', $result );
		$this->assertStringContainsString( 'full_width_featured_image', $result );
		$this->assertStringContainsString( 'checkbox', $result );
	}

	/**
	 * Create a mock WP_Post object.
	 *
	 * @param int $post_id The post ID.
	 *
	 * @return WP_Post
	 */
	private function make_post( int $post_id = 1 ): WP_Post {
		$post     = new WP_Post();
		$post->ID = $post_id;
		return $post;
	}
}
