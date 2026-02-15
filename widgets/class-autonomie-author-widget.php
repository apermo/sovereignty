<?php

/**
 * Author widget for displaying author details in the entry-meta sidebar.
 */
// phpcs:ignore PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Commenting.ClassComment.Missing
class Autonomie_Author_Widget extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'autonomie-author',          // Base ID.
			'Author Details (Autonomie)' // Name.
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
