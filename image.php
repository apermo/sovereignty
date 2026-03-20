<?php
/**
 * The template for displaying image attachments.
 *
 * @package Autonomie
 * @since Autonomie 1.0.0
 */

get_header(); ?>

			<main id="primary" <?php autonomie_main_class(); ?><?php autonomie_semantics( 'main' ); ?>>

			<?php
			while ( have_posts() ) :
				the_post();
				?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>itemscope itemtype="https://schema.org/ImageObject">
					<?php get_template_part( 'template-parts/entry-header' ); ?>

					<div class="entry-content e-content" itemprop="description">

						<div class="entry-attachment">
							<figure class="attachment">
								<?php
								/**
								 * Grab the IDs of all the image attachments in a gallery so we can get the URL of the next adjacent image in a gallery,
								 * or the first image (if we're looking at the last image in a gallery), or, in a gallery of one, just the link to that image file
								 */
								global $post;
								$sovereignty_attachments = array_values(
									get_children(
										[
											'post_parent' => $post->post_parent,
											'post_status' => 'inherit',
											'post_type' => 'attachment',
											'post_mime_type' => 'image',
											'order' => 'ASC',
											'orderby' => 'menu_order ID',
										]
									)
								);
								$sovereignty_k = 0;
								foreach ( $sovereignty_attachments as $sovereignty_k => $sovereignty_attachment ) {
									if ( $sovereignty_attachment->ID === $post->ID ) {
										break;
									}
								}
								$sovereignty_k++;
								// If there is more than 1 attachment in a gallery.
								if ( count( $sovereignty_attachments ) > 1 ) {
									if ( isset( $sovereignty_attachments[ $sovereignty_k ] ) ) {
										// Get the URL of the next image attachment.
										$sovereignty_next_url = get_attachment_link( $sovereignty_attachments[ $sovereignty_k ]->ID );
									} else {
										// Or get the URL of the first image attachment.
										$sovereignty_next_url = get_attachment_link( $sovereignty_attachments[0]->ID );
									}
								} else {
									// Or, if there's only 1 image, get the URL of the image.
									$sovereignty_next_url = wp_get_attachment_url();
								}
								?>

							<a href="<?php echo esc_url( $sovereignty_next_url ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" rel="attachment">
													<?php
													/**
													 * Filters the attachment image size.
													 *
													 * @param int $size The attachment image size in pixels.
													 *
													 * @return int The filtered size.
													 */
													$sovereignty_attachment_size = apply_filters( 'autonomie_attachment_size', 1200 );
													echo wp_get_attachment_image( $post->ID, [ $sovereignty_attachment_size, $sovereignty_attachment_size ], false, [ 'itemprop' => 'image contentURL' ] );
													?>
								</a>

								<?php if ( ! empty( $post->post_excerpt ) ) : ?>
								<figcaption class="entry-caption">
									<?php the_excerpt(); ?>
								</figcaption>
								<?php endif; ?>
							</figure><!-- .attachment -->
						</div><!-- .entry-attachment -->

						<?php the_content(); ?>
						<?php
						wp_link_pages(
							[
								'before' => '<div class="page-link">' . __( 'Pages:', 'autonomie' ),
								'after' => '</div>',
							]
						);
						?>
					</div><!-- .entry-content -->

					<?php get_template_part( 'template-parts/entry-footer' ); ?>
				</article><!-- #post-<?php the_ID(); ?> -->

				<?php comments_template(); ?>

			<?php endwhile; // end of the loop. ?>

			</main><!-- #content -->

<?php
get_footer();
