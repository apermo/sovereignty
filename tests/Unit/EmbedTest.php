<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Tests\Unit;

use Apermo\Sovereignty\Embed;
use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

class EmbedTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
	}

	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

	public function test_content_width_sets_global(): void {
		Functions\expect( 'apply_filters' )
			->once()
			->with( 'sovereignty_content_width', 900 )
			->andReturn( 900 );

		Embed::content_width();

		$this->assertSame( 900, $GLOBALS['content_width'] );
	}

	public function test_defaults_returns_dimensions(): void {
		$result = Embed::defaults();

		$this->assertSame( 900, $result['width'] );
		$this->assertSame( 600, $result['height'] );
	}

	public function test_fetch_url_adds_dimensions(): void {
		Functions\expect( 'add_query_arg' )
			->twice()
			->andReturnUsing( static fn ( $key, $val, $url ) => $url . "&{$key}={$val}" );

		$result = Embed::fetch_url( 'https://provider.com/oembed' );

		$this->assertStringContainsString( 'width=900', $result );
		$this->assertStringContainsString( 'height=600', $result );
	}
}
