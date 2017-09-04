<?php

namespace Inpsyde\MultisiteFeed;

use Inpsyde\MultisiteFeed\Cache\CacheHandler;

class Plugin {

	/**
	 * @var DataStorage
	 */
	private $settings;
	/**
	 * @var CacheHandler
	 */
	private $cache;
	/**
	 * @var RequestValidator
	 */
	private $request;

	/**
	 * Plugin constructor.
	 *
	 * @param DataStorage      $settings
	 * @param RequestValidator $request
	 * @param CacheHandler     $cache
	 */
	public function __construct(
		DataStorage $settings,
		RequestValidator $request,
		CacheHandler $cache
	) {

		$this->settings = $settings;
		$this->request  = $request;
		$this->cache    = $cache;
	}

	/**
	 * Return feed url.
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	public static function get_feed_url( array $args = [] ) {

		$slug = DI::instance( Settings::class )->get( OptionsKeys::URL_SLUG );
		$url  = home_url( $slug );
		$url  = add_query_arg( $args, $url );

		return apply_filters( 'inpsmf_feed_url', $url );
	}

	/**
	 * Setup WordPress hooks and initialize plugin
	 */
	public function init() {

		if ( is_admin() ) {
			if ( is_network_admin() ) {
				DI::instance( SettingsPage::class )->init();
				// Load translation file
				add_action( 'load-settings_page_inpsyde-multisite-feed-page', [ $this, 'localize_plugin' ] );
			}
			// invalidate cache when necessary
			add_action( 'admin_init', function () {

				// network activation check
				if ( is_network_admin() ) {

				}

				$actions = [
					'publish_post',
					'deleted_post',
					'save_post',
					'trashed_post',
					'private_to_published',
					'inpsmf_update_settings',
				];

				foreach ( $actions as $action ) {
                    add_action($action, function () {

                        $this->settings->set('last_modified', current_time('mysql'));
                        $this->cache->flush();
                    });
				}
			}
			);
		}

		// hijack feed into WordPress
		add_action( 'init', function () {

			if ( $this->request->validate() ) {
				do_action( Hooks::ACTION_MULTIFEED_REQUEST );
				DI::instance( FeedGenerator::class )
				  ->display_feed();
				exit;
			}
		}
		);
	}

	/**
	 * Load plugin translation
	 *
	 * @since   06/06/2013
	 * @return  void
	 */
	public function localize_plugin() {

		load_plugin_textdomain(
			'inps-multisite-feed',
			false,
			str_replace( 'inc', '', dirname( plugin_basename( __FILE__ ) ) ) . 'languages'
		);
	}

}
