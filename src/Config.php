<?php

declare(strict_types=1);

namespace Apermo\Sovereignty;

/**
 * Centralized theme configuration.
 *
 * Loads theme.json, caches the parsed result, exposes values via
 * dot-notation, and provides the sovereignty_config filter for
 * multisite per-site overrides.
 *
 * @package Sovereignty
 */
final class Config {

	/**
	 * Parsed config data, null until first load.
	 *
	 * @var array|null
	 */
	private static ?array $data = null;

	/**
	 * Get a config value by dot-notation path.
	 *
	 * @param string $key     Dot-notation path (e.g. 'sovereignty.embed.width').
	 * @param mixed  $default Fallback if key not found.
	 *
	 * @return mixed
	 */
	public static function get( string $key, mixed $default = null ): mixed {
		$data = self::load();

		return self::resolve( $data, $key ) ?? $default;
	}

	/**
	 * Get an integer config value.
	 *
	 * @param string $key     Dot-notation path.
	 * @param int    $default Fallback value.
	 *
	 * @return int
	 */
	public static function int( string $key, int $default = 0 ): int {
		return (int) ( self::get( $key ) ?? $default );
	}

	/**
	 * Get a string config value.
	 *
	 * @param string $key     Dot-notation path.
	 * @param string $default Fallback value.
	 *
	 * @return string
	 */
	public static function string( string $key, string $default = '' ): string {
		return (string) ( self::get( $key ) ?? $default );
	}

	/**
	 * Get an array config value.
	 *
	 * @param string $key     Dot-notation path.
	 * @param array  $default Fallback value.
	 *
	 * @return array
	 */
	public static function array( string $key, array $default = [] ): array {
		$value = self::get( $key );

		return \is_array( $value ) ? $value : $default;
	}

	/**
	 * Get a boolean config value.
	 *
	 * @param string $key     Dot-notation path.
	 * @param bool   $default Fallback value.
	 *
	 * @return bool
	 */
	public static function bool( string $key, bool $default = false ): bool {
		return (bool) ( self::get( $key ) ?? $default );
	}

	/**
	 * Reset the cached config (for testing).
	 *
	 * @return void
	 */
	public static function reset(): void {
		self::$data = null;
	}

	/**
	 * Load and cache the config.
	 *
	 * Reads theme.json from the theme root, decodes it, applies
	 * the sovereignty_config filter, and caches the result.
	 *
	 * @return array The full config tree.
	 */
	private static function load(): array {
		if ( self::$data !== null ) {
			return self::$data;
		}

		$file = get_template_directory() . '/theme.json';

		if ( ! \file_exists( $file ) ) {
			self::$data = [];
			return self::$data;
		}

		$content = \file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local file, not remote.
		$decoded = \json_decode( $content, true );

		if ( ! \is_array( $decoded ) ) {
			self::$data = [];
			return self::$data;
		}

		/**
		 * Filters the full theme configuration.
		 *
		 * Use this to override any config value per-site in a multisite,
		 * or from a child theme or mu-plugin.
		 *
		 * @param array $config The decoded theme.json contents.
		 *
		 * @return array The filtered config.
		 */
		self::$data = apply_filters( 'sovereignty_config', $decoded );

		return self::$data;
	}

	/**
	 * Resolve a dot-notation path against a nested array.
	 *
	 * @param array  $data The data tree.
	 * @param string $path Dot-separated key path.
	 *
	 * @return mixed The resolved value, or null if not found.
	 */
	private static function resolve( array $data, string $path ): mixed {
		$segments = \explode( '.', $path );

		$current = $data;

		foreach ( $segments as $segment ) {
			if ( ! \is_array( $current ) || ! \array_key_exists( $segment, $current ) ) {
				return null;
			}

			$current = $current[ $segment ];
		}

		return $current;
	}
}
