<?php
namespace Inpsyde\MultisiteFeed\Settings;

/**
 * Convenience wrapper to access plugin options.
 * 
 * @param  string $name	option name
 * @param  mixed  $default fallback value if option does not exist
 * @return mixed
 */
function get_site_option( $name, $default = NULL ) {
	
	$options = \get_site_option( 'inpsyde_multisitefeed' );

	return ( isset( $options[ $name ] ) ) ? $options[ $name ] : $default;
}

/**
 * Settings Page Class
 * 
 * @authors et, fb
 * @since   2.0.0  03/26/2012
 */
class Inpsyde_Settings_Page {
	
	private $page_hook;
	
	public function __construct() {
		add_action( 'network_admin_menu', array( $this, 'init_menu' ) );
		add_action( 'network_admin_menu', array( $this, 'save' ) );
	}
	
	public function init_menu() {
		
		$this->page_hook = add_submenu_page(
			/* $parent_slug*/ 'settings.php',
			/* $page_title */ 'Multisite Feed',
			/* $menu_title */ 'Multisite Feed',
			/* $capability */ 'manage_users',
			/* $menu_slug  */ 'inpsyde-multisite-feed-page',
			/* $function   */ array( $this, 'page' )
		);
	}

	public function save() {
		
		if ( ! isset( $_POST[ 'action' ] ) || $_POST[ 'action' ] != 'update' )
			return;

		if ( ! wp_verify_nonce( $_REQUEST[ '_wpnonce' ], 'inpsmf-options') )
			wp_die( 'Sorry, you failed the nonce test.' ); 

		update_site_option( 'inpsyde_multisitefeed', $_REQUEST[ 'inpsyde_multisitefeed' ] );

		do_action( 'inpsmf_update_settings' );

		if ( isset( $_REQUEST[ '_wp_http_referer' ] ) )
			wp_redirect( $_REQUEST[ '_wp_http_referer' ] );
	}
	
	/**
	 * Get settings pages incl. markup
	 * 
	 * @authors et, fb
	 * @since   2.0.0  03/26/2012
	 * @return  void
	 */
	public function page() {
		?>
		<div class="wrap">

			<?php screen_icon( 'options-general' ); ?>
			<h2><?php _e( 'Multisite Feed Settings', 'inps-multisite-feed' ); ?></h2>
			
			<form method="post" action="#">

				<?php 
				echo '<input type="hidden" name="action" value="update" />';
				wp_nonce_field( "inpsmf-options" );
				?>

				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row">
								<label for="inpsmf_title"><?php _e( 'Title', 'inps-multisite-feed' ) ?></label>
							</th>
							<td>
								<input class="regular-text" type="text" value="<?php echo get_site_option( 'title', '' ); ?>" name="inpsyde_multisitefeed[title]" id="inpsmf_title">
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="inpsmf_description"><?php _e( 'Description', 'inps-multisite-feed' ) ?></label>
							</th>
							<td>
								<textarea name="inpsyde_multisitefeed[description]" id="inpsmf_description" cols="40" rows="7"><?php echo get_site_option( 'description', '' ); ?></textarea>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="inpsmf_url_slug"><?php _e( 'Url', 'inps-multisite-feed' ) ?></label>
							</th>
							<td>
								<input class="regular-text" type="text" value="<?php echo get_site_option( 'url_slug', 'multifeed' ); ?>" name="inpsyde_multisitefeed[url_slug]" id="inpsmf_url_slug">
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="inpsmf_max_entries_per_site"><?php _e( 'Max. entries per site', 'inps-multisite-feed' ) ?></label>
							</th>
							<td>
								<input class="regular-text" type="text" value="<?php echo get_site_option( 'max_entries_per_site', 20 ); ?>" name="inpsyde_multisitefeed[max_entries_per_site]" id="inpsmf_max_entries_per_site">
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="inpsmf_max_entries"><?php _e( 'Max. entries overall', 'inps-multisite-feed' ) ?></label>
							</th>
							<td>
								<input class="regular-text" type="text" value="<?php echo get_site_option( 'max_entries', 100 ); ?>" name="inpsyde_multisitefeed[max_entries]" id="inpsmf_max_entries">
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="inpsmf_excluded_blogs"><?php _e( 'Exclude blogs', 'inps-multisite-feed' ) ?></label>
							</th>
							<td>
								<input class="regular-text" type="text" value="<?php echo get_site_option( 'excluded_blogs', '' ); ?>" name="inpsyde_multisitefeed[excluded_blogs]" id="inpsmf_excluded_blogs">
								<p><?php _e( 'Blog IDs, separated by comma. Leave empty to include all blogs.', 'inps-multisite-feed' ) ?></p>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="inpsmf_only_podcasts"><?php _e( 'Only include podcast episodes', 'inps-multisite-feed' ) ?></label>
							</th>
							<td>
							    <input id="inpsmf_only_podcasts" name="inpsyde_multisitefeed[only_podcasts]" type="checkbox" value="1" <?php if (get_site_option( 'only_podcasts', '' )) { checked('1', get_site_option( 'only_podcasts', '' )); } ?> />
							    <p><?php _e( 'Currently supports podPress or Blubrry PowerPress plugin.', 'inps-multisite-feed' ) ?></p>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="inpsmf_cache_expiry_minutes"><?php _e( 'Cache duration in minutes', 'inps-multisite-feed' ) ?></label>
							</th>
							<td>
								<input class="regular-text" type="text" value="<?php echo get_site_option( 'cache_expiry_minutes', 60 ); ?>" name="inpsyde_multisitefeed[cache_expiry_minutes]" id="inpsmf_cache_expiry_minutes">
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<?php _e( 'Your Feed', 'inps-multisite-feed' ) ?>
							</th>
							<td>
								<?php $url = \Inpsyde\MultisiteFeed\get_feed_url(); ?>
								<a target="_blank" href="<?php echo $url ?>"><?php echo $url ?></a>
							</td>
						</tr>
					</tbody>
				</table>
				<?php submit_button( __( 'Save Changes' ), 'button-primary', 'submit', TRUE ); ?>
			</form>
			
		</div>
		<?php
	}
	
}