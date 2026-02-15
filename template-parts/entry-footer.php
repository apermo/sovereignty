<?php if ( is_singular() || is_attachment() ) : ?>
	<footer class="entry-footer entry-meta">
		<div class="entry-actions">
			<?php if ( comments_open() || get_comments_number() !== 0 ) : ?>
			<indie-action do="reply" with="<?php the_permalink(); ?>"><div class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'autonomie' ), __( '1 Comment', 'autonomie' ), __( '% Comments', 'autonomie' ) ); ?></div></indie-action>
			<?php endif; ?>
			<?php get_template_part( 'template-parts/entry', 'share' ); ?>
		</div>

		<?php dynamic_sidebar( 'entry-meta' ); ?>
		<?php do_action( 'autonomie_entry_footer' ); ?>
	</footer><!-- #entry-meta -->
<?php else : ?>
	<footer class="entry-footer entry-meta">
		<?php if ( comments_open() || get_comments_number() !== 0 ) : ?>
		<div class="entry-actions">
			<indie-action do="reply" with="<?php the_permalink(); ?>"><div class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'autonomie' ), __( '1 Comment', 'autonomie' ), __( '% Comments', 'autonomie' ) ); ?></div></indie-action>
		<div>
		<?php endif; ?>
	</footer><!-- #entry-meta -->
<?php endif; ?>
