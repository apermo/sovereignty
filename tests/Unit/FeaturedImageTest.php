<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Tests\Unit;

use Apermo\Sovereignty\Featured_Image;
use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Featured_Image class.
 */
#[CoversClass( Apermo\Sovereignty\Featured_Image::class )]
class FeaturedImageTest extends TestCase {

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
	 * Verify that has_full_width returns false on non-singular pages.
	 */
	public function test_has_full_width_returns_false_when_not_singular(): void {
		Functions\expect( 'is_singular' )->once()->andReturn( false );

		$this->assertFalse( Featured_Image::has_full_width() );
	}

	/**
	 * Verify that has_full_width returns false when no post thumbnail is set.
	 */
	public function test_has_full_width_returns_false_when_no_thumbnail(): void {
		Functions\expect( 'is_singular' )->once()->andReturn( true );
		Functions\expect( 'has_post_thumbnail' )->once()->andReturn( false );

		$this->assertFalse( Featured_Image::has_full_width() );
	}

	/**
	 * Verify that has_full_width returns true when the meta value is enabled.
	 */
	public function test_has_full_width_returns_true_when_meta_enabled(): void {
		Functions\expect( 'is_singular' )->once()->andReturn( true );
		Functions\expect( 'has_post_thumbnail' )->once()->andReturn( true );
		Functions\expect( 'get_the_ID' )->once()->andReturn( 1 );
		Functions\expect( 'get_post_meta' )->once()->with( 1, 'full_width_featured_image', true )->andReturn( '1' );

		$this->assertTrue( Featured_Image::has_full_width() );
	}

	/**
	 * Verify that post_class adds the full-width class when the feature is enabled.
	 */
	public function test_post_class_adds_class_when_full_width(): void {
		Functions\expect( 'is_singular' )->twice()->andReturn( true );
		Functions\expect( 'has_post_thumbnail' )->once()->andReturn( true );
		Functions\expect( 'get_the_ID' )->once()->andReturn( 1 );
		Functions\expect( 'get_post_meta' )->once()->with( 1, 'full_width_featured_image', true )->andReturn( '1' );

		$result = Featured_Image::post_class( [ 'post' ] );

		$this->assertContains( 'has-full-width-featured-image', $result );
	}

	/**
	 * Verify that post_class does not add the full-width class when the feature is disabled.
	 */
	public function test_post_class_unchanged_when_not_full_width(): void {
		Functions\expect( 'is_singular' )->once()->andReturn( false );

		$result = Featured_Image::post_class( [ 'post' ] );

		$this->assertNotContains( 'has-full-width-featured-image', $result );
	}

	/**
	 * Verify that admin_thumbnail_html appends a full-width checkbox to the thumbnail metabox.
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
}
