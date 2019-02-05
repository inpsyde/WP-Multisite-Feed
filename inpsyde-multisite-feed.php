<?php
/**
 * Plugin Name: Inpsyde Multisite Feed
 * Plugin URI:  http://wordpress.org/extend/plugins/wp-multisite-feed/
 * Description: Consolidates all network feeds into one.
 * Version:     1.1.0
 * Author:      Inpsyde GmbH
 * Author URI:  https://inpsyde.com/
 * License:     GPLv3+
 * Text Domain: inpsyde-multisite-feed
 * Domain Path: /languages
 * Network:     true
 */

namespace Inpsyde\MultisiteFeed;

add_action( 'plugins_loaded', function () {

	$admin_notice = function ( $message ) {

		add_action( 'admin_notices', function () use ( $message ) {

			$class = 'notice notice-error';
			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
		} );
	};

	if ( ! PHP_VERSION_ID >= 50600 ) {
		$admin_notice( 'Inpsyde Multisite Feed Plugin requires <strong>PHP 5.6</strong> or higher.<br>You are running PHP ' . PHP_VERSION );

		return;
	}

	/**
	 * Check if we're already autoloaded by some external autoloader.
	 * If not, load our own
	 */
	if ( ! class_exists( 'Inpsyde\\MultisiteFeed\\Plugin' ) ) {

		if ( file_exists( $autoloader = __DIR__ . '/vendor/autoload.php' ) ) {
			/** @noinspection PhpIncludeInspection */
			require $autoloader;
		} else {
			$admin_notice( 'Could not find a working autoloader for Inpsyde Multisite Feed.' );

			return;
		}
	}

	DI::instance( Plugin::class )->init();
} );


