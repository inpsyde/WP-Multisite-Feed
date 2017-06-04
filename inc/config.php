<?php # -*- coding: utf-8 -*-
namespace Inpsyde\MultisiteFeed;

use Inpsyde\MultisiteFeed\Cache\SiteTransientCache;

/**
 * Configuration of constructor arguments for all required classes.
 */
return [
	Plugin::class => function () {

		return [
			DI::instance( Settings::class ),
			DI::instance( FeedRequestValidator::class ),
			DI::instance( SiteTransientCache::class ),
		];
	},

	FeedRequestValidator::class => function () {

		return [ DI::instance( Settings::class ) ];
	},

	SiteTransientCache::class => function () {

		/**
		 * @var Settings $settings
		 */
		$settings = DI::instance( Settings::class );

		return [ 'inpsyde_multisite_feed_cache', $settings->get( OptionsKeys::CACHE_EXPIRY ) ];
	},

	FeedGenerator::class => function () {

		return [
			DI::instance( Settings::class ),
			DI::instance( NetworkFeedItemProvider::class ),
			DI::instance( FeedRenderer::class ),
			DI::instance( SiteTransientCache::class ),
		];
	},

	NetworkFeedItemProvider::class => function () {

		return [
			$GLOBALS['wpdb'],
			DI::instance( Settings::class ),
			$_GET,
		];
	},

	FeedRenderer::class => function () {

		return [
			DI::instance( Settings::class ),
		];
	},

	SettingsPage::class => function () {

		return [
			DI::instance( Settings::class ),
		];
	},
];

/**
 * Option keys used by the settings object.
 *
 * @package Inpsyde\MultisiteFeed
 */
interface OptionsKeys {

	const CACHE_EXPIRY = 'cache_expiry_minutes';
	const TITLE = 'title';
	const DESCRIPTION = 'description';
	const URL_SLUG = 'url_slug';
	const LANGUAGE_SLUG = 'language_slug';
	const MAX_ENTRIES = 'max_entries';
	const MAX_ENTRIES_PER_SITE = 'max_entries_per_site';
	const INCLUDED_BLOGS = 'included_blogs';
	const EXCLUDED_BLOGS = 'excluded_blogs';
	const USE_EXCERPT = 'use_excerpt';
	const USE_MORE = 'use_more';
	const ONLY_PODCASTS = 'only_podcasts';
	const ONLY_AUTHORS = 'only_authors';
}

/**
 * WordPress actions and filters
 *
 * @package Inpsyde\MultisiteFeed
 */
interface Hooks {

	const FILTER_SITE_QUERY_ARGS = 'inpsyde.multisite_feed.site_query_args';
	const ACTION_MULTIFEED_REQUEST = 'inpsyde.multisite_feed.valid_request';
}