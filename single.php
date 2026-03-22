<?php
/**
 * The Template for displaying all single posts.
 *
 * @package Sovereignty
 * @since Sovereignty 1.0.0
 */

use Apermo\Sovereignty\Semantics;
use Apermo\Sovereignty\Template\Tags;

get_header(); ?>

			<main id="primary" <?php Tags::main_class(); ?><?php Semantics::output( 'main' ); ?>>

			<?php
			while ( have_posts() ) {
				the_post();
				?>

				<?php get_template_part( 'templates/content', get_post_format() ); ?>

				<?php
				// If comments are open, or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() !== '0' ) {
					comments_template( '', true );
				}
				?>

			<?php } // end of the loop. ?>

			</main><!-- #content -->

			<?php Tags::content_nav( 'nav-below' ); ?>

<?php
get_footer();
