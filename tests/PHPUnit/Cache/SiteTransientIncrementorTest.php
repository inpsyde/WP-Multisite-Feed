<?php
/**
 * Created by PhpStorm.
 * User: biont
 * Date: 05.06.17
 * Time: 16:16
 */

namespace Inpsyde\MultisiteFeed\Cache;

use Brain\Monkey\Functions;
use MonkeryTestCase\BrainMonkeyWpTestCase;

class SiteTransientIncrementorTest extends BrainMonkeyWpTestCase {

	public function test_get() {

		$group_name  = 'foo';
		$incrementor = uniqid();
		Functions\expect( 'get_site_transient' )
		         ->once()
		         ->andReturn( $incrementor );
		$testee = new SiteTransientIncrementor( $group_name );
		$result = $testee->get();
		$this->assertSame( $result, $incrementor );
	}

	public function test_increase() {

		$group_name  = 'foo';
		$incrementor = uniqid();
		Functions\expect( 'get_site_transient' )
		         ->once()
		         ->andReturn( $incrementor );
		Functions\expect( 'set_site_transient' )
		         ->once();
		$testee   = new SiteTransientIncrementor( $group_name );
		$previous = $testee->get();
		$new      = $testee->increase();
		$this->assertSame( $previous, $incrementor );
		$this->assertNotSame( $new, $incrementor );
	}
}
