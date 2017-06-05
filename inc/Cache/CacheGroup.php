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
	private $incrementor;

	/**
	 * Cache constructor.
	 *
	 * @param string           $group_name
	 * @param Incrementor|null $incrementor
	 */
	public function __construct( $group_name, Incrementor $incrementor ) {

		$this->incrementor = $incrementor;
		$this->group_name  = $group_name;
	}

	/**
	 * Increment the incrementor and thereby invalidate the cache.
	 */
	public function flush() {

		$this->incrementor->increase();
	}

	/**
	 * Returns the salted cache group.
	 *
	 * @return string
	 */
	protected function get_group_name() {

		return $this->group_name . $this->incrementor->get();
	}
}