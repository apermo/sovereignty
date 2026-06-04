<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Tests\Unit\Integration;

use Apermo\Sovereignty\Integration\Msls;
use Brain\Monkey;
use Brain\Monkey\Functions;
use lloc\Msls\MslsBlog;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Msls integration class.
 */
#[CoversClass( Msls::class )]
class MslsTest extends TestCase {

	/**
	 * Set up Brain Monkey before each test.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
		Functions\when( 'esc_attr__' )->returnArg();
		Functions\when( 'esc_attr' )->returnArg();
		Functions\when( 'esc_html' )->returnArg();
		Functions\when( 'esc_url' )->returnArg();
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
	 * Builds a mock MslsBlog for the given locale and translation URL.
	 *
	 * @param string $locale The blog locale, e.g. de_DE.
	 * @param string $url    The translation URL, or empty when none exists.
	 *
	 * @return MockInterface
	 */
	private function blog( string $locale, string $url ): MockInterface {
		$blog = Mockery::mock( MslsBlog::class );
		$blog->shouldReceive( 'get_language' )->andReturn( $locale );
		$blog->shouldReceive( 'get_alpha2' )->andReturn( \substr( $locale, 0, 2 ) );
		$blog->shouldReceive( 'get_url' )->andReturn( $url );

		return $blog;
	}

	/**
	 * Stubs the MSLS collection and options around the given blogs.
	 *
	 * @param array<int, MockInterface> $blogs   Blog mocks in display order.
	 * @param MockInterface             $current The blog treated as current.
	 *
	 * @return void
	 */
	private function stub_collection( array $blogs, MockInterface $current ): void {
		$collection = Mockery::mock();
		$collection->shouldReceive( 'get_objects' )->andReturn( $blogs );
		$collection->shouldReceive( 'is_current_blog' )->andReturnUsing(
			static fn ( $blog ) => $blog === $current,
		);

		Functions\when( 'msls_blog_collection' )->justReturn( $collection );
		Functions\when( 'msls_options' )->justReturn( Mockery::mock() );
	}

	/**
	 * Verify register() hooks the switcher into the search action.
	 *
	 * @return void
	 */
	public function test_register_adds_hook(): void {
		Functions\expect( 'add_action' )->once()->with( 'sovereignty_after_search', [ Msls::class, 'display' ] );

		Msls::register();
	}

	/**
	 * Verify display() marks the current language and links the translated ones.
	 *
	 * @return void
	 */
	public function test_display_marks_current_and_links_others(): void {
		$en = $this->blog( 'en_US', '' );
		$de = $this->blog( 'de_DE', 'https://example.tld/de/' );

		$this->stub_collection( [ $en, $de ], $en );

		$this->expectOutputString(
			'<nav class="language-switcher" aria-label="Languages">'
			. '<span class="current" aria-current="page" lang="en">EN</span>'
			. '<a href="https://example.tld/de/" hreflang="de">DE</a>'
			. '</nav>',
		);

		Msls::display();
	}

	/**
	 * Verify display() skips languages that have no translation for the content.
	 *
	 * @return void
	 */
	public function test_display_skips_untranslated_languages(): void {
		$en = $this->blog( 'en_US', '' );
		$de = $this->blog( 'de_DE', 'https://example.tld/de/' );
		$fr = $this->blog( 'fr_FR', '' );

		$this->stub_collection( [ $en, $de, $fr ], $en );

		$this->expectOutputString(
			'<nav class="language-switcher" aria-label="Languages">'
			. '<span class="current" aria-current="page" lang="en">EN</span>'
			. '<a href="https://example.tld/de/" hreflang="de">DE</a>'
			. '</nav>',
		);

		Msls::display();
	}

	/**
	 * Verify display() renders nothing when no other language has a translation.
	 *
	 * @return void
	 */
	public function test_display_renders_nothing_without_other_languages(): void {
		$en = $this->blog( 'en_US', '' );

		$this->stub_collection( [ $en ], $en );

		$this->expectOutputString( '' );

		Msls::display();
	}
}
