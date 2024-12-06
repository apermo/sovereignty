<?php
/**
 * Adds "custom-color" support
 *
 * @since 1.3.0
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function autonomie_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'autonomie_settings_section',
		array(
			'title' => __( 'Advanced Settings', 'autonomie' ),
			'description' => __( 'Enable/Disable some advanced Autonomie features.', 'autonomie' ),
			'priority' => 35,
		)
	);
}
// add_action( 'customize_register', 'autonomie_customize_register' );
