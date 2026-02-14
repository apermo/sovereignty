<header class="entry-header">
	<div class="entry-header-wrapper">
		<div class="entry-meta post-format">
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- apply_filters() output contains safe HTML.
			echo apply_filters(
				'autonomie_post_format',
				sprintf(
					'<a class="entry-format entry-format-%s entry-type-%s" href="%s">%s</a>',
					autonomie_get_post_format(),
					get_post_type(),
					esc_url( autonomie_get_post_format_link( autonomie_get_post_format() ) ),
					autonomie_get_post_format_string()
				)
			);
			?>
		</div>

		<?php
		if ( ! in_array( get_post_format(), [ 'aside', 'quote', 'status' ], true ) && ! empty( get_the_title() ) ) :
			if ( is_singular() ) {
				// phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps
				$title_element = 'h1';
			} else {
				// phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps
				$title_element = 'h2';
			}
			?>
		<?php // phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps, WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<<?php echo esc_html( $title_element ); ?> class="entry-title p-name" itemprop="name headline">
			<?php // translators: %s: Post title. ?>
			<a href="<?php the_permalink(); ?>" class="u-url url" title="<?php printf( esc_attr__( 'Permalink to %s', 'autonomie' ), the_title_attribute( [ 'echo' => false ] ) ); ?>" rel="bookmark" itemprop="url">
				<?php the_title(); ?>
			</a>
		<?php // phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps, WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</<?php echo esc_html( $title_element ); ?>>
		<?php endif; ?>

		<?php // if ( ! is_singular() ) : ?>
		<div class="entry-meta">
			<?php autonomie_posted_by(); ?> <span class="sep"> · </span> <?php autonomie_posted_on(); ?> <span class="sep"> · </span> <?php autonomie_reading_time(); ?>
		</div>
		<?php // endif; ?>
	</div>
</header><!-- .entry-header -->

<?php do_action( 'autonomie_before_entry_content' ); ?>
