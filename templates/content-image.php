<?php
/**
 * The template for displaying posts in the Image Post Format on index and archive pages
 *
 * Learn more: http://codex.wordpress.org/Post_Formats
 *
 * @package Autonomie
 * @since Autonomie 1.0.0
 */
?>

<article <?php autonomie_post_id(); ?> <?php post_class(); ?><?php autonomie_semantics( 'post' ); ?>>
	<?php get_template_part( 'template-parts/entry-header' ); ?>

	<?php autonomie_the_post_thumbnail( '<div class="entry-media">', '</div>' ); ?>
	<div class="entry-content e-content" itemprop="description articleBody">
		<?php autonomie_the_content(); ?>
		<?php
		wp_link_pages(
			array(
				'before' => '<div class="page-link">' . __( 'Pages:', 'autonomie' ),
				'after' => '</div>',
			)
		);
		?>
	</div><!-- .entry-content -->

	<?php get_template_part( 'template-parts/entry-footer' ); ?>
</article><!-- #post-<?php the_ID(); ?> -->
