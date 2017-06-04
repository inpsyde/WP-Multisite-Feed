<?php # -*- coding: utf-8 -*-

namespace Inpsyde\MultisiteFeed;

interface FeedItemProvider {

	/**
	 * @return FeedItem[]
	 */
	public function get_items();
}