<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Template;

/**
 * Inline SVG icon helpers.
 *
 * Reads SVG markup from the theme's `assets/svg/` directory so icons can be
 * inlined into templates and styled with CSS (currentColor, custom
 * properties) and animated.
 *
 * @package Sovereignty
 */
class Svg {

	/**
	 * Retrieves the inline markup for an SVG icon.
	 *
	 * @param string $name Icon file name without the `.svg` extension.
	 *
	 * @return string The SVG markup, or an empty string when the icon is missing.
	 */
	public static function get( string $name ): string {
		$path = get_template_directory() . '/assets/svg/' . $name . '.svg';

		if ( ! \file_exists( $path ) ) {
			return wp_get_environment_type() === 'production'
				? ''
				: '<!-- sovereignty: missing SVG icon "' . esc_html( $name ) . '" -->';
		}

		$svg = \file_get_contents( $path );

		return $svg === false ? '' : $svg;
	}

	/**
	 * Prints the inline markup for an SVG icon.
	 *
	 * @param string $name Icon file name without the `.svg` extension.
	 *
	 * @return void
	 */
	public static function print( string $name ): void {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted theme-bundled SVG markup.
		echo self::get( $name );
	}
}
