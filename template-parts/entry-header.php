<?php
use Apermo\Sovereignty\Template\Post_Format;
use Apermo\Sovereignty\Template\Tags;

global $post;
?>

<header class="entry-header">
	<div class="entry-header-wrapper">
		<div class="entry-meta post-format">
			<?php
			/**
			 * Filters the post format link HTML.
			 *
			 * @param string $html The post format link HTML.
			 *
			 * @return string The filtered HTML.
			 */
			echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- apply_filters() output contains safe HTML.
				'sovereignty_post_format',
				sprintf(
					'<a class="entry-format entry-format-%s entry-type-%s" href="%s">%s</a>',
					Post_Format::get_format( $post ),
					get_post_type( $post ),
					esc_url( Post_Format::get_format_link( Post_Format::get_format( $post ), $post ) ),
					Post_Format::get_format_string( $post ),
				),
			);
			?>
		</div>

		<?php
		if ( ! in_array( get_post_format( $post ), [ 'aside', 'quote', 'status' ], true ) && ! empty( get_the_title() ) ) {
			?>
		<<?php echo esc_html( Tags::entry_title_tag() ); ?> class="entry-title p-name" itemprop="name headline">
			<?php // translators: %s: Post title. ?>
			<a href="<?php the_permalink(); ?>" class="u-url url" title="<?php printf( esc_attr__( 'Permalink to %s', 'sovereignty' ), the_title_attribute( [ 'echo' => false ] ) ); ?>" rel="bookmark" itemprop="url">
				<?php the_title(); ?>
			</a>
		</<?php echo esc_html( Tags::entry_title_tag() ); ?>>
		<?php } ?>

		<div class="entry-meta">
			<?php Tags::posted_by( $post ); ?> <span class="sep"> · </span> <?php Tags::posted_on( $post ); ?> <span class="sep"> · </span> <?php Tags::reading_time( $post ); ?>
		</div>
	</div>
</header><!-- .entry-header -->

<?php
/**
 * Fires before the entry content.
 */
do_action( 'sovereignty_before_entry_content' );
?>
