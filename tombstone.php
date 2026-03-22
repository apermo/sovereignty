<?php
/**
 * Template for tombstone (HTTP 410) posts.
 *
 * Displays a minimal removal notice with microformats2 markup.
 *
 * @package Sovereignty
 */

use Apermo\Sovereignty\Semantics;
use Apermo\Sovereignty\Template\Tags;

get_header();

global $post;
?>

<main id="primary" <?php Tags::main_class(); ?><?php Semantics::output( 'main' ); ?>>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'h-entry' ); ?>>
		<header class="entry-header">
			<h1 class="entry-title p-name"><?php esc_html_e( 'Gone', 'sovereignty' ); ?></h1>
		</header>

		<div class="entry-content e-content">
			<p class="p-content"><?php esc_html_e( 'This content has been removed.', 'sovereignty' ); ?></p>
			<p>
				<time class="dt-updated" datetime="<?php echo esc_attr( get_the_modified_date( 'c', $post ) ); ?>">
					<?php
					printf(
						/* translators: %s: date the content was removed */
						esc_html__( 'Removed on %s', 'sovereignty' ),
						esc_html( get_the_modified_date( '', $post ) ),
					);
					?>
				</time>
			</p>
		</div>
	</article>
</main>

<?php
get_footer();
