<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Tests\Unit\Template;

use Apermo\Sovereignty\Template\Post_Format;
use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

class PostFormatTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
	}

	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

	public function test_get_format_returns_standard_when_no_format(): void {
		Functions\expect( 'get_post_format' )->once()->andReturn( false );

		$this->assertSame( 'standard', Post_Format::get_format() );
	}

	public function test_get_format_returns_actual_format(): void {
		Functions\expect( 'get_post_format' )->once()->andReturn( 'aside' );

		$this->assertSame( 'aside', Post_Format::get_format() );
	}

	public function test_get_format_string_returns_attachment_for_attachment(): void {
		Functions\expect( 'get_post_type' )->once()->andReturn( 'attachment' );
		Functions\stubs( [ '__' => static fn ( $text ) => $text ] );

		$this->assertSame( 'Attachment', Post_Format::get_format_string() );
	}

	public function test_get_format_string_returns_text_for_standard(): void {
		Functions\stubs(
			[
				'get_post_type' => 'post',
				'get_post_format' => false,
				'__' => static fn ( $text ) => $text,
			],
		);

		$this->assertSame( 'Text', Post_Format::get_format_string() );
	}
}
