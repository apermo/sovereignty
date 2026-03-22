<?php
/**
 * The template for displaying posts in the Quote Post Format on index and archive pages
 *
 * Learn more: http://codex.wordpress.org/Post_Formats
 *
 * @package Sovereignty
 * @since Sovereignty 1.0.0
 */

use Apermo\Sovereignty\Featured_Image;
use Apermo\Sovereignty\Semantics;
use Apermo\Sovereignty\Template\Tags;
?>

<article <?php Tags::post_id(); ?> <?php post_class(); ?><?php Semantics::output( 'post' ); ?>>
	<?php get_template_part( 'template-parts/entry-header' ); ?>

	<?php Featured_Image::the_post_thumbnail( '<div class="entry-media">', '</div>' ); ?>
	<div class="entry-title p-name entry-content e-content" itemprop="name headline description articleBody">
		<?php Tags::the_content(); ?>
		<?php
		wp_link_pages(
			[
				'before' => '<div class="page-link">' . __( 'Pages:', 'sovereignty' ),
				'after' => '</div>',
			],
		);
		?>
	</div><!-- .entry-content -->

	<?php get_template_part( 'template-parts/entry-footer' ); ?>
</article><!-- #post-<?php the_ID(); ?> -->
