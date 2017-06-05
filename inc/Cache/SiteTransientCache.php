<?php # -*- coding: utf-8 -*-

namespace Inpsyde\MultisiteFeed\Cache;

class SiteTransientCache extends CacheGroup {

	private $expiration;

	public function __construct( $group_name, $expiration = null ) {

		if ( is_null( $expiration ) ) {
			$this->expiration = intval( $expiration * MINUTE_IN_SECONDS );

		} else {
			$this->expiration = HOUR_IN_SECONDS * 10;

		}

		parent::__construct( $group_name );
	}

	/**
	 * Store a value in a transient.
	 *
	 * @param $key
	 * @param $value
	 */
	public function set( $key, $value ) {

		$expiration = $this->expiration + MINUTE_IN_SECONDS * rand( 0, 60 );
		set_site_transient( $this->get_group_name() . $key, $value, $expiration );
	}

	/**
	 * Checks if a transient value is present.
	 *
	 * @param $key
	 *
	 * @return bool
	 */
	public function has( $key ) {

		return ! ! $this->get( $key );
	}

	/**
	 * Fetch a transient value.
	 *
	 * @param $key
	 *
	 * @return mixed
	 */
	public function get( $key ) {

		return get_site_transient( $this->get_group_name() . $key );
	}

	protected function get_incrementor_value() {

		return get_site_transient( $this->get_incrementor_key() );
	}

	protected function set_incrementor_value( $incrementor_value ) {

		set_site_transient( $this->get_incrementor_key(), $incrementor_value );
	}
}