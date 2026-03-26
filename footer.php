<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package Sovereignty
 * @since Sovereignty 1.0.0
 */
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
			<?php
			// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- Contains intentional HTML links for attribution.
			printf(
				// translators: %1$s: Link to WordPress, %2$s: Link to Autonomie theme.
				__( 'This site is powered by %1$s and styled with the %2$s theme', 'sovereignty' ),
				'<a href="https://wordpress.org/" rel="generator">WordPress</a>',
				'<a href="https://notiz.blog/projects/autonomie/">Autonomie</a>',
			);
			// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</div>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
