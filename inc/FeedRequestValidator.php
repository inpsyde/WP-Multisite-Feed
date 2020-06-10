<?php # -*- coding: utf-8 -*-

namespace Inpsyde\MultisiteFeed;

class FeedRequestValidator implements RequestValidator {

	/**
	 * @var DataStorage
	 */
	private $settings;

	/**
	 * FeedRequestValidator constructor.
	 *
	 * @param DataStorage $settings
	 */
	public function __construct( DataStorage $settings ) {

		$this->settings = $settings;
	}

	/**
	 * Check if a multifeed URL is requested
	 *
	 * @return bool
	 */
	public function validate() {

		$slug = $this->settings->get( 'url_slug' );

		if ( ! $slug ) {
			return false;
		}

		$request_uri = filter_input( INPUT_SERVER, 'REQUEST_URI' );
		if ( ! $request_uri ) {
			return false;
		}

		$parsed_url = parse_url( $request_uri );
		if ( ! $parsed_url['path'] ) {
			return false;
		}

		$request_uri = trim( $parsed_url['path'], '/' );
		$parts       = explode( '/', $request_uri );

		if ( ! $parts ) {
			return false;
		}

		return ( end( $parts ) === $slug );
	}
}
