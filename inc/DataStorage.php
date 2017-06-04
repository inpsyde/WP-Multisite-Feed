<?php # -*- coding: utf-8 -*-

namespace Inpsyde\MultisiteFeed;

/**
 * Interface DataStorage
 *
 * Base Interface for things like Settings containers
 *
 * @package Inpsyde\MultisiteFeed
 */
interface DataStorage {

	public function get( $key, $default = null );

	public function set( $key, $value );

}