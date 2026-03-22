<?php
use Apermo\Sovereignty\Semantics;
use Apermo\Sovereignty\Template\Functions;
?>

<div class="page-banner">
	<div class="page-branding author p-author h-card">
		<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_avatar() returns safe HTML. ?>
		<?php echo get_avatar( get_the_author_meta( 'ID' ), 150 ); ?>
		<h1 id="page-title"<?php Semantics::output( 'page-title' ); ?>><?php echo esc_html( get_the_author_meta( 'display_name' ) ); ?></h1>
		<div id="page-description"<?php Semantics::output( 'page-description' ); ?>><?php echo wp_kses_post( get_the_author_meta( 'description' ) ); ?></div>
		<div id="page-meta"><?php Functions::render_archive_author_meta(); ?></div>
	</div>
</div>
