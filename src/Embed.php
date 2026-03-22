<?php

declare(strict_types=1);

namespace Apermo\Sovereignty;

/**
 * Content width and embed defaults.
 *
 * @package Sovereignty
 */
class Embed {

	/**
	 * Set the content width global.
	 *
	 * @return void
	 */
	public static function content_width(): void {
		$content_width = 900;

		/**
		 * Filters the content width in pixels.
		 *
		 * @param int $content_width The content width.
		 *
		 * @return int The filtered content width.
		 */
		$GLOBALS['content_width'] = apply_filters( 'sovereignty_content_width', $content_width );
	}

	/**
	 * Set the default embed dimensions.
	 *
	 * @return array{width: int, height: int}
	 */
	public static function defaults(): array {
		return [
			'width'  => 900,
			'height' => 600,
		];
	}

	/**
	 * Set default embed width/height on oEmbed fetch URL.
	 *
	 * @param string $provider The oEmbed provider URL.
	 *
	 * @return string
	 */
	public static function fetch_url( string $provider ): string {
		$provider = add_query_arg( 'width', 900, $provider );
		$provider = add_query_arg( 'height', 600, $provider );

		return $provider;
	}
}
