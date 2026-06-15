<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package Sovereignty
 * @since Sovereignty 1.0.0
 */

$sovereignty_widget_areas = [
	'secondary'  => 'sidebar-1',
	'tertiary'   => 'sidebar-2',
	'quaternary' => 'sidebar-3',
];
?>
	<div id="sidebar">
		<?php
		/**
		 * Fires before the sidebar widgets.
		 */
		do_action( 'sovereignty_before_sidebar' );

		foreach ( $sovereignty_widget_areas as $sovereignty_id => $sovereignty_sidebar ) {
			ob_start();
			dynamic_sidebar( $sovereignty_sidebar );
			$sovereignty_widgets = trim( (string) ob_get_clean() );

			// Skip empty areas so they don't leave hollow landmark elements behind.
			if ( $sovereignty_widgets === '' ) {
				continue;
			}

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Widget HTML from core/registered widgets.
			printf( '<div id="%1$s" class="widget-area">%2$s</div>', esc_attr( $sovereignty_id ), $sovereignty_widgets );
		}
		?>
	</div>
