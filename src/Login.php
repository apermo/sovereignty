<?php

declare(strict_types=1);

namespace Apermo\Sovereignty;

/**
 * Login page customization.
 *
 * @package Sovereignty
 */
class Login {

	/**
	 * Use the site icon as the login page logo.
	 *
	 * @return void
	 */
	public static function logo(): void {
		if ( ! has_site_icon() ) {
			return;
		}

		?>
		<style type="text/css">
			body.login div#login h1 a {
				background-image: url( <?php echo esc_url( get_site_icon_url( 84 ) ); ?> );
			}
		</style>
		<?php
	}
}
