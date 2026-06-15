<?php
use Apermo\Sovereignty\Template\Functions;

$sovereignty_author_id = (int) get_the_author_meta( 'ID' );
$sovereignty_avatar    = get_avatar( $sovereignty_author_id, 100 );
?>
<address class="author p-author vcard hcard h-card">
	<?php
	if ( is_string( $sovereignty_avatar ) && trim( $sovereignty_avatar ) !== '' ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_avatar() returns safe HTML.
		echo $sovereignty_avatar;
	}
	?>
	<a class="url uid u-url u-uid fn p-name" href="<?php echo esc_url( get_author_posts_url( $sovereignty_author_id ) ); ?>">
		<?php echo esc_html( Functions::author_name( $sovereignty_author_id ) ); ?>
	</a>
	<div class="note e-note"><?php echo wp_kses_post( get_the_author_meta( 'description' ) ); ?></div>
	<a class="subscribe" href="<?php echo esc_url( get_author_feed_link( $sovereignty_author_id ) ); ?>"><i class="openwebicons-feed"></i> <?php esc_html_e( 'Subscribe to author feed', 'sovereignty' ); ?></a>
</address>
