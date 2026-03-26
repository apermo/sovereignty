<address class="author p-author vcard hcard h-card">
	<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_avatar() returns safe HTML.
	echo get_avatar( get_the_author_meta( 'ID' ), 100 );
	?>
	<a class="url uid u-url u-uid fn p-name" href="<?php echo esc_url( get_author_posts_url( (int) get_the_author_meta( 'ID' ) ) ); ?>">
		<?php echo esc_html( get_the_author() ); ?>
	</a>
	<div class="note e-note"><?php echo wp_kses_post( get_the_author_meta( 'description' ) ); ?></div>
	<a class="subscribe" href="<?php echo esc_url( get_author_feed_link( (int) get_the_author_meta( 'ID' ) ) ); ?>"><i class="openwebicons-feed"></i> <?php esc_html_e( 'Subscribe to author feed', 'sovereignty' ); ?></a>
</address>
