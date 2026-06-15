<?php
use Apermo\Sovereignty\Semantics;
use Apermo\Sovereignty\Template\Functions;
?>

<?php if ( Functions::show_page_banner() && ( Functions::has_archive_title() || Functions::has_archive_description() ) ) { ?>
<div class="page-banner">
	<div class="page-branding">
		<?php if ( Functions::has_archive_title() ) { ?>
		<h1 id="page-title"<?php Semantics::output( 'page-title' ); ?>><?php Functions::render_archive_title(); ?></h1>
		<?php } ?>
		<?php if ( Functions::has_archive_description() ) { ?>
		<div id="page-description"<?php Semantics::output( 'page-description' ); ?>><?php Functions::render_archive_description(); ?></div>
		<?php } ?>
	</div>
</div>
<?php } ?>
