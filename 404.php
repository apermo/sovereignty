<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package Sovereignty
 * @since Sovereignty 1.0.0
 */

use Apermo\Sovereignty\Semantics;
use Apermo\Sovereignty\Template\Tags;

get_header();
?>

		<main id="primary" <?php Tags::main_class(); ?><?php Semantics::output( 'main' ); ?>>

			<article id="post-0" class="post error404 not-found">
				<header class="entry-header">
					<h1 class="entry-title p-name"><?php esc_html_e( "Well this is somewhat embarrassing, isn't it?", 'sovereignty' ); ?></h1>
				</header>

				<div class="entry-content e-content">
					<p><?php esc_html_e( "It seems we can't find what you're looking for. Perhaps searching, or one of the links below, can help.", 'sovereignty' ); ?></p>

					<?php get_search_form(); ?>

					<?php the_widget( 'WP_Widget_Recent_Posts' ); ?>

					<div class="widget">
						<h2 class="widgettitle"><?php esc_html_e( 'Most Used Categories', 'sovereignty' ); ?></h2>
						<ul>
						<?php
						wp_list_categories(
							[
								'orderby' => 'count',
								'order' => 'DESC',
								'show_count' => 1,
								'title_li' => '',
								'number' => 10,
							],
						);
						?>
						</ul>
					</div>

					<?php
					/* translators: %1$s: smilie */
					$sovereignty_archive_content = '<p>' . sprintf( __( 'Try looking in the monthly archives. %1$s', 'sovereignty' ), convert_smilies( ':)' ) ) . '</p>';
					the_widget( 'WP_Widget_Archives', [ 'dropdown' => 1 ], [ 'after_title' => '</h2>' . $sovereignty_archive_content ] );
					?>

					<?php the_widget( 'WP_Widget_Tag_Cloud' ); ?>

				</div><!-- .entry-content -->
			</article><!-- #post-0 -->

		</main><!-- #content -->

<?php
get_footer();
