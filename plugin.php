<?php
namespace Inpsyde\MultisiteFeed;

require_once dirname( __FILE__) . '/settings.php';

if ( is_network_admin() ) {
	new Settings\Inpsyde_Settings_Page;
}

/**
 * Return feed url.
 * 
 * @return string
 */
function get_feed_url() {
	$base_url = get_bloginfo( 'url' );
	$slug     = Settings\get_site_option( 'url_slug' );

	return apply_filters( 'inpsmf_feed_url' , $base_url . '/' . $slug );
}

/**
 * Return feed title.
 * 
 * @return string
 */
function get_feed_title() {
	$title     = Settings\get_site_option( 'title' );

	if ( ! $title )
		$title = get_bloginfo_rss( 'name' ) . get_wp_title_rss();

	return apply_filters( 'inpsmf_feed_title' , $title );
}

/**
 * Return feed description.
 * 
 * @return string
 */
function get_feed_description() {
	$description     = Settings\get_site_option( 'description' );

	if ( ! $description )
		$description = get_bloginfo_rss( "description" );

	return apply_filters( 'inpsmf_feed_description' , $description );
}

/**
 * Print out feed XML. Use cache if available.
 * 
 * @return void
 */
function display_feed() {
	global $wpdb;

	$cache_key = 'inpsyde_multisite_feed_cache';
    if ( FALSE === ( $out = get_site_transient( $cache_key ) ) ) {

		$max_entries_per_site = Settings\get_site_option( 'max_entries_per_site' );
		$max_entries          = Settings\get_site_option( 'max_entries' );
		$excluded_blogs       = Settings\get_site_option( 'excluded_blogs' );

		if ( $excluded_blogs )
			$excluded_blogs_sql = "AND blog.`blog_id` NOT IN (" . $excluded_blogs . ")";
		else
			$excluded_blogs_sql = '';

		$blogs = $wpdb->get_col( "
			SELECT
				blog.`blog_id`
			FROM
				".$wpdb->base_prefix."blogs AS blog 
			WHERE
				blog.`public` = '1'
				AND blog.`archived` = '0'
				AND blog.`spam` = '0'
				$excluded_blogs_sql
				AND blog.`deleted` ='0' 
				AND blog.`last_updated` != '0000-00-00 00:00:00'
		");

		if ( ! is_array( $blogs ) )
			wp_die( "There are no blogs." );

		$feed_items = array();

		foreach ( $blogs as $blog_id ) {
			$results = $wpdb->get_results( "
				SELECT
					`ID`, `post_date_gmt` AS date
				FROM
					`" . $wpdb->base_prefix . $blog_id . "_posts` 
				WHERE
					`post_status` = 'publish'
					AND `post_password` = ''
					AND `post_date_gmt` < '" . gmdate( "Y-m-d H:i:s" ) . "'
				LIMIT "
					. (int) $max_entries_per_site
			);

			if ( ! is_array( $results ) || empty( $results ) )
				continue;

			// add blog id to post data
			$results = array_map( function ( $row ) use ( $blog_id ) {
				$row->blog_id = $blog_id;
				return $row;
			}, $results );

			// add blog items to final array
			$feed_items = array_merge( $feed_items, $results );
		}

		// sort by date
		uasort( $feed_items, function ( $a, $b ) {
			if ( $a->date == $b->date )
				return 0;

			return ( $a->date > $b->date ) ? -1 : 1;
		} );

		if ( $max_entries )
			$feed_items = array_slice( $feed_items, 0, $max_entries );

        $out = get_feed_xml( $feed_items );
        set_site_transient( $cache_key, $out, 60 * Settings\get_site_option( 'cache_expiry_minutes', 60 ) );
    }

    header( 'Content-Type: ' . feed_content_type( 'rss-http' ) . '; charset=' . get_option( 'blog_charset' ), true );
    echo $out;
}

/**
 * Invalidate cache.
 * 
 * On the next request, the feed will be guaranteed to be fresh.
 * 
 * @return void
 */
function invalidate_cache() {
	delete_site_transient( 'inpsyde_multisite_feed_cache' );
}

/**
 * Return XML for full feed.
 * 
 * @param  array $feed_items Array of objects. Required attributes: ID (=post id), blog_id
 * @return string
 */
function get_feed_xml( $feed_items ) {
	global $post;

	ob_start();
	echo '<?xml version="1.0" encoding="' . get_option( 'blog_charset' ) . '"?' . '>';
	?>
	<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
	<?php do_action('rss2_ns'); ?>
	>

	<channel>
		<title><?php echo get_feed_title(); ?></title>
		<link><?php echo get_feed_url(); ?></link>
		<description><?php echo get_feed_description(); ?></description>
		<lastBuildDate><?php echo mysql2date( 'D, d M Y H:i:s +0000', get_lastpostmodified( 'GMT' ), FALSE ); ?></lastBuildDate>
		<language><?php echo get_option( 'rss_language' ); ?></language>
		<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
		<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
		<?php do_action( 'rss2_head' ); ?>

		<?php  ?>
		<?php foreach ( $feed_items as $feed_item ): ?>
			<?php switch_to_blog( $feed_item->blog_id ); ?>
			<?php $post = get_post( $feed_item->ID ); ?>
			<?php setup_postdata( $post ); ?>

			<item>
				<title><?php the_title_rss() ?></title>
				<link><?php the_permalink_rss() ?></link>
				<comments><?php comments_link_feed(); ?></comments>
				<pubDate><?php echo mysql2date( 'D, d M Y H:i:s +0000', get_post_time( 'Y-m-d H:i:s', true ), FALSE ); ?></pubDate>
				<dc:creator><?php the_author() ?></dc:creator>
				<?php the_category_rss( 'rss2' ) ?>

				<guid isPermaLink="false"><?php the_guid(); ?></guid>
		<?php if (get_option('rss_use_excerpt')) : ?>
				<description><![CDATA[<?php the_excerpt_rss() ?>]]></description>
		<?php else : ?>
				<description><![CDATA[<?php the_excerpt_rss() ?>]]></description>
			<?php if ( strlen( $post->post_content ) > 0 ) : ?>
				<content:encoded><![CDATA[<?php the_content_feed('rss2') ?>]]></content:encoded>
			<?php else : ?>
				<content:encoded><![CDATA[<?php the_excerpt_rss() ?>]]></content:encoded>
			<?php endif; ?>
		<?php endif; ?>
				<wfw:commentRss><?php echo esc_url( get_post_comments_feed_link( null, 'rss2' ) ); ?></wfw:commentRss>
				<slash:comments><?php echo get_comments_number(); ?></slash:comments>
		<?php rss_enclosure(); ?>
			<?php do_action( 'rss2_item' ); ?>
			</item>

			<?php restore_current_blog(); ?>
		<?php endforeach ?>

	</channel>
	</rss>
	<?php

	$xml = ob_get_contents();
	ob_end_clean();

	return $xml;
}

// invalidate cache when necessary
add_action( 'init', function () {

	$actions = array(
		'publish_post',
		'deleted_post',
		'save_post',
		'trashed_post',
		'private_to_published',
		'inpsmf_update_settings'
	);

	foreach ( $actions as $action )
		add_action( $action, '\Inpsyde\MultisiteFeed\invalidate_cache' );

} );

// hijack feed into WordPress
add_action( 'init', function () {
	$slug = Settings\get_site_option( 'url_slug' );

	if ( ! $slug )
		return;

	$end_of_request_uri = substr( $_SERVER[ 'REQUEST_URI' ], strlen( $slug ) * -1 );

	if ( $slug === $end_of_request_uri ) {
		display_feed();
		exit;
	}
} );