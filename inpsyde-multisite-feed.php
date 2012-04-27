<?php
/*
Plugin Name: Inpsyde Multisite Feed
Plugin URI: 
Description: Consolidates all network feeds into one.
Version: 1.0alpha
Author: Inpsyde GmbH
Author URI: 
License: GPL
*/

$correct_php_version = version_compare( phpversion(), "5.3", ">=" );

if ( ! $correct_php_version ) {
	echo "Inpsyde Inpsyde Multisite Feed Plugin requires <strong>PHP 5.3</strong> or higher.<br>";
	echo "You are running PHP " . phpversion();
	exit;
}

require_once 'plugin.php';