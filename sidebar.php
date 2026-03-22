<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package Sovereignty
 * @since Sovereignty 1.0.0
 */
?>
	<div id="sidebar">
		<?php
		/**
		 * Fires before the sidebar widgets.
		 */
		do_action( 'sovereignty_before_sidebar' );
		?>

		<div id="secondary" class="widget-area" role="complementary">
			<?php dynamic_sidebar( 'sidebar-1' ); ?>
		</div><!-- #secondary .widget-area -->

		<?php if ( is_active_sidebar( 'sidebar-2' ) ) { ?>
		<div id="tertiary" class="widget-area" role="complementary">
			<?php dynamic_sidebar( 'sidebar-2' ); ?>
		</div><!-- #tertiary .widget-area -->
		<?php } ?>

		<?php if ( is_active_sidebar( 'sidebar-3' ) ) { ?>
		<div id="quaternary" class="widget-area" role="complementary">
			<?php dynamic_sidebar( 'sidebar-3' ); ?>
		</div><!-- #quaternary .widget-area -->
		<?php } ?>
	</div>
