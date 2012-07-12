<?php
/*
Plugin Name: Inpsyde Multisite Feed
Plugin URI:  http://wordpress.org/extend/plugins/wp-multisite-feed/
Description: Consolidates all network feeds into one.
Version:     1.0.0
Author:      Inpsyde GmbH
Author URI:  http://inpsyde.com/
License:     GPLv3
*/

$correct_php_version = version_compare( phpversion(), "5.3", ">=" );

if ( ! $correct_php_version ) {
	echo "Inpsyde Inpsyde Multisite Feed Plugin requires <strong>PHP 5.3</strong> or higher.<br>";
	echo "You are running PHP " . phpversion();
	exit;
}

require_once 'inc/plugin.php';
