<?php
/*
 * translators: used between list items, there is a space after the comma
 */
$sovereignty_categories_list = get_the_category_list();
if ( $sovereignty_categories_list !== '' ) {
	?>
<div class="cat-links">
	<?php echo esc_html__( 'Categories', 'autonomie' ); ?>
	<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $sovereignty_categories_list contains safe HTML from get_the_category_list().
	echo $sovereignty_categories_list;
	?>
</div>
<?php } // End if categories. ?>

<?php
// translators: used between list items, there is a space after the comma.
$sovereignty_tags_list = get_the_tag_list( '<ul><li>', '</li><li>', '</li></ul>' );
if ( $sovereignty_tags_list ) {
	?>
<div class="tag-links" itemprop="keywords">
	<?php echo esc_html__( 'Tags', 'autonomie' ); ?>
	<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $sovereignty_tags_list contains safe HTML from get_the_tag_list().
	echo $sovereignty_tags_list;
	?>
</div>
	<?php
} // End if $sovereignty_tags_list.

