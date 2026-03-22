<?php
/**
 * The default template for displaying post content
 *
 * Used for Standard posts and as fallback for all post formats
 * without a dedicated template file.
 *
 * @package Sovereignty
 * @since Sovereignty 1.0.0
 */

use Apermo\Sovereignty\Featured_Image;

global $post;
use Apermo\Sovereignty\Semantics;

global $post;
use Apermo\Sovereignty\Template\Tags;

global $post;
?>

<article <?php Tags::post_id( $post ); ?> <?php post_class(); ?><?php Semantics::output( 'post' ); ?>>
	<?php get_template_part( 'template-parts/entry-header' ); ?>

	<?php Featured_Image::the_post_thumbnail( $post, '<div class="entry-media">', '</div>' ); ?>
	<div class="entry-content e-content" itemprop="description articleBody">
		<?php Tags::the_content( $post ); ?>
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
