<?php # -*- coding: utf-8 -*-

namespace Inpsyde\MultisiteFeed;

class PostFeedItem implements FeedItem {

	private $date_created;
	private $title;
	private $permalink;
	private $author_avatar_url;
	private $guid;
	private $author;
	private $comments_link;
	private $excerpt;
	private $enclosure;
	private $comments_number;
	private $wp_rss2_item;
	private $date_modified;
	private $wp_rss2_category;
	private $content;
	private $get_post_thumbnail_title;
	private $get_post_thumbnail_url;
	private $has_post_thumbnail;
	private $date_mysql;

	/**
	 * PostFeedItem constructor.
	 *
	 * @param $post_id
	 */
	public function __construct( $post_id ) {

		global $post;
		$post = get_post( $post_id );
		setup_postdata( $post );
		$this->guid              = get_the_guid();
		$this->title             = get_the_title_rss();
		$this->date_created      = mysql2date( 'D, d M Y H:i:s +0000', get_post_time( 'Y-m-d H:i:s', true ),
			false );
		$this->date_modified     = mysql2date( 'D, d M Y H:i:s +0000', get_lastpostmodified( 'GMT' ), false );
		$this->permalink         = esc_url( apply_filters( 'the_permalink_rss', get_permalink() ) );
		$this->author            = get_the_author();
		$this->author_avatar_url = get_avatar_url( get_the_author_meta( 'ID' ) );
		$this->comments_link     = apply_filters( 'comments_link_feed', get_comments_link() );
		$this->comments_number   = get_comments_number();
		$this->excerpt           = apply_filters( 'the_excerpt_rss', get_the_excerpt() );
		$this->content           = apply_filters( 'the_content', get_the_content() );

		if ( $this->has_post_thumbnail = has_post_thumbnail() ) {

			$image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'medium' );
			if ( is_array( $image ) ) {
				$this->get_post_thumbnail_url = $image[0];

			}
			$this->get_post_thumbnail_title = get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt',
				true );
		}

		ob_start();
		rss_enclosure();
		$this->enclosure = ob_get_clean();

		ob_start();
		do_action( 'rss2_item' );
		$this->wp_rss2_item = ob_get_clean();

		ob_start();
		the_category_rss( 'rss2' );
		$this->wp_rss2_category = ob_get_clean();
		wp_reset_postdata();
	}

	public function get_db_date() {

		return $this->date_mysql;
	}

	public function get_date_created() {

		return $this->date_created;
	}

	public function get_title() {

		return $this->title;

	}

	public function get_permalink() {

		return $this->permalink;
	}

	public function get_author_avatar_url() {

		return $this->author_avatar_url;
	}

	public function get_guid() {

		return $this->guid;
	}

	public function get_author() {

		return $this->author;
	}

	public function get_comments_link() {

		return $this->comments_link;
	}

	public function get_excerpt() {

		return $this->excerpt;
	}

	public function get_content() {

		return $this->content;
	}

	public function get_enclosure() {

		return $this->enclosure;
	}

	public function get_comments_number() {

		return $this->comments_number;
	}

	public function wp_rss2_item() {

		return $this->wp_rss2_item;
	}

	public function wp_rss2_category() {

		return $this->wp_rss2_category;
	}

	public function has_post_thumbnail() {

		return $this->has_post_thumbnail;
	}

	public function get_post_thumbnail_url() {

		return $this->get_post_thumbnail_url;
	}

	public function get_post_thumbnail_title() {

		return $this->get_post_thumbnail_title;
	}
}