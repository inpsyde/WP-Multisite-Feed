<?php # -*- coding: utf-8 -*-

namespace Inpsyde\MultisiteFeed;

interface FeedItem {

	/**
	 * @return string
	 */
	public function get_guid();

	/**
	 * @return string
	 */
	public function get_date_created();

	/**
	 * @return string
	 */
	public function get_title();

	/**
	 * @return string
	 */
	public function get_permalink();

	/**
	 * @return string
	 */
	public function get_author();

	/**
	 * @return string
	 */
	public function get_author_avatar_url();

	/**
	 * @return string
	 */
	public function get_comments_link();

	/**
	 * @return int
	 */
	public function get_comments_number();

	/**
	 * @return string
	 */
	public function get_excerpt();

	/**
	 * @return string
	 */
	public function get_content();

	/**
	 * @return string
	 */
	public function get_enclosure();

	/**
	 * @return string
	 */
	public function wp_rss2_item();

	/**
	 * @return string
	 */
	public function wp_rss2_category();

	/**
	 * @return bool
	 */
	public function has_post_thumbnail();

	/**
	 * @return string
	 */
	public function get_post_thumbnail_url();

	/**
	 * @return string
	 */
	public function get_post_thumbnail_title();
}