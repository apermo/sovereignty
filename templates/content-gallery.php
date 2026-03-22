<?php
/**
 * The template for displaying posts in the Gallery Post Format on index and archive pages
 *
 * Learn more: http://codex.wordpress.org/Post_Formats
 *
 * @package Sovereignty
 * @since Sovereignty 1.0.0
 */

use Apermo\Sovereignty\Featured_Image;
use Apermo\Sovereignty\Semantics;
use Apermo\Sovereignty\Template\Tags;

global $post; // Set by the_post() in calling template.
?>

<article <?php Tags::post_id( $post ); ?> <?php post_class(); ?><?php Semantics::output( 'post' ); ?>>
	<?php get_template_part( 'template-parts/entry-header' ); ?>

	<?php Featured_Image::the_post_thumbnail( $post, '<div class="entry-media">', '</div>' ); ?>
	<div class="entry-content e-content" itemprop="description">
		<?php Tags::the_content( $post ); ?>
		<?php
		wp_link_pages(
			[
				'before' => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'sovereignty' ) . '</span>',
				'after' => '</div>',
				'link_before' => '<span>',
				'link_after' => '</span>',
			],
		);
		?>
	</div><!-- .entry-content -->

	<?php get_template_part( 'template-parts/entry-footer' ); ?>
</article><!-- #post-<?php the_ID(); ?> -->
