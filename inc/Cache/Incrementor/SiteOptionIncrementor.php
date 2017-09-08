<?php # -*- coding: utf-8 -*-

namespace Inpsyde\MultisiteFeed\Cache\Incrementor;

class SiteOptionIncrementor implements Incrementor {

	/**
	 * @var
	 */
	private $group_name;

	/**
	 * @var string
	 */
	private $incrementor_suffix = 'incr';

	/**
	 * SiteTransientIncrementor constructor.
	 *
	 * @param string $group_name
	 */
	public function __construct( $group_name ) {

		$this->group_name = $group_name;
	}

	/**
	 * @return int
	 */
	public function get() {

		$incrementor_value = \get_site_option( $this->get_incrementor_key() );

		if ( false === $incrementor_value ) {
			$incrementor_value = $this->increase();
		}

		return $incrementor_value;
	}

	/**
	 * Returns the incrementor key.
	 *
	 * @return string
	 */
	protected function get_incrementor_key() {

		return $this->group_name . '_' . $this->incrementor_suffix;

	}

	public function increase() {

		$incrementor_value = time();
		\update_site_option( $this->get_incrementor_key(), $incrementor_value );

		return $incrementor_value;
	}
}
