<?php # -*- coding: utf-8 -*-

namespace Inpsyde\MultisiteFeed;

class Settings implements DataStorage {

	const OPTION_KEY = 'inpsyde_multisitefeed';

	/**
	 * Convenience wrapper to access plugin options.
	 *
	 * @param  string $name    option name
	 * @param  mixed  $default fallback value if option does not exist
	 *
	 * @return mixed
	 */
	public function get( $name, $default = null ) {

		$options = \get_site_option( self::OPTION_KEY );

		return ( isset( $options[ $name ] ) ) ? $options[ $name ] : $default;
	}

	public function set( $key, $value ) {

		$options         = \get_site_option( self::OPTION_KEY );
		$options[ $key ] = $value;
		\update_site_option( self::OPTION_KEY, $options );
	}
}