<?php # -*- coding: utf-8 -*-

namespace Inpsyde\MultisiteFeed\Cache\Incrementor;

class CallbackIncrementor implements Incrementor {

	/**
	 * @var
	 */
	private $get_callback;
	/**
	 * @var
	 */
	private $increase_callback;
	/**
	 * @var
	 */
	private $group_name;
	/**
	 * @var string
	 */
	private $incrementor_suffix = 'incr';

	public function __construct( $group_name, $get, $increase ) {

		$this->group_name        = $group_name;
		$this->get_callback      = $get;
		$this->increase_callback = $increase;
	}

	public function get() {

		return call_user_func( [ $this, 'get_callback' ], $this->get_incrementor_key() );
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

		return call_user_func( [ $this, 'increase_callback' ], $this->get_incrementor_key() );

	}
}
