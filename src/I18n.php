<?php

declare(strict_types=1);

namespace Apermo\Sovereignty;

/**
 * Registers the theme with the self-hosted Traduttore Registry so installs
 * receive translations from the GlotPress server at translate.chrdm.de.
 *
 * The theme text domain is loaded in Setup::setup() via load_theme_textdomain();
 * this class only points WordPress at the translation source.
 *
 * @package Sovereignty
 */
class I18n {

	/**
	 * Project type as understood by Traduttore Registry.
	 */
	private const PROJECT_TYPE = 'theme';

	/**
	 * GlotPress translations API endpoint for this project. The trailing slash
	 * is significant; the bare `/api/translations/` path returns a 404.
	 */
	private const API_URL = 'https://translate.chrdm.de/glotpress/api/translations/sovereignty/';

	/**
	 * Register the project with Traduttore Registry when the library is present.
	 *
	 * Degrades to a no-op when the dependency is missing.
	 *
	 * @return void
	 */
	public static function add_project(): void {
		if ( \function_exists( 'Required\Traduttore_Registry\add_project' ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions, SlevomatCodingStandard.Namespaces.FullyQualifiedGlobalFunctions, SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly -- The ruleset also bans `use function` imports, so a guarded FQ call is the only option.
			\Required\Traduttore_Registry\add_project(
				self::PROJECT_TYPE,
				'sovereignty',
				self::API_URL,
			);
		}
	}
}
