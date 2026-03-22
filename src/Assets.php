<?php

declare(strict_types=1);

namespace Apermo\Sovereignty;

/**
 * Script and style enqueueing.
 *
 * @package Sovereignty
 */
class Assets {

	/**
	 * Enqueue theme scripts and styles.
	 *
	 * @return void
	 */
	public static function enqueue(): void { // phpcs:ignore SlevomatCodingStandard.Functions.FunctionLength.FunctionLength -- Enqueue is inherently long.
		if (
			is_singular() &&
			comments_open() &&
			(bool) get_option( 'thread_comments' )
		) {
			wp_enqueue_script( 'comment-reply' );
		}

		wp_enqueue_script(
			'sovereignty-navigation',
			get_template_directory_uri() . '/assets/js/navigation.js',
			[],
			\SOVEREIGNTY_VERSION,
			[
				'strategy' => 'async',
			],
		);

		if ( is_singular() ) {
			wp_enqueue_script(
				'sovereignty-share',
				get_template_directory_uri() . '/assets/js/share.js',
				[],
				\SOVEREIGNTY_VERSION,
				[
					'strategy' => 'async',
				],
			);
		}

		wp_enqueue_style( 'dashicons' );

		$suffix    = \defined( 'SCRIPT_DEBUG' ) && \SCRIPT_DEBUG ? '' : '.min';
		$theme_uri = get_template_directory_uri();

		wp_enqueue_style( 'sovereignty-style', get_stylesheet_directory_uri() . "/style{$suffix}.css", [ 'dashicons' ], \SOVEREIGNTY_VERSION );
		wp_enqueue_style( 'sovereignty-print-style', $theme_uri . "/assets/css/print{$suffix}.css", [ 'sovereignty-style' ], \SOVEREIGNTY_VERSION, 'print' );
		wp_enqueue_style( 'sovereignty-narrow-style', $theme_uri . "/assets/css/narrow-width{$suffix}.css", [ 'sovereignty-style' ], \SOVEREIGNTY_VERSION, '(max-width: 800px)' );
		wp_enqueue_style( 'sovereignty-default-style', $theme_uri . "/assets/css/default-width{$suffix}.css", [ 'sovereignty-style' ], \SOVEREIGNTY_VERSION, '(min-width: 800px)' );
		wp_enqueue_style( 'sovereignty-wide-style', $theme_uri . "/assets/css/wide-width{$suffix}.css", [ 'sovereignty-style' ], \SOVEREIGNTY_VERSION, '(min-width: 1000px)' );

		wp_localize_script(
			'sovereignty',
			'vars',
			[
				'template_url' => get_template_directory_uri(),
			],
		);

		if ( has_header_image() ) {
			if ( is_author() ) {
				$css = '.page-banner {
					background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.7)), url(' . get_header_image() . ') no-repeat center center scroll;
				}' . \PHP_EOL;
			} else {
				$css = '.page-banner {
					background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.7)), url(' . get_header_image() . ') no-repeat center center scroll;
				}' . \PHP_EOL;
			}

			wp_add_inline_style( 'sovereignty-style', $css );
		}
	}
}
