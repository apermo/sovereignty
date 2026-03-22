<?php
use Apermo\Sovereignty\Semantics;
use Apermo\Sovereignty\Template\Functions;
?>

<?php if ( Functions::show_page_banner() ) { ?>
<div class="page-banner">
	<?php if ( ! is_singular() ) { ?>
	<div class="page-branding">
		<?php if ( Functions::get_the_archive_title() !== '' ) { ?>
		<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Functions::get_the_archive_title() may contain HTML. ?>
		<h1 id="page-title"<?php Semantics::output( 'page-title' ); ?>><?php echo Functions::get_the_archive_title(); ?></h1>
		<?php } ?>
		<?php if ( Functions::get_the_archive_description() !== '' ) { ?>
		<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Functions::get_the_archive_description() contains HTML. ?>
		<div id="page-description"<?php Semantics::output( 'page-description' ); ?>><?php echo Functions::get_the_archive_description(); ?></div>
		<?php } ?>
	</div>
		<?php printf( '<link itemprop="mainEntityOfPage" href="%s" />', esc_url( get_self_link() ) ); ?>
	<?php } ?>
</div>
<?php } ?>
