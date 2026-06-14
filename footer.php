<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package Sovereignty
 * @since Sovereignty 1.0.0
 */

use Apermo\Sovereignty\Template\Tags;

?>
	<footer id="colophon">
		<?php get_sidebar(); ?>

		<div id="site-generator">
			<?php
			/**
			 * Fires in the footer before the credits.
			 */
			do_action( 'sovereignty_credits' );
			?>
			<?php Tags::separator(); ?>
			<div class="site-info">
				<?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- Intentional attribution links. ?>
				<p>
					<?php
					printf(
						/* translators: %1$s: WordPress link, %2$s: Sovereignty theme link, %3$s: Autonomie theme link. */
						__( 'This site is powered by %1$s and styled with %2$s, based on %3$s', 'sovereignty' ),
						'<a href="https://wordpress.org/" rel="generator">WordPress</a>',
						'<a href="https://github.com/apermo/sovereignty">Sovereignty</a>',
						'<a href="https://notiz.blog/projects/autonomie/">Autonomie</a>',
					);
					?>
				</p>
				<?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
