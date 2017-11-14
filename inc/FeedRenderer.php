<?php # -*- coding: utf-8 -*-

namespace Inpsyde\MultisiteFeed;

class FeedRenderer implements Renderer {

	/**
	 * @var DataStorage
	 */
	private $settings;

	/**
	 * FeedRenderer constructor.
	 *
	 * @param DataStorage $settings
	 */
	public function __construct( DataStorage $settings ) {

		$this->settings = $settings;
	}

	/**
	 * @param FeedItem[] $feed_items
	 *
	 * @return string
	 */
	public function render( array $feed_items ) {

		$rss_language = $this->settings->get( 'language_slug' );
		if ( empty( $rss_language ) && defined( 'WPLANG' ) ) {
			$rss_language = substr( WPLANG, 0, 2 );
		}
		date_default_timezone_set( get_option( 'timezone_string' ) );

		ob_start();
		echo '<?xml version="1.0" encoding="' . esc_attr( get_option( 'blog_charset' ) ) . '"?' . '>'; ?>

		<rss version="2.0"
			xmlns:content="http://purl.org/rss/1.0/modules/content/"
			xmlns:wfw="http://wellformedweb.org/CommentAPI/"
			xmlns:dc="http://purl.org/dc/elements/1.1/"
			xmlns:atom="http://www.w3.org/2005/Atom"
			xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
			xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
			xmlns:media="http://search.yahoo.com/mrss/"
			<?php do_action( 'rss2_ns' ); ?>
		>

			<channel>
				<title><?php echo esc_attr( $this->get_feed_title() ); ?></title>
				<atom:link href="<?php echo esc_url( Plugin::get_feed_url() ); ?>" type="application/rss+xml" />
				<link><?php echo esc_url( Plugin::get_feed_url() ); ?></link>
				<description><?php echo esc_attr( $this->get_feed_description() ); ?></description>
				<lastBuildDate><?php
					$lastModified  = $this->settings->get( 'last_modified', get_lastpostmodified( 'GMT' ) );
					$lastBuildDate = date( 'D, d M Y H:i:s O', strtotime( $lastModified ) );
					echo $lastBuildDate;

					?></lastBuildDate>
				<language><?php echo esc_attr( $rss_language ); ?></language>
				<sy:updatePeriod>
					<?php echo esc_attr( apply_filters( 'rss_update_period', 'hourly' ) ); ?>
				</sy:updatePeriod>
				<sy:updateFrequency>
					<?php echo (int) apply_filters( 'rss_update_frequency', '1' ); ?>
				</sy:updateFrequency>
				<?php do_action( 'rss2_head' );

				foreach ( $feed_items as $feed_item ): ?>

					<item>
						<title><?php echo $feed_item->get_title() ?></title>
						<link><?php echo $feed_item->get_permalink() ?></link>
						<comments><?php echo $feed_item->get_comments_link(); ?></comments>
						<pubDate><?php echo $feed_item->get_date_created() ?></pubDate>
						<category><![CDATA[<?php bloginfo( 'name' ); ?>]]></category>
						<dc:creator><?php $feed_item->get_author(); ?></dc:creator>

						<media:content
							url="<?php echo esc_url( $feed_item->get_author_avatar_url() ); ?>"
							medium="image">
							<media:title type="html"><?php echo $feed_item->get_author() ?></media:title>
						</media:content>
						<?php echo $feed_item->wp_rss2_category() ?>

						<guid isPermaLink="false"><?php echo $feed_item->get_guid() ?></guid>
						<?php if ( $feed_item->has_post_thumbnail() ) : ?>
							<image>
								<url><?php echo esc_url( $feed_item->get_post_thumbnail_url() ); ?></url>
								<title><?php echo esc_html( $feed_item->get_post_thumbnail_title() ); ?></title>
								<link><?php echo $feed_item->get_permalink() ?></link>
							</image>
						<?php endif; ?>
						<description><![CDATA[<?php echo $feed_item->get_excerpt() ?>]]></description>

						<?php if ( ! $this->settings->get( 'rss_use_excerpt' ) ) : ?>
							<?php $content = $this->get_the_content_feed( 'rss2', $feed_item ); ?>
							<?php if ( strlen( $content ) > 0 ) : ?>
								<content:encoded><![CDATA[<?php echo $content; ?>]]></content:encoded>
							<?php else : ?>
								<content:encoded><![CDATA[<?php echo $feed_item->get_excerpt() ?>]]></content:encoded>
							<?php endif; ?>
						<?php endif; ?>
						<wfw:commentRss><?php echo esc_url(
								$feed_item->get_comments_link()
							); ?></wfw:commentRss>
						<slash:comments><?php echo $feed_item->get_comments_number() ?></slash:comments>
						<?php
						echo $feed_item->get_enclosure();
						echo $feed_item->wp_rss2_item();
						?>
					</item>

				<?php endforeach ?>

			</channel>
		</rss>
		<?php
		return ob_get_clean();
	}

	/**
	 * Return feed title.
	 *
	 * @return string
	 */
	protected function get_feed_title() {

		$info  = strip_tags( $this->settings->get( 'title' ) );
		$title = apply_filters( 'get_bloginfo_rss', convert_chars( $info ) );

		if ( ! $title ) {
			$title = get_bloginfo_rss( 'name' );
			$title .= get_wp_title_rss();
		}

		return apply_filters( 'inpsmf_feed_title', $title );
	}

	/**
	 * Return feed description.
	 *
	 * @return string
	 */
	protected function get_feed_description() {

		$info        = strip_tags( $this->settings->get( 'description' ) );
		$description = apply_filters( 'get_bloginfo_rss', convert_chars( $info ) );

		if ( ! $description ) {
			$description = get_bloginfo_rss( 'description' );
		}

		return apply_filters( 'inpsmf_feed_description', $description );
	}

	/**
	 * Retrieve the post content for feeds with the custom option for full or excerpt text.
	 *
	 * @param  string $feed_type The type of feed. rss2 | atom | rss | rdf
	 *
	 * @return string The filtered content.
	 */
	protected function get_the_content_feed( $feed_type = null, FeedItem $item ) {

		if ( ! $feed_type ) {
			$feed_type = get_default_feed();
		}
		$use_excerpt = (int) $this->settings->get( 'use_excerpt' );

		if ( $use_excerpt ) {
			$content = $item->get_excerpt();
		} else {

			global $more;
			$temp = $more;
			$more = (int) $this->settings->get( 'use_more' );
			/** This filter is documented in wp-admin/post-template.php */
			$content = $item->get_content();
			$content = str_replace( ']]>', ']]&gt;', $content );
			$more    = $temp;
		}

		/**
		 * Filter the post content for use in feeds.
		 *
		 * @param string $content   The current post content.
		 * @param string $feed_type Type of feed. Possible values include 'rss2', 'atom'.
		 *                          Default 'rss2'.
		 */

		return apply_filters( 'the_content_feed', $content, $feed_type );
	}
}
