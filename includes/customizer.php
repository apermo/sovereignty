<?php
/**
 * Adds "custom-color" support
 *
 * @since 1.3.0
 *
 * @param WP_Customize_Manager $wp_customize The WordPress customizer manager.
 *
 * @return void
 */
function autonomie_customize_register( WP_Customize_Manager $wp_customize ): void { // phpcs:ignore Generic.NamingConventions.CamelCapsFunctionName.NotCamelCaps, Squiz.NamingConventions.ValidVariableName.NotCamelCaps -- WordPress core parameter name.

	// phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps -- WordPress core parameter name.
	$wp_customize->add_section(
		'autonomie_settings_section',
		[
			'title' => __( 'Advanced Settings', 'autonomie' ),
			'description' => __( 'Enable/Disable some advanced Autonomie features.', 'autonomie' ), // Descriptive tooltip.
			'priority' => 35,
		]
	);
}
// add_action( 'customize_register', 'autonomie_customize_register' );
