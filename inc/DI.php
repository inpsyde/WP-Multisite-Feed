<?php # -*- coding: utf-8 -*-
namespace Inpsyde\MultisiteFeed;

use Inpsyde\MultisiteFeed\Exception\DIException;

/**
 * Very simple helper class to handle dependency injection and class instantiation.
 *
 * Classes are configured in a specified config.php file.
 *
 * @package Inpsyde\MultisiteFeed
 */
class DI {

	/**
	 * Location of the config file.
	 * @var string
	 */
	public static $config_path = __DIR__ . DIRECTORY_SEPARATOR . 'config.php';

	/**
	 * Returns the required class instance. Will either return a cached instance or a new one,
	 * depending on the $singleton parameter.
	 *
	 * @param      $classname
	 * @param bool $singleton
	 *
	 * @return mixed
	 * @throws DIException
	 */
	public static function instance( $classname, $singleton = true ) {

		static $cache;
		if ( null === $cache ) {
			$cache = [];
		}

		if ( $singleton && isset( $cache[ $classname ] ) ) {
			return $cache[ $classname ];
		}

		if ( ! class_exists( $classname ) ) {
			throw new DIException( $classname . ' does not exist.' );
		}
		$class = new $classname( ... self::get( $classname, $singleton ) );
		if ( $singleton && ! isset( $cache[ $classname ] ) ) {
			$cache[ $classname ] = $class;

		}

		return $class;

	}

	/**
	 * Retrieves configured constructor args for the specified class
	 *
	 * @param      $key
	 * @param bool $singleton
	 *
	 * @return array|mixed
	 */
	public static function get( $key, $singleton = true ) {

		static $cache;
		if ( null === $cache ) {
			$cache = [];
		}
		if ( $singleton && isset( $cache[ $key ] ) ) {
			return $cache[ $key ];
		}
		$config = self::get_config();
		if ( ! isset( $config[ $key ] ) ) {
			return [];
		}
		$params = $config[ $key ]();
		if ( $singleton && ! isset( $cache[ $key ] ) ) {
			$cache[ $key ] = $params;
		}

		return $params;
	}

	/**
	 * Loads and returns the config.
	 *
	 * @return mixed
	 */
	protected static function get_config() {

		static $config;
		if ( null === $config ) {
			/** @noinspection PhpIncludeInspection */
			$config = require self::$config_path;
		}

		return $config;
	}
}
