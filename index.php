<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Sovereignty
 * @since Sovereignty 1.0.0
 */

use Apermo\Sovereignty\Semantics;
use Apermo\Sovereignty\Template\Tags;

get_header(); ?>

		<main id="primary" <?php Tags::main_class(); ?><?php Semantics::output( 'main' ); ?>>

		<?php if ( have_posts() ) { ?>

			<?php /* Start the Loop */ ?>
			<?php
			while ( have_posts() ) {
				the_post();
				global $post;
				?>

				<?php
					/*
					 * Include the Post-Format-specific template for the content.
					 * If you want to overload this in a child theme then include a file
					 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
					 */
				get_template_part( 'templates/content', get_post_format( $post ) );
				?>

			<?php } ?>

		<?php } else { ?>

			<article id="post-0" class="post no-results not-found">
				<header class="entry-header">
					<h1 class="entry-title p-entry-title"><?php esc_html_e( 'Nothing Found', 'sovereignty' ); ?></h1>
				</header><!-- .entry-header -->

				<div class="entry-content e-entry-content">
					<p><?php esc_html_e( "It seems we can't find what you're looking for. Perhaps searching can help.", 'sovereignty' ); ?></p>
					<?php get_search_form(); ?>
				</div><!-- .entry-content -->
			</article><!-- #post-0 -->

		<?php } ?>

		</main><!-- #content -->

		<?php Tags::content_nav( 'nav-below' ); ?>

<?php
get_footer();
