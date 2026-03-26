<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package Sovereignty
 * @since Sovereignty 1.0.0
 */

use Apermo\Sovereignty\Semantics;
use Apermo\Sovereignty\Template\Functions;
use Apermo\Sovereignty\Template\Tags;
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php echo esc_attr( get_bloginfo( 'charset' ) ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="profile" href="https://microformats.org/profile/specs" />
	<link rel="profile" href="https://microformats.org/profile/hatom" />

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?><?php Semantics::output( 'body' ); ?>>
<?php
wp_body_open();
?>
<div id="page">
	<div class="skip-link screen-reader-text"><a href="#primary" title="<?php esc_attr_e( 'Skip to content', 'sovereignty' ); ?>"><?php esc_html_e( 'Skip to content', 'sovereignty' ); ?></a></div>
	<?php
	/**
	 * Fires before the site header.
	 */
	do_action( 'sovereignty_before' );
	?>
	<header id="site-header" class="site-header">
		<div class="site-branding">
			<?php
			if ( has_custom_logo() ) {
				echo get_custom_logo(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Returns safe HTML from core.
			}

			?>
			<<?php Tags::site_title_tag(); ?> id="site-title"<?php Semantics::output( 'site-title' ); ?>>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"<?php Semantics::output( 'site-url' ); ?>>
				<?php echo esc_html( get_bloginfo( 'name' ) ); ?>
				</a>
			</<?php Tags::site_title_tag(); ?>>

			<?php get_search_form( [ 'echo' => true ] ); ?>
		</div>

		<nav id="site-navigation" class="site-navigation">
			<button class="menu-toggle" aria-controls="site-navigation" aria-expanded="false"><?php esc_html_e( 'Primary Menu', 'sovereignty' ); ?></button>

			<?php wp_nav_menu( [ 'theme_location' => 'primary' ] ); ?>
		</nav><!-- #site-navigation -->

		<?php get_template_part( 'template-parts/page-banner', Functions::get_archive_type() ); ?>
	</header><!-- #site-header -->
