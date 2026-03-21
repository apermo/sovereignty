<div id="post-nav">
	<?php
	$sovereignty_prev_post = get_previous_post( true );
	if ( $sovereignty_prev_post instanceof WP_Post ) {
		$sovereignty_args = [
			'posts_per_page' => 1,
			'include' => [ $sovereignty_prev_post->ID ],
		];
		$sovereignty_prev_post = get_posts( $sovereignty_args );
		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		foreach ( $sovereignty_prev_post as $post ) {
			setup_postdata( $post );
			?>
		<div class="previous-post" style="background-image: url( <?php echo esc_url( get_the_post_thumbnail_url( $post->ID, 'medium' ) ); ?>">
			<a class="previous" href="<?php the_permalink(); ?>"><span aria-hidden="true">&larr;</span> <?php esc_html_e( 'Previous Story', 'autonomie' ); ?></a>
			<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
			<small><?php the_date( 'F j, Y' ); ?></small>
		</div>
			<?php
				wp_reset_postdata();
		} //end foreach
	} // end if

	$sovereignty_next_post = get_next_post( true );

	if ( $sovereignty_next_post instanceof WP_Post ) {
		$sovereignty_args = [
			'posts_per_page' => 1,
			'include' => [ $sovereignty_next_post->ID ],
		];
		$sovereignty_next_post = get_posts( $sovereignty_args );
		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		foreach ( $sovereignty_next_post as $post ) {
			setup_postdata( $post );
			?>
		<div class="next-post" style="background-image: url( <?php echo esc_url( get_the_post_thumbnail_url( $post->ID, 'medium' ) ); ?>">
			<a class="next" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Next Story', 'autonomie' ); ?> <span aria-hidden="true">&rarr;</span></a>
			<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
			<small><?php the_date( 'F j, Y' ); ?></small>
		</div>
			<?php
			wp_reset_postdata();
		} //end foreach
	} // end if
	?>
</div>
