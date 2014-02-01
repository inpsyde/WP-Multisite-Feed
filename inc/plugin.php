<?php
namespace Inpsyde\MultisiteFeed;

require_once dirname( __FILE__) . '/class-settings-page.php';

// Load translation file
add_action( 'load-settings_page_inpsyde-multisite-feed-page', 'Inpsyde\MultisiteFeed\localize_plugin' );
/**
 * Load plugin translation
 * 
 * @since   06/06/2013
 * @return  void
 */
function localize_plugin() {
	
	load_plugin_textdomain(
		'inps-multisite-feed',
		FALSE,
		str_replace( 'inc', '', dirname( plugin_basename(__FILE__) ) ) . 'languages' );
}

// network activation check
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
	
	$info  = strip_tags( Settings\get_site_option( 'title' ) );
	$title = apply_filters( 'get_bloginfo_rss', convert_chars( $info ) );
	
	if ( ! $title ) {
		$title  = get_bloginfo_rss( 'name' );
		$title .= get_wp_title_rss();
	}
	
	return apply_filters( 'inpsmf_feed_title', $title );
}

/**
 * Return feed description.
 * 
 * @return string 
 */
function get_feed_description() {
	
	$info        = strip_tags( Settings\get_site_option( 'description' ) );
	$description = apply_filters( 'get_bloginfo_rss', convert_chars( $info ) );
	
	if ( ! $description )
		$description = get_bloginfo_rss( 'description' );

	return apply_filters( 'inpsmf_feed_description', $description );
}
/**
 * Print out feed XML. Use cache if available.
 * 
 * @return void
 */
function display_feed() {
	global $wpdb;
	
	$cache_key = 'inpsyde_multisite_feed_cache';
	$out = get_site_transient( $cache_key );
	
	// Deactivate Caching for Debugging
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG
			 || ( 0 === Settings\get_site_option( 'cache_expiry_minutes' ) )
			)
		$out = FALSE;
	
	if ( FALSE === $out ) {

		$max_entries_per_site = Settings\get_site_option( 'max_entries_per_site' );
		$max_entries          = Settings\get_site_option( 'max_entries' );
		$excluded_blogs       = Settings\get_site_option( 'excluded_blogs' );
		$only_podcasts        = Settings\get_site_option( 'only_podcasts' );
		
		if ( $excluded_blogs )
			$excluded_blogs_sql = "AND blog.`blog_id` NOT IN (" . $excluded_blogs . ")";
		else
			$excluded_blogs_sql = '';
		
		$blogs = $wpdb->get_col( "
			SELECT
				blog.`blog_id`
			FROM
				" . $wpdb->base_prefix . "blogs AS blog 
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
		
		foreach( $blogs as $blog_id ) {
			
			if ( $only_podcasts ) {
				$only_podcasts_sql_from  = ", `" . $wpdb->get_blog_prefix( $blog_id ) . "postmeta` AS postmeta";
				$only_podcasts_sql_where = "AND posts.`ID` = postmeta.`post_id`";
				$only_podcasts_sql       = "AND (postmeta.`meta_key` = 'enclosure' OR postmeta.`meta_key` = '_podPressMedia')";
			} else {
				$only_podcasts_sql_from = '';
				$only_podcasts_sql_where = '';
				$only_podcasts_sql = '';
			}
			
			// $wpdb::get_blog_prefix( $blog_id )
			// $wpdb->base_prefix . ($blog_id > 1 ? $blog_id . '_' : '')
			$results = $wpdb->get_results( "
				SELECT
					posts.`ID`, posts.`post_date_gmt` AS date
				FROM
					`" . $wpdb->get_blog_prefix( $blog_id ) . "posts` AS posts
					$only_podcasts_sql_from
				WHERE
					posts.`post_type` = 'post'
					$only_podcasts_sql_where
					AND posts.`post_status` = 'publish'
					AND posts.`post_password` = ''
					AND posts.`post_date_gmt` < '" . gmdate( "Y-m-d H:i:s" ) . "'
					$only_podcasts_sql
				ORDER BY
					posts.post_date_gmt DESC
				LIMIT 0,"
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
	
	header( 'Content-Type: ' . feed_content_type( 'rss-http' ) . '; charset=' . get_option( 'blog_charset' ), TRUE );
	echo $out;
}

/**
 * Invalidate Cache.
 *
 * On the next request, the feed will be guaranteed to be fresh.
 * 
 * @return  void
 */
function invalidate_cache() {
	
	delete_site_transient( 'inpsyde_multisite_feed_cache' );
}

/**
 * Return XML for full feed.
 * 
 * @param   array $feed_items Array of objects. Required attributes: ID (=post id), blog_id
 * @return  string
 */
function get_feed_xml( $feed_items ) {
	global $post;
	
	$rss_language = Settings\get_site_option( 'language_slug' );
	if ( empty( $rss_language ) && defined( 'WPLANG' ) )
		$rss_language = substr( WPLANG, 0, 2 ); 
	
	ob_start();
echo '<?xml version="1.0" encoding="' . get_option( 'blog_charset' ) . '"?' . '>'; ?>

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
	<atom:link href="<?php echo get_feed_url(); ?>" rel="self" type="application/rss+xml" />
	<link><?php echo get_feed_url(); ?></link>
	<description><?php echo get_feed_description(); ?></description>
	<lastBuildDate><?php echo mysql2date( 'D, d M Y H:i:s +0000', get_lastpostmodified( 'GMT' ), FALSE ); ?></lastBuildDate>
	<language><?php echo $rss_language; ?></language>
	<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
	<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
	<?php do_action( 'rss2_head' );
	
	foreach ( $feed_items as $feed_item ):
		switch_to_blog( $feed_item->blog_id );
		$post = get_post( $feed_item->ID );
		setup_postdata( $post ); ?>
		
		<item>
			<title><?php the_title_rss() ?></title>
			<link><?php the_permalink_rss() ?></link>
			<comments><?php comments_link_feed(); ?></comments>
			<pubDate><?php echo mysql2date( 'D, d M Y H:i:s +0000', get_post_time( 'Y-m-d H:i:s', TRUE ), FALSE ); ?></pubDate>
			<dc:creator><?php the_author(); ?></dc:creator>
			<?php the_category_rss( 'rss2' ); ?>
			
			<guid isPermaLink="false"><?php the_guid(); ?></guid>
	<?php if ( get_option( 'rss_use_excerpt' ) ) : ?>
			<description><![CDATA[<?php the_excerpt_rss(); ?>]]></description>
	<?php else : ?>
			<description><![CDATA[<?php the_excerpt_rss(); ?>]]></description>
		<?php $content = get_the_content_feed( 'rss2' ); ?>
		<?php if ( strlen( $content ) > 0 ) : ?>
			<content:encoded><![CDATA[<?php echo $content; ?>]]></content:encoded>
		<?php else : ?>
			<content:encoded><![CDATA[<?php the_excerpt_rss() ?>]]></content:encoded>
		<?php endif; ?>
	<?php endif; ?>
			<wfw:commentRss><?php echo esc_url( get_post_comments_feed_link( NULL, 'rss2' ) ); ?></wfw:commentRss>
			<slash:comments><?php echo get_comments_number(); ?></slash:comments>
	<?php rss_enclosure();
		do_action( 'rss2_item' ); ?>
		</item>
		
		<?php restore_current_blog();
	endforeach ?>
	
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

/**
 * Simple helper to debug to the console
 * 
 * $data   Array, String $data
 * @return void
 */
function debug_to_console( $data ) {
	
	if ( is_array( $data ) )
		$output = "<script>console.log( 'Debug Multisite-Feed: " . implode( ',', $data) . "' );</script>";
	else
		$output = "<script>console.log( 'Debug Multisite-Feed: " . $data . "' );</script>";
		
	echo $output;
}