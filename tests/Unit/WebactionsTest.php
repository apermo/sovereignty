<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Tests\Unit;

use Apermo\Sovereignty\Webactions;
use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;
use stdClass;

class WebactionsTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
	}

	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

	public function test_comment_reply_link_wraps_in_indie_action(): void {
		$comment          = new stdClass();
		$comment->comment_ID = 42;

		$post     = new stdClass();
		$post->ID = 1;

		Functions\expect( 'get_permalink' )
			->once()
			->with( 1 )
			->andReturn( 'https://example.com/post/' );

		Functions\expect( 'add_query_arg' )
			->once()
			->with( 'replytocom', 42, 'https://example.com/post/' )
			->andReturn( 'https://example.com/post/?replytocom=42' );

		Functions\expect( 'esc_url' )
			->once()
			->andReturnFirstArg();

		$result = Webactions::comment_reply_link(
			'<a class="reply">Reply</a>',
			[],
			$comment,
			$post,
		);

		$this->assertStringContainsString( 'indie-action', $result );
		$this->assertStringContainsString( 'do="reply"', $result );
		$this->assertStringContainsString( 'replytocom=42', $result );
		$this->assertStringContainsString( '<a class="reply">Reply</a>', $result );
	}

	public function test_comment_form_after_closes_indie_action(): void {
		$this->expectOutputString( '</indie-action>' );
		Webactions::comment_form_after();
	}
}
