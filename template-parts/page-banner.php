<?php if ( autonomie_show_page_banner() ) : ?>
<div class="page-banner">
	<?php if ( ! is_singular() ) : ?>
	<div class="page-branding">
		<?php if ( autonomie_get_the_archive_title() ) { ?>
		<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- autonomie_get_the_archive_title() may contain HTML. ?>
		<h1 id="page-title"<?php autonomie_semantics( 'page-title' ); ?>><?php echo autonomie_get_the_archive_title(); ?></h1>
		<?php } ?>
		<?php if ( autonomie_get_the_archive_description() ) { ?>
		<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- autonomie_get_the_archive_description() contains HTML. ?>
		<div id="page-description"<?php autonomie_semantics( 'page-description' ); ?>><?php echo autonomie_get_the_archive_description(); ?></div>
		<?php } ?>
	</div>
		<?php printf( '<link itemprop="mainEntityOfPage" href="%s" />', esc_url( get_self_link() ) ); ?>
	<?php endif; ?>
</div>
<?php endif; ?>
