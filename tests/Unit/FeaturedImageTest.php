<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Tests\Unit;

use Apermo\Sovereignty\Featured_Image;
use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

class FeaturedImageTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
	}

	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

	public function test_has_full_width_returns_false_when_not_singular(): void {
		Functions\expect( 'is_singular' )->once()->andReturn( false );

		$this->assertFalse( Featured_Image::has_full_width() );
	}

	public function test_has_full_width_returns_false_when_no_thumbnail(): void {
		Functions\expect( 'is_singular' )->once()->andReturn( true );
		Functions\expect( 'has_post_thumbnail' )->once()->andReturn( false );

		$this->assertFalse( Featured_Image::has_full_width() );
	}

	public function test_has_full_width_returns_true_when_meta_enabled(): void {
		Functions\expect( 'is_singular' )->once()->andReturn( true );
		Functions\expect( 'has_post_thumbnail' )->once()->andReturn( true );
		Functions\expect( 'get_the_ID' )->once()->andReturn( 1 );
		Functions\expect( 'get_post_meta' )->once()->with( 1, 'full_width_featured_image', true )->andReturn( '1' );

		$this->assertTrue( Featured_Image::has_full_width() );
	}

	public function test_post_class_adds_class_when_full_width(): void {
		Functions\expect( 'is_singular' )->twice()->andReturn( true );
		Functions\expect( 'has_post_thumbnail' )->once()->andReturn( true );
		Functions\expect( 'get_the_ID' )->once()->andReturn( 1 );
		Functions\expect( 'get_post_meta' )->once()->andReturn( '1' );

		$result = Featured_Image::post_class( [ 'post' ] );

		$this->assertContains( 'has-full-width-featured-image', $result );
	}

	public function test_post_class_unchanged_when_not_full_width(): void {
		Functions\expect( 'is_singular' )->once()->andReturn( false );

		$result = Featured_Image::post_class( [ 'post' ] );

		$this->assertNotContains( 'has-full-width-featured-image', $result );
	}

	public function test_featured_image_meta_appends_checkbox(): void {
		Functions\expect( 'get_post_meta' )->once()->andReturn( '1' );
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
