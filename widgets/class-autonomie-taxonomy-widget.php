<?php

/**
 * Taxonomy widget for displaying entry taxonomy in the entry-meta sidebar.
 */
// phpcs:ignore PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Commenting.ClassComment.Missing
class Autonomie_Taxonomy_Widget extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {

		parent::__construct(
			'autonomie-taxonomy',  // Base ID
			'Entry Taxonomy (Autonomie)'   // Name
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
		get_template_part( 'template-parts/entry', 'taxonomy' );
	}
}
