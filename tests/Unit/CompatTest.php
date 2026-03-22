<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Tests\Unit;

use Apermo\Sovereignty\Compat;
use Brain\Monkey;
use PHPUnit\Framework\TestCase;

class CompatTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
	}

	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

	public function test_comment_autocomplete_adds_attributes(): void {
		$fields = [
			'author' => '<input type="text" name="author" />',
			'email'  => '<input type="email" name="email" />',
			'url'    => '<input type="url" name="url" />',
		];

		$result = Compat::comment_autocomplete( $fields );

		$this->assertStringContainsString( 'autocomplete="nickname name"', $result['author'] );
		$this->assertStringContainsString( 'autocomplete="email"', $result['email'] );
		$this->assertStringContainsString( 'autocomplete="url"', $result['url'] );
		$this->assertStringContainsString( 'enterkeyhint="send"', $result['url'] );
	}

	public function test_comment_field_input_type_adds_enterkeyhint(): void {
		$field = '<textarea name="comment"></textarea>';

		$result = Compat::comment_field_input_type( $field );

		$this->assertStringContainsString( 'enterkeyhint="next"', $result );
	}

	public function test_add_lazy_loading_adds_loading_attribute(): void {
		$content = '<img src="test.jpg" />';

		$result = Compat::add_lazy_loading( $content );

		$this->assertStringContainsString( 'loading="lazy"', $result );
	}

	public function test_add_lazy_loading_does_not_double_add(): void {
		$content = '<img loading="lazy" src="test.jpg" />';

		$result = Compat::add_lazy_loading( $content );

		$this->assertSame( 1, \substr_count( $result, 'loading="lazy"' ) );
	}

	public function test_disable_native_lazy_loading_returns_false_for_the_content(): void {
		$result = Compat::disable_native_lazy_loading( true, 'img', 'the_content' );

		$this->assertFalse( $result );
	}

	public function test_disable_native_lazy_loading_passes_through_for_other_contexts(): void {
		$result = Compat::disable_native_lazy_loading( true, 'img', 'widget_text' );

		$this->assertTrue( $result );
	}
}
