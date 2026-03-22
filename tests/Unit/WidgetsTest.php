<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Tests\Unit;

use Apermo\Sovereignty\Widget\Author;
use Apermo\Sovereignty\Widget\Taxonomy;
use Apermo\Sovereignty\Widgets;
use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Widgets class.
 */
#[CoversClass( Apermo\Sovereignty\Widgets::class )]
class WidgetsTest extends TestCase {

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
	 * Verify that widgets_init registers the Author and Taxonomy custom widgets.
	 */
	public function test_widgets_init_registers_custom_widgets(): void {
		Functions\expect( 'register_widget' )
			->once()
			->with( Author::class );

		Functions\expect( 'register_widget' )
			->once()
			->with( Taxonomy::class );

		Functions\stubs( [ 'register_sidebar', '__' ] );

		Widgets::widgets_init();
	}

	/**
	 * Verify that widgets_init registers exactly four sidebars.
	 */
	public function test_widgets_init_registers_four_sidebars(): void {
		Functions\stubs( [ 'register_widget', '__' ] );

		Functions\expect( 'register_sidebar' )->times( 4 );

		Widgets::widgets_init();
	}

	/**
	 * Verify that starter_content_add_widget adds entry-meta widgets to starter content.
	 */
	public function test_starter_content_adds_entry_meta_widgets(): void {
		$content = [ 'widgets' => [] ];

		$result = Widgets::starter_content_add_widget( $content, [] );

		$this->assertArrayHasKey( 'entry-meta', $result['widgets'] );
		$this->assertCount( 2, $result['widgets']['entry-meta'] );
		$this->assertSame( 'sovereignty-author', $result['widgets']['entry-meta'][0][0] );
		$this->assertSame( 'sovereignty-taxonomy', $result['widgets']['entry-meta'][1][0] );
	}

	/**
	 * Verify that activate sets the default widget option values.
	 */
	public function test_activate_sets_default_widget_options(): void {
		Functions\expect( 'update_option' )->times( 3 );

		Widgets::activate();
	}
}
