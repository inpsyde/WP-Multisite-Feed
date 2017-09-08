<?php
/**
 * Created by PhpStorm.
 * User: biont
 * Date: 05.06.17
 * Time: 14:26
 */

namespace Inpsyde\MultisiteFeed\Cache;

use Brain\Monkey\Functions;
use Inpsyde\MultisiteFeed\Cache\Incrementor\SiteTransientIncrementor;
use MonkeryTestCase\BrainMonkeyWpTestCase;

class SiteTransientCacheTest extends BrainMonkeyWpTestCase {

	public function test_value_is_cached() {

		$value            = 3;
		$cacheGroupName   = 'cache_group';
		$expiration       = PHP_INT_MAX;
		$incrementorValue = 'incrementor';
		$cacheKey         = 'cache_key';
		$groupCacheKey    = $cacheGroupName . $incrementorValue . $cacheKey;

		$incr = \Mockery::mock( SiteTransientIncrementor::class );
		$incr->shouldReceive( 'get' )
		     ->twice()
		     ->andReturn( $incrementorValue );

		Functions\expect( 'get_site_transient' )
		         ->withArgs( [ $groupCacheKey ] )
		         ->once()
		         ->andReturn( $value );

		Functions\expect( 'set_site_transient' )
		         ->withArgs( [ $groupCacheKey, $value, $expiration ] )
		         ->once();
		$testee = new SiteTransientCache( $cacheGroupName, $expiration, $incr );

		$testee->set( $cacheKey, $value, $expiration );

		$result = $testee->get( $cacheKey );

		$this->assertSame( $result, $value );
	}

	public function test_cache_is_flushed() {

		$value             = 3;
		$cacheGroupName    = 'cache_group';
		$expiration        = PHP_INT_MAX;
		$incrementorValue  = 'incrementor';
		$incrementorValue2 = 'incrementor2';
		$cacheKey          = 'cache_key';

		$groupCacheKey  = $cacheGroupName . $incrementorValue . $cacheKey;
		$groupCacheKey2 = $cacheGroupName . $incrementorValue2 . $cacheKey;

		$incr = \Mockery::mock( SiteTransientIncrementor::class );

		$incr->shouldReceive( 'get' )
		     ->twice()
		     ->andReturn( $incrementorValue );

		$incr->shouldReceive( 'get' )
		     ->once()
		     ->andReturn( $incrementorValue2 );

		$incr->shouldReceive( 'increase' )
		     ->once()
		     ->andReturn( $incrementorValue2 );

		Functions\expect( 'set_site_transient' )
		         ->withArgs( [ $groupCacheKey, $value, $expiration ] )
		         ->once();

		Functions\expect( 'get_site_transient' )
		         ->withArgs( [ $groupCacheKey ] )
		         ->once()
		         ->andReturn( $value );

		Functions\expect( 'get_site_transient' )
		         ->withArgs( [ $groupCacheKey2 ] )
		         ->once()
		         ->andReturn( false );

		$testee = new SiteTransientCache( $cacheGroupName, $expiration, $incr );

		$testee->set( $cacheKey, $value, $expiration );

		$result = $testee->get( $cacheKey );

		$testee->flush();

		$result2 = $testee->get( $cacheKey );

		$this->assertSame( $result, $value );
		$this->assertFalse( $result2 );

	}

	protected function setUp() {

		if ( ! defined( 'MINUTE_IN_SECONDS' ) ) {
			define( 'MINUTE_IN_SECONDS', 60 );

		}

		if ( ! defined( 'HOUR_IN_SECONDS' ) ) {
			define( 'HOUR_IN_SECONDS', 3600 );

		}
	}
}
