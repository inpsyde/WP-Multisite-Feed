<?php # -*- coding: utf-8 -*-

namespace Inpsyde\MultisiteFeed;

interface Renderer {

	/**
	 * @param FeedItem[] $feed_items
	 *
	 * @return mixed
	 */
	public function render( array $feed_items );
}