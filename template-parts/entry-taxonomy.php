<?php
/*
 * translators: used between list items, there is a space after the comma
 */
$categories_list = get_the_category_list();
if ( $categories_list ) :
	?>
<div class="cat-links">
	<?php echo esc_html__( 'Categories', 'autonomie' ); ?>
	<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $categories_list contains safe HTML from get_the_category_list().
	echo $categories_list;
	?>
</div>
<?php endif; // End if categories ?>

<?php
/*
 * translators: used between list items, there is a space after the comma
 */
$tags_list = get_the_tag_list( '<ul><li>', '</li><li>', '</li></ul>' );
if ( $tags_list ) :
	?>
<div class="tag-links" itemprop="keywords">
	<?php echo esc_html__( 'Tags', 'autonomie' ); ?>
	<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $tags_list contains safe HTML from get_the_tag_list().
	echo $tags_list;
	?>
</div>
<?php endif; // End if $tags_list ?>
