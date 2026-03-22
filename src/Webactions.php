<?php

declare(strict_types=1);

namespace Apermo\Sovereignty;

/**
 * IndieWeb web actions for comment reply links and forms.
 *
 * Wraps comment links and forms with indie-action elements
 * to support IndieWeb reply/like/repost interactions.
 *
 * @package Sovereignty
 */
class Webactions {

	/**
	 * Wrap comment reply link in an indie-action element.
	 *
	 * @param string $link    The HTML markup for the comment reply link.
	 * @param array  $args    An array of arguments overriding the defaults.
	 * @param object $comment The comment object.
	 * @param object $post    The post object.
	 *
	 * @return string The wrapped reply link.
	 */
	public static function comment_reply_link( string $link, array $args, object $comment, object $post ): string {
		$permalink = get_permalink( $post->ID );
		return '<indie-action do="reply" with="' . esc_url( add_query_arg( 'replytocom', $comment->comment_ID, $permalink ) ) . '">' . $link . '</indie-action>';
	}

	/**
	 * Open indie-action element before comment form.
	 *
	 * @return void
	 */
	public static function comment_form_before(): void {
		$post      = get_queried_object();
		$permalink = get_permalink( $post->ID );
		echo '<indie-action do="reply" with="' . esc_url( $permalink ) . '">';
	}

	/**
	 * Close indie-action element after comment form.
	 *
	 * @return void
	 */
	public static function comment_form_after(): void {
		echo '</indie-action>';
	}
}
