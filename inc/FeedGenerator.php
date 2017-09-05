<?php # -*- coding: utf-8 -*-

namespace Inpsyde\MultisiteFeed;

use Inpsyde\MultisiteFeed\Cache\CacheHandler;

/**
 * Class FeedGenerator
 *
 * @package Inpsyde\MultisiteFeed
 */
class FeedGenerator {

	/**
	 * @var CacheHandler
	 */
	private $cache;
	/**
	 * @var DataStorage
	 */
	private $settings;
	/**
	 * @var Renderer
	 */
	private $renderer;
	/**
	 * @var FeedItemProvider
	 */
	private $item_provider;

	/**
	 * FeedGenerator constructor.
	 *
	 * @param DataStorage      $settings
	 * @param FeedItemProvider $item_provider
	 * @param Renderer         $renderer
	 * @param CacheHandler     $cache
	 */
	public function __construct(
		DataStorage $settings,
		FeedItemProvider $item_provider,
		Renderer $renderer,
		CacheHandler $cache
	) {

		$this->settings      = $settings;
		$this->item_provider = $item_provider;
		$this->cache         = $cache;
		$this->renderer      = $renderer;
	}

	/**
	 * Print out feed XML. Use cache if available.
	 *
	 * @return void
	 */
	public function display_feed() {

		$cache_key = $this->get_cache_key();
		$out       = false;

		// Deactivate Caching for Debugging
        if ($cache_enabled = $this->is_cache_enabled()) {
            $out = $this->cache->get($cache_key);
        }

        if ( ! $out) {
            $feed_items = $this->item_provider->get_items();
            $out        = $this->renderer->render($feed_items);
            
            if ($cache_enabled) {
                $this->cache->set($cache_key, $out);

            }
        }

		header( 'Content-Type: ' . feed_content_type( 'rss-http' ) . '; charset=' . get_option( 'blog_charset' ),
			true );
		echo $out;
	}

	private function get_cache_key() {

		return 'feed_' . md5( serialize( $_REQUEST ) );
	}

	private function is_cache_enabled() {

		$wp_debug = ( defined( 'WP_DEBUG' ) && WP_DEBUG );
		if ( $wp_debug ) {
			return false;
		}

		$cache_expiry = (int) $this->settings->get( OptionsKeys::CACHE_EXPIRY );
		if ( $cache_expiry === 0 ) {
			return false;
		}

		return true;

	}

}
