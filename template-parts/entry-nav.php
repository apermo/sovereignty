<div id="post-nav">
	<?php
	// phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps
	$prev_post = get_previous_post( true );
	// phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps
	if ( $prev_post ) {
		$args = [
			'posts_per_page' => 1,
			// phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps, Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
			'include' => [ $prev_post->ID ],
		];
		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps
		$prev_post = get_posts( $args );
		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps, WordPress.WP.GlobalVariablesOverride.Prohibited
		foreach ( $prev_post as $post ) {
			setup_postdata( $post );
			?>
		<div class="previous-post" style="background-image: url( <?php echo esc_url( get_the_post_thumbnail_url( $post->ID, 'medium' ) ); ?>">
			<a class="previous" href="<?php the_permalink(); ?>">&laquo; Previous Story</a>
			<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
			<small><?php the_date( 'F j, Y' ); ?></small>
		</div>
			<?php
				wp_reset_postdata();
		} //end foreach
	} // end if

	// phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps
	$next_post = get_next_post( true );

	// phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps
	if ( $next_post ) {
		$args = [
			'posts_per_page' => 1,
			// phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps, Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
			'include' => [ $next_post->ID ],
		];
		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps
		$next_post = get_posts( $args );
		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps, WordPress.WP.GlobalVariablesOverride.Prohibited
		foreach ( $next_post as $post ) {
			setup_postdata( $post );
			?>
		<div class="next-post" style="background-image: url( <?php echo esc_url( get_the_post_thumbnail_url( $post->ID, 'medium' ) ); ?>">
			<a class="next" href="<?php the_permalink(); ?>">Next Story &raquo;</a>
			<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
			<small><?php the_date( 'F j, Y' ); ?></small>
		</div>
			<?php
			wp_reset_postdata();
		} //end foreach
	} // end if
	?>
</div>
