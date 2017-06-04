<?php # -*- coding: utf-8 -*-

namespace Inpsyde\MultisiteFeed;

use MonkeryTestCase\BrainMonkeyWpTestCase;

/**
 * Class DITest
 *
 * @runTestsInSeparateProcesses
 *
 * @package Inpsyde\MultisiteFeed
 */
class DITest extends BrainMonkeyWpTestCase {

	const NUM_TESTS = 4;

	public function test_object_returns_singleton() {

		$className = \DIFooClass::class;
		$tests     = [];
		for ( $i = 0; $i <= self::NUM_TESTS; $i ++ ) {
			$tests[] = DI::instance( $className, true );
		}

		$this->assertContainsOnlyInstancesOf( $className, $tests );

		$cmp = reset( $tests );
		foreach ( $tests as $instance ) {
			$this->assertSame( $cmp, $instance );
		}
	}

	public function test_object_returns_new_instances() {

		$className = \DIFooClass::class;
		$tests     = [];
		for ( $i = 0; $i <= self::NUM_TESTS; $i ++ ) {
			$tests[] = DI::instance( $className, false );
		}

		$this->assertContainsOnlyInstancesOf( $className, $tests );

		$cmp = reset( $tests );
		foreach ( $tests as $idx => $instance ) {
			if ( $idx == 0 ) {
				continue;
			}
			$this->assertNotSame( $cmp, $instance );
		}
	}

	public function test_get_singleton_args() {

		$className = \DIFooClass::class;
		$tests     = [];
		for ( $i = 0; $i <= self::NUM_TESTS; $i ++ ) {
			$tests[] = DI::get( $className, true );
		}

		$cmp = reset( $tests );

		foreach ( $tests as $idx => $instance ) {
			$this->assertSame( $cmp, $instance );
		}

	}

	public function test_get_new_args() {

		$className = \DIFooClass::class;
		$tests     = [];
		for ( $i = 0; $i <= self::NUM_TESTS; $i ++ ) {
			$tests[] = DI::get( $className, false );
		}

		$cmp = reset( $tests );

		foreach ( $tests as $idx => $instance ) {
			if ( $idx == 0 ) {
				continue;
			}
			$this->assertNotSame( $cmp, $instance );
		}

	}

	protected function setUp() {

		DI::$config_path = __DIR__ . '/../../Helper/config.php';

	}
}