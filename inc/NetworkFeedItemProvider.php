<?php # -*- coding: utf-8 -*-

namespace Inpsyde\MultisiteFeed;

class NetworkFeedItemProvider implements FeedItemProvider {

	/**
	 * @var \wpdb
	 */
	private $wpdb;
	/**
	 * @var DataStorage
	 */
	private $settings;
	/**
	 * @var array
	 */
	private $settings_overrides;

	/**
	 * NetworkFeedItemProvider constructor.
	 *
	 * @param \wpdb       $wpdb
	 * @param DataStorage $settings
	 * @param array       $settings_overrides
	 */
	public function __construct( \wpdb $wpdb, DataStorage $settings, array $settings_overrides = [] ) {

		$this->wpdb               = $wpdb;
		$this->settings           = $settings;
		$this->settings_overrides = $settings_overrides;
	}

	/**
	 * Returns a list of FeedItems
	 *
	 * @return NetworkPostFeedItem[]
	 */
	public function get_items() {

		$max_entries_per_site = $this->settings->get( OptionsKeys::MAX_ENTRIES_PER_SITE );
		$max_entries          = $this->settings->get( OptionsKeys::MAX_ENTRIES );
		$only_podcasts        = $this->settings->get( 'only_podcasts' );
		$only_authors         = $this->settings->get( 'only_authors' );

		$blogs = $this->get_site_ids();

		if ( ! is_array( $blogs ) ) {
			return [];
		}

		$feed_items = [];

		foreach ( $blogs as $blog_id ) {

			if ( $only_podcasts ) {
				$only_podcasts_sql_from  = ', `' . $this->wpdb->get_blog_prefix( $blog_id ) . 'postmeta` AS postmeta';
				$only_podcasts_sql_where = 'AND posts.`ID` = postmeta.`post_id`';
				$only_podcasts_sql       = "AND (postmeta.`meta_key` = 'enclosure' OR postmeta.`meta_key` = '_podPressMedia')";
			} else {
				$only_podcasts_sql_from  = '';
				$only_podcasts_sql_where = '';
				$only_podcasts_sql       = '';
			}

			if ( $only_authors ) {
				$only_authors_sql = 'AND post.`author_id` IN (' . $only_authors . ')';
			} else {
				$only_authors_sql = '';
			}

			$results = $this->wpdb->get_results(
				"
				SELECT
					posts.`ID`, posts.`post_date_gmt` AS date
				FROM
					`" . $this->wpdb->get_blog_prefix( $blog_id ) . "posts` AS posts
					$only_podcasts_sql_from
				WHERE
					posts.`post_type` = 'post'
					$only_podcasts_sql_where
					AND posts.`post_status` = 'publish'
					AND posts.`post_password` = ''
					AND posts.`post_date_gmt` < '" . gmdate( "Y-m-d H:i:s" ) . "'
					$only_podcasts_sql
					$only_authors_sql
				ORDER BY
					posts.post_date_gmt DESC
				LIMIT 0,"
				. (int) $max_entries_per_site
			);

			if ( ! is_array( $results ) || empty( $results ) ) {
				continue;
			}

			// add blog id to post data
			$results = array_map(
				function ( $row ) use ( $blog_id ) {

					$row->blog_id = $blog_id;

					return $row;
				}, $results
			);
			// add blog items to final array
			$feed_items = array_merge( $feed_items, $results );
		}

		// sort by date

		uasort(
			$feed_items, function ( $key_a, $key_b ) {

			if ( $key_a->date == $key_b->date ) {
				return 0;
			}

			return ( $key_a->date > $key_b->date ) ? - 1 : 1;
		}
		);

		if ( $max_entries ) {
			$feed_items = array_slice( $feed_items, 0, $max_entries );
		}

		$instances = [];
		foreach ( $feed_items as $feed_item ) {
			$instances[] = new NetworkPostFeedItem( $feed_item->blog_id, $feed_item->ID );
		}

		return $instances;
	}

	/**
	 * Perform the site query.
	 *
	 * @return array
	 */
	private function get_site_ids() {

		$site_query_args = [
			'number'   => $this->settings->get( OptionsKeys::MAX_ENTRIES ),
			'archived' => 0,
			'spam'     => 0,
			'deleted'  => 0,
		];

		if ( ! empty( $included = $this->get_included_blogs() ) ) {
			$site_query_args['site__in'] = $included;
		}

		if ( ! empty( $excluded = $this->get_excluded_blogs() ) ) {
			$site_query_args['site__not_in'] = $excluded;
		}

		$site_query_args = apply_filters( Hooks::FILTER_SITE_QUERY_ARGS, $site_query_args );

		$site_query_args['fields'] = 'ids';

		return get_sites( $site_query_args );
	}

	/**
	 * Return an array of blog IDs to include
	 *
	 * @return int[]
	 */
	private function get_included_blogs() {

		if ( isset( $this->settings_overrides[ OptionsKeys::INCLUDED_BLOGS ] ) ) {
			if ( is_string( $included = $this->settings_overrides[ OptionsKeys::INCLUDED_BLOGS ] ) ) {
				return explode( ',', $included );
			}

			return $included;
		} else {
			return [];
		}
	}

	/**
	 * Return an array of excluded blog ids
	 *
	 * @return int[]
	 */
	private function get_excluded_blogs() {

		if ( isset( $this->settings_overrides['excluded_blogs'] ) ) {
			if ( is_string( $excluded = $this->settings_overrides[ OptionsKeys::EXCLUDED_BLOGS ] ) ) {
				return explode( ',', $excluded );
			}

			return $excluded;
		} else {
			return explode( ',', $this->settings->get( OptionsKeys::EXCLUDED_BLOGS ) );
		}
	}
}