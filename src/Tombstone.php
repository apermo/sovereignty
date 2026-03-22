<?php

declare(strict_types=1);

namespace Apermo\Sovereignty;

use WP_Post;

/**
 * HTTP 410 tombstone support for deleted content.
 *
 * Registers a 'sovereignty_tombstone' post status and serves
 * tombstone posts with HTTP 410 and minimal microformats2 markup.
 *
 * @package Sovereignty
 */
class Tombstone {

	/**
	 * Custom post status name.
	 *
	 * @var string
	 */
	public const STATUS = 'sovereignty_tombstone';

	/**
	 * Register the tombstone post status.
	 *
	 * @return void
	 */
	public static function register_post_status(): void {
		register_post_status(
			self::STATUS,
			[
				'label'                     => __( 'Tombstone (Gone)', 'sovereignty' ),
				'public'                    => true,
				'exclude_from_search'       => true,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				/* translators: %s: number of tombstone posts */
				'label_count'               => _n_noop( 'Tombstone <span class="count">(%s)</span>', 'Tombstone <span class="count">(%s)</span>', 'sovereignty' ),
			],
		);
	}

	/**
	 * Send HTTP 410 and load tombstone template for tombstone posts.
	 *
	 * @return void
	 */
	public static function template_redirect(): void {
		if ( ! is_singular() ) {
			return;
		}

		$post = get_post(); // phpcs:ignore Apermo.WordPress.ImplicitPostFunction -- Hook callback.

		if ( ! $post instanceof WP_Post ) {
			return;
		}

		if ( $post->post_status !== self::STATUS ) {
			return;
		}

		status_header( 410 );
		nocache_headers();

		// Load tombstone template if it exists, otherwise use a minimal fallback.
		$template = locate_template( 'tombstone.php' );
		if ( $template !== '' ) {
			require $template; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
		} else {
			get_header();
			?>
			<main id="primary" class="h-entry">
				<article class="entry-content e-content">
					<p class="p-content"><?php esc_html_e( 'This content has been removed.', 'sovereignty' ); ?></p>
					<time class="dt-updated" datetime="<?php echo esc_attr( get_the_modified_date( 'c', $post ) ); ?>">
						<?php echo esc_html( get_the_modified_date( '', $post ) ); ?>
					</time>
				</article>
			</main>
			<?php
			get_footer();
		}
		exit();
	}

	/**
	 * Add tombstone status to the post status dropdown in the admin.
	 *
	 * @return void
	 */
	public static function admin_footer_edit(): void {
		$post = get_post(); // phpcs:ignore Apermo.WordPress.ImplicitPostFunction -- Admin hook callback.

		if ( ! $post instanceof WP_Post ) {
			return;
		}

		$selected = $post->post_status === self::STATUS ? ' selected="selected"' : '';
		$label    = __( 'Tombstone (Gone)', 'sovereignty' );

		?>
		<script>
			jQuery( document ).ready( function() {
				jQuery( 'select#post_status' ).append(
					'<option value="<?php echo esc_attr( self::STATUS ); ?>"<?php echo $selected; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Contains HTML attribute from checked source. ?>><?php echo esc_html( $label ); ?></option>'
				);
				<?php if ( $post->post_status === self::STATUS ) { ?>
				jQuery( '#post-status-display' ).text( '<?php echo esc_js( $label ); ?>' );
				<?php } ?>
			});
		</script>
		<?php
	}
}
