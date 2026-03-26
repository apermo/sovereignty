<?php if ( is_singular() || is_attachment() ) { ?>
	<footer class="entry-footer entry-meta">
		<div class="entry-actions">
			<?php if ( comments_open() || get_comments_number() !== 0 ) { ?>
			<div class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'sovereignty' ), __( '1 Comment', 'sovereignty' ), __( '% Comments', 'sovereignty' ) ); ?></div>
			<?php } ?>
			<?php get_template_part( 'template-parts/entry', 'share' ); ?>
		</div>

		<?php dynamic_sidebar( 'entry-meta' ); ?>
		<?php
		/**
		 * Fires in the entry footer after the entry meta sidebar.
		 */
		do_action( 'sovereignty_entry_footer' );
		?>
	</footer><!-- #entry-meta -->
<?php } else { ?>
	<footer class="entry-footer entry-meta">
		<?php if ( comments_open() || get_comments_number() !== 0 ) { ?>
		<div class="entry-actions">
			<div class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'sovereignty' ), __( '1 Comment', 'sovereignty' ), __( '% Comments', 'sovereignty' ) ); ?></div>
		</div>
		<?php } ?>
	</footer><!-- #entry-meta -->
<?php } ?>
