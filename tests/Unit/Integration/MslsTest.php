<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Tests\Unit\Integration;

use Apermo\Sovereignty\Integration\Msls;
use Brain\Monkey;
use Brain\Monkey\Functions;
use lloc\Msls\MslsBlog;
use lloc\Msls\MslsBlogCollection;
use lloc\Msls\OptionsInterface;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

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
	 * Invokes a private static method on the Msls class.
	 *
	 * @param string $method The method name.
	 * @param mixed  ...$args The arguments to pass.
	 *
	 * @return mixed
	 */
	private function invoke( string $method, mixed ...$args ): mixed {
		$reflection = new ReflectionMethod( Msls::class, $method );

		return $reflection->invoke( null, ...$args );
	}

	/**
	 * Builds a mock MslsBlog for the given locale, URL and blog id.
	 *
	 * @param string $locale The blog locale, e.g. de_DE.
	 * @param string $url    The translation URL, or empty when none exists.
	 * @param int    $id     The blog id, used for the home-page fallback.
	 *
	 * @return MockInterface
	 */
	private function blog( string $locale, string $url, int $id = 0 ): MockInterface {
		$blog = Mockery::mock( MslsBlog::class );
		$blog->shouldReceive( 'get_alpha2' )->andReturn( \substr( $locale, 0, 2 ) );
		$blog->shouldReceive( 'get_url' )->andReturn( $url );
		$blog->userblog_id = $id;

		return $blog;
	}

	/**
	 * Builds a mock collection over the given blogs.
	 *
	 * @param array<int, MockInterface> $blogs   Blog mocks in display order.
	 * @param MockInterface             $current The blog treated as current.
	 *
	 * @return MockInterface
	 */
	private function collection( array $blogs, MockInterface $current ): MockInterface {
		$collection = Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get_objects' )->andReturn( $blogs );
		$collection->shouldReceive( 'is_current_blog' )->andReturnUsing(
			static fn ( $blog ) => $blog === $current,
		);

		return $collection;
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
	 * Verify the current language is marked and translated ones are linked.
	 *
	 * @return void
	 */
	public function test_get_languages_marks_current_and_links_translations(): void {
		$en = $this->blog( 'en_US', '' );
		$de = $this->blog( 'de_DE', 'https://example.tld/de/' );

		$result = $this->invoke( 'get_languages', $this->collection( [ $en, $de ], $en ), Mockery::mock( OptionsInterface::class ) );

		$this->assertSame(
			[
				[
					'label' => 'EN',
					'lang' => 'en',
					'url' => '',
					'current' => true,
				],
				[
					'label' => 'DE',
					'lang' => 'de',
					'url' => 'https://example.tld/de/',
					'current' => false,
				],
			],
			$result,
		);
	}

	/**
	 * Verify a language with no translation falls back to its home page.
	 *
	 * @return void
	 */
	public function test_get_languages_falls_back_to_home_when_untranslated(): void {
		Functions\when( 'get_home_url' )->alias( static fn ( $id ) => "https://blog{$id}.tld/" );

		$en = $this->blog( 'en_US', '', 1 );
		$de = $this->blog( 'de_DE', '', 2 );

		$result = $this->invoke( 'get_languages', $this->collection( [ $en, $de ], $en ), Mockery::mock( OptionsInterface::class ) );

		$this->assertSame(
			[
				[
					'label' => 'EN',
					'lang' => 'en',
					'url' => '',
					'current' => true,
				],
				[
					'label' => 'DE',
					'lang' => 'de',
					'url' => 'https://blog2.tld/',
					'current' => false,
				],
			],
			$result,
		);
	}

	/**
	 * Verify nothing is collected when the only language is the current one.
	 *
	 * @return void
	 */
	public function test_get_languages_empty_without_other_languages(): void {
		$en = $this->blog( 'en_US', '' );

		$result = $this->invoke( 'get_languages', $this->collection( [ $en ], $en ), Mockery::mock( OptionsInterface::class ) );

		$this->assertSame( [], $result );
	}

	/**
	 * Verify render() wraps the current language and links in a labelled nav.
	 *
	 * @return void
	 */
	public function test_render_outputs_switcher_markup(): void {
		$languages = [
			[
				'label' => 'EN',
				'lang' => 'en',
				'url' => '',
				'current' => true,
			],
			[
				'label' => 'DE',
				'lang' => 'de',
				'url' => 'https://example.tld/de/',
				'current' => false,
			],
		];

		$this->assertSame(
			'<nav class="language-switcher" aria-label="Languages">'
			. '<span class="current" aria-current="page" lang="en">EN</span>'
			. '<a href="https://example.tld/de/" hreflang="de">DE</a>'
			. '</nav>',
			$this->invoke( 'render', $languages ),
		);
	}

	/**
	 * Verify display() renders nothing when the MSLS plugin is inactive.
	 *
	 * @return void
	 */
	public function test_display_renders_nothing_when_plugin_inactive(): void {
		$this->expectOutputString( '' );

		Msls::display();
	}
}
