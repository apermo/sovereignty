<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Widget;

use WP_Widget;

/**
 * Author widget for displaying author details in the entry-meta sidebar.
 *
 * @package Sovereignty
 */
class Author extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'sovereignty-author',
			'Author Details (Sovereignty)',
		);
	}

	/**
	 * Output the widget content.
	 *
	 * @param mixed $args     Widget display arguments.
	 * @param mixed $instance Widget settings.
	 *
	 * @return void
	 */
	public function widget( $args, $instance ): void { // phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
		get_template_part( 'template-parts/entry', 'author' );
	}
}
