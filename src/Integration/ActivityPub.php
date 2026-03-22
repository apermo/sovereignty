<?php

declare(strict_types=1);

namespace Apermo\Sovereignty\Integration;

use Activitypub\Webfinger;

/**
 * ActivityPub integration: author metadata and follower counts.
 *
 * @link https://github.com/pfefferle/wordpress-activitypub
 *
 * @package Sovereignty
 */
class ActivityPub {

	/**
	 * Register integration hooks.
	 *
	 * @return void
	 */
	public static function register(): void {
		add_filter( 'sovereignty_archive_author_meta', [ self::class, 'archive_author_meta' ], 10, 2 );
		add_filter( 'sovereignty_archive_author_followers', [ self::class, 'followers' ], 10, 2 );
	}

	/**
	 * Add fediverse follow action to author archive meta.
	 *
	 * @param array $meta      The meta array.
	 * @param int   $author_id The author id.
	 *
	 * @return array The filtered meta array.
	 */
	public static function archive_author_meta( array $meta, int $author_id ): array {
		$meta[] = \sprintf(
			// translators: how to follow an author on the fediverse, 1: the author archive URL, 2: the author's webfinger resource.
			__( '<indie-action do="follow" with="%1$s">Follow <code>%2$s</code> (fediverse)</indie-action>', 'sovereignty' ),
			get_author_posts_url( $author_id ),
			Webfinger::get_user_resource( $author_id ),
		);

		return $meta;
	}

	/**
	 * Add ActivityPub followers to the follower count.
	 *
	 * @param int $followers The follower counter.
	 * @param int $author_id The author id.
	 *
	 * @return int The filtered counter.
	 */
	public static function followers( int $followers, int $author_id ): int {
		$activitypub_followers = get_user_option( 'activitypub_followers', $author_id );

		if ( \is_array( $activitypub_followers ) ) {
			$activitypub_followers = \count( $activitypub_followers );
		} else {
			$activitypub_followers = 0;
		}

		return $followers + $activitypub_followers;
	}
}
