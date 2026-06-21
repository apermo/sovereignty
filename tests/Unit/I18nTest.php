<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Tests\Unit;

use Apermo\Sovereignty\I18n;
use Brain\Monkey;
use Brain\Monkey\Functions;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

/**
 * Tests the Traduttore Registry project registration.
 *
 * The library autoloads its `Required\Traduttore_Registry\add_project()`
 * function eagerly (composer `files` autoload), so the function is always
 * defined in the test process and cannot be redefined by Brain Monkey. These
 * tests therefore drive the real `add_project()` and assert on the WordPress
 * hooks it wires.
 */
#[CoversClass( I18n::class )]
class I18nTest extends TestCase {

	use MockeryPHPUnitIntegration;

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
	 * Confirm the registry library is bundled so the runtime guard resolves.
	 *
	 * @return void
	 */
	public function test_registry_library_is_available(): void {
		$this->assertTrue(
			\function_exists( 'Required\Traduttore_Registry\add_project' ),
			'The Traduttore Registry library must ship with the theme.',
		);
	}

	/**
	 * Confirm add_project() wires the registry's translation filters for the
	 * sovereignty slug.
	 *
	 * Registration mutates the library's static project registry, so this runs
	 * in a separate process to keep that state out of sibling tests.
	 *
	 * @return void
	 */
	#[RunInSeparateProcess]
	#[PreserveGlobalState( false )]
	public function test_adds_project_when_library_present(): void {
		$filters = [];
		// has_action/add_action stubs satisfy the registry's own internal cron
		// wiring; only the add_filter calls are the subject of this test.
		Functions\when( 'has_action' )->justReturn( false );
		Functions\when( 'add_action' )->justReturn( true );
		Functions\when( 'add_filter' )->alias(
			static function ( string $hook ) use ( &$filters ): bool {
				$filters[] = $hook;

				return true;
			},
		);

		I18n::add_project();

		$this->assertContains( 'translations_api', $filters );
		$this->assertContains( 'site_transient_update_themes', $filters );
	}
}
