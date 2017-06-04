<?php # -*- coding: utf-8 -*-

namespace Inpsyde\MultisiteFeed\Cache;


abstract class CacheGroup implements CacheHandler {

	/**
	 * @var string
	 */
	private $group_name;
	/**
	 * @var string
	 */
	private $incrementor_suffix = 'incr';

	/**
	 * Cache constructor.
	 *
	 * @param string $group_name
	 */
	public function __construct( $group_name ) {

		$this->group_name = $group_name;
	}

	/**
	 * Increment the incrementor and thereby invalidate the cache.
	 */
	public function flush() {

		$this->increase_incrementor();
	}

	private function increase_incrementor() {

		$incrementor_value = time();
		$this->set_incrementor_value( $incrementor_value );

		return $incrementor_value;
	}

	protected abstract function set_incrementor_value( $incrementor_value );

	/**
	 * Returns the incrementor key.
	 *
	 * @return string
	 */
	protected function get_incrementor_key() {

		return $this->group_name . '_' . $this->incrementor_suffix;

	}

	/**
	 * Returns the salted cache group.
	 *
	 * @return string
	 */
	protected function get_group_name() {

		return $this->group_name . $this->get_incrementor();
	}

	/**
	 *
	 * @return bool|int|mixed
	 */
	private function get_incrementor() {

		$incrementor_value = $this->get_incrementor_value();

		if ( false === $incrementor_value ) {
			$incrementor_value = $this->increase_incrementor();
		}

		return $incrementor_value;
	}

	protected abstract function get_incrementor_value();
}