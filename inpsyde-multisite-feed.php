<?php
/**
 * Plugin Name: Inpsyde Multisite Feed
 * Plugin URI:  http://wordpress.org/extend/plugins/wp-multisite-feed/
 * Description: Consolidates all network feeds into one.
 * Version:     1.1.0
 * Author:      Inpsyde GmbH
 * Author URI:  http://inpsyde.com/
 * License:     GPLv2+
 * Network:     true
 */

namespace Inpsyde\MultisiteFeed;

add_action( 'plugins_loaded', function () {

	$error = function ( $message ) {

		add_action( 'admin_notices', function () use ( $message ) {

			$class = 'notice notice-error';
			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
		} );
	};

	if ( ! version_compare( phpversion(), '5.6', '>=' ) ) {
		$error( 'Inpsyde Multisite Feed Plugin requires <strong>PHP 5.6</strong> or higher.<br>You are running PHP ' . phpversion() );

		return;
	}

	/**
	 * Check if we're already autoloaded by some external autloader
	 * If not, load our own
	 */
	if ( ! class_exists( 'Inpsyde\\MultisiteFeed\\Plugin' ) ) {

		if ( file_exists( $autoloader = __DIR__ . '/vendor/autoload.php' ) ) {
			/** @noinspection PhpIncludeInspection */
			require $autoloader;
		} else {
			$error( 'Could not find a working autoloader for Inpsyde Multisite Feed.' );

			return;
		}
	}

	( DI::instance( Plugin::class ) )->init();
} );


