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
		$content_width = Config::int( 'sovereignty.embed.width' );

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
			'width'  => Config::int( 'sovereignty.embed.width' ),
			'height' => Config::int( 'sovereignty.embed.height' ),
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
		$provider = add_query_arg( 'width', Config::int( 'sovereignty.embed.width' ), $provider );
		$provider = add_query_arg( 'height', Config::int( 'sovereignty.embed.height' ), $provider );

		return $provider;
	}
}
