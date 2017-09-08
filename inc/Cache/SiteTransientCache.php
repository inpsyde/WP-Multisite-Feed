<?php # -*- coding: utf-8 -*-

namespace Inpsyde\MultisiteFeed\Cache;

use Inpsyde\MultisiteFeed\Cache\Incrementor\Incrementor;
use Inpsyde\MultisiteFeed\Cache\Incrementor\SiteTransientIncrementor;

class SiteTransientCache extends IncrementorBasedCacheGroup {

	private $expiration;

	public function __construct( $group_name, $expiration = null, Incrementor $incrementor = null ) {

		if ( null !== $expiration ) {
			$this->expiration = (int) $expiration * MINUTE_IN_SECONDS;

		} else {
			$this->expiration = HOUR_IN_SECONDS * 10;

		}
		$incrementor = $incrementor ?: new SiteTransientIncrementor( $group_name );
		parent::__construct( $group_name, $incrementor );
	}

	/**
	 * Store a value in a transient.
	 *
	 * @param      $key
	 * @param      $value
	 * @param null|int $expiration
	 */
	public function set( $key, $value, $expiration = null ) {

        $expiration = $expiration ?: $this->expiration;
		\set_site_transient( $this->get_group_name() . $key, $value, $expiration );
	}

	/**
	 * Checks if a transient value is present.
	 *
	 * @param $key
	 *
	 * @return bool
	 */
	public function has( $key ) {

		return (bool) $this->get( $key );
	}

	/**
	 * Fetch a transient value.
	 *
	 * @param $key
	 *
	 * @return mixed
	 */
	public function get( $key ) {

		return \get_site_transient( $this->get_group_name() . $key );
	}
}
