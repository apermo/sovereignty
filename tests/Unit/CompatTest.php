<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Tests\Unit;

use Apermo\Sovereignty\Compat;
use Brain\Monkey;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Compat class.
 */
#[CoversClass( Compat::class )]
class CompatTest extends TestCase {

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
	 * Verify autocomplete and enterkeyhint attributes are added to comment form fields.
	 *
	 * @return void
	 */
	public function test_comment_autocomplete_adds_attributes(): void {
		$fields = [
			'author' => '<input type="text" name="author" />',
			'email'  => '<input type="email" name="email" />',
			'url'    => '<input type="url" name="url" />',
		];

		$result = Compat::comment_autocomplete( $fields );

		$this->assertStringContainsString( 'autocomplete="nickname name"', $result['author'] );
		$this->assertStringContainsString( 'enterkeyhint="next"', $result['author'] );
		$this->assertStringContainsString( 'autocomplete="email"', $result['email'] );
		$this->assertStringContainsString( 'inputmode="email"', $result['email'] );
		$this->assertStringContainsString( 'autocomplete="url"', $result['url'] );
		$this->assertStringContainsString( 'enterkeyhint="send"', $result['url'] );
	}

	/**
	 * Verify enterkeyhint attribute is added to the comment textarea.
	 *
	 * @return void
	 */
	public function test_comment_field_input_type_adds_enterkeyhint(): void {
		$field = '<textarea name="comment"></textarea>';

		$result = Compat::comment_field_input_type( $field );

		$this->assertStringContainsString( 'enterkeyhint="next"', $result );
	}

	/**
	 * Verify loading="lazy" is added to elements with src attributes.
	 *
	 * @return void
	 */
	public function test_add_lazy_loading_adds_loading_attribute(): void {
		$content = '<img src="test.jpg" />';

		$result = Compat::add_lazy_loading( $content );

		$this->assertStringContainsString( 'loading="lazy"', $result );
	}

	/**
	 * Verify loading="lazy" is not duplicated on elements that already have it.
	 *
	 * @return void
	 */
	public function test_add_lazy_loading_does_not_double_add(): void {
		$content = '<img loading="lazy" src="test.jpg" />';

		$result = Compat::add_lazy_loading( $content );

		$this->assertSame( 1, \substr_count( $result, 'loading="lazy"' ) );
	}

	/**
	 * Verify native lazy loading is disabled for the_content context.
	 *
	 * @return void
	 */
	public function test_disable_native_lazy_loading_returns_false_for_the_content(): void {
		$result = Compat::disable_native_lazy_loading( true, 'img', 'the_content' );

		$this->assertFalse( $result );
	}

	/**
	 * Verify native lazy loading is preserved for non-content contexts.
	 *
	 * @return void
	 */
	public function test_disable_native_lazy_loading_passes_through_for_other_contexts(): void {
		$result = Compat::disable_native_lazy_loading( true, 'img', 'widget_text' );

		$this->assertTrue( $result );
	}
}
