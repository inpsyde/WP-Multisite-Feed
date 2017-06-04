<?php # -*- coding: utf-8 -*-

namespace Inpsyde\MultisiteFeed;

class NetworkPostFeedItem extends PostFeedItem {

	/**
	 * NetworkPostFeedItem constructor.
	 *
	 * @param $blog_id
	 * @param $post_id
	 */
	public function __construct( $blog_id, $post_id ) {

		switch_to_blog( $blog_id );
		parent::__construct( $post_id );
		restore_current_blog();
	}
}