<?php

declare(strict_types=1);

namespace Apermo\Sovereignty;

use WP_Comment;

/**
 * Comment rendering callback for wp_list_comments.
 *
 * @package Sovereignty
 */
class Comment {

	/**
	 * Render a single comment with microformats and microdata.
	 *
	 * Used as a callback by wp_list_comments() for displaying comments.
	 *
	 * @param WP_Comment $comment The comment object.
	 * @param array      $args    Display arguments.
	 * @param int        $depth   Depth of the comment.
	 *
	 * @return void
	 */
	public static function render( WP_Comment $comment, array $args, int $depth ): void {
		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Required by wp_list_comments() callback.
		$GLOBALS['comment'] = $comment;
		?>
		<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
			<article id="comment-<?php comment_ID(); ?>" class="comment <?php echo esc_attr( $comment->comment_type ); ?>" itemprop="comment" itemscope itemtype="https://schema.org/Comment">
				<div class="edit-link"><?php edit_comment_link( __( 'Edit', 'sovereignty' ), ' ' ); ?></div>
				<footer class="comment-meta commentmetadata">
					<address class="comment-author p-author author vcard hcard h-card" itemprop="creator" itemscope itemtype="https://schema.org/Person">
						<?php echo get_avatar( $comment, 40 ); ?>
						<?php \printf( '<cite class="fn p-name" itemprop="name">%s</cite>', get_comment_author_link() ); ?>
					</address><!-- .comment-author .vcard -->

					<a href="<?php echo esc_url( get_comment_link( $comment ) ); ?>"><time class="updated published dt-updated dt-published" datetime="<?php comment_time( 'c' ); ?>" itemprop="dateCreated">
					<?php
						/* translators: 1: date, 2: time */
						\printf( esc_html__( '%1$s at %2$s', 'sovereignty' ), esc_html( get_comment_date() ), esc_html( get_comment_time() ) );
					?>
					</time></a>
				</footer>

			<?php if ( $comment->comment_approved === '0' ) { ?>
					<p><em><?php esc_html_e( 'Your comment is awaiting moderation.', 'sovereignty' ); ?></em></p>
				<?php } ?>

				<div class="comment-content e-content p-summary p-name" itemprop="text name description"><?php comment_text(); ?></div>

				<div class="reply">
					<?php
					comment_reply_link(
						\array_merge(
							$args,
							[
								'depth'     => $depth,
								'max_depth' => $args['max_depth'],
							],
						),
					);
					?>
				</div><!-- .reply -->
			</article><!-- #comment-## -->
		<?php
	}
}
