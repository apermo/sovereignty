<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package Sovereignty
 * @since Sovereignty 1.0.0
 */

use Apermo\Sovereignty\Semantics;
use Apermo\Sovereignty\Template\Tags;

get_header(); ?>

		<main id="primary" <?php Tags::main_class(); ?><?php Semantics::output( 'main' ); ?>>

			<article id="post-0" class="post error offline">
				<header class="entry-header">
					<h1 class="entry-title p-name"><?php esc_html_e( 'Offline', 'sovereignty' ); ?></h1>
				</header>

				<div class="entry-content e-content">
					<p><?php wp_service_worker_error_message_placeholder(); // @phpstan-ignore function.notFound (PWA plugin) ?></p>
				</div><!-- .entry-content -->
			</article><!-- #post-0 -->

		</main><!-- #content -->

<?php
get_footer();
