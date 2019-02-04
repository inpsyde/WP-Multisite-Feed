<?php

namespace Inpsyde\MultisiteFeed;

/**
 * Settings Page Class
 *
 * @authors et, fb
 * @since   2.0.0  03/26/2012
 */
class SettingsPage {

	const MENU_SLUG = 'inpsyde-multisite-feed-page';
	const OPTION_KEY = 'inpsyde_multisitefeed';

	private $page_hook;
	/**
	 * @var DataStorage
	 */
	private $settings;

	/**
	 * SettingsPage constructor.
	 *
	 * @param DataStorage $settings
	 */
	public function __construct( DataStorage $settings ) {

		$this->settings = $settings;
	}

	public function init() {

		add_action( 'network_admin_menu', [ $this, 'init_menu' ] );
	}

	public function init_menu() {

		$this->page_hook = add_submenu_page(
		/* $parent_slug*/
			'settings.php',
			/* $page_title */
			'Multisite Feed',
			/* $menu_title */
			'Multisite Feed',
			/* $capability */
			'manage_users',
			/* $menu_slug  */
			self::MENU_SLUG,
			/* $function   */
			[ $this, 'render_page' ]
		);

	}

	/**
	 * Get settings pages incl. markup
	 *
	 * @author  et, fb
	 * @since   2.0.0  03/26/2012
	 * @return  void
	 */
	public function render_page() {

		$this->save();
		?>
		<div class="wrap">

			<h2><?php _e( 'Multisite Feed Settings', 'inps-multisite-feed' ); ?></h2>

			<form method="post" action="#">

				<?php
				echo '<input type="hidden" name="action" value="update" />';
				wp_nonce_field( 'inpsmf-options' );
				?>

				<table class="form-table">
					<tbody>
					<tr valign="top">
						<th scope="row">
							<label for="inpsmf_<?php echo OptionsKeys::TITLE ?>"><?php _e( 'Title',
									'inps-multisite-feed' ) ?></label>
						</th>
						<td>
							<input class="regular-text" type="text" value="<?php echo esc_attr(
								$this->settings->get(
									OptionsKeys::TITLE, ''
								)
							); ?>" name="<?php echo self::OPTION_KEY ?>[<?php echo OptionsKeys::TITLE ?>]"
							       id="inpsmf_<?php echo OptionsKeys::TITLE ?>">
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="inpsmf_<?php echo OptionsKeys::DESCRIPTION ?>"><?php _e( 'Description',
									'inps-multisite-feed' ) ?></label>
						</th>
						<td>
							<textarea name="<?php echo self::OPTION_KEY ?>[<?php echo OptionsKeys::DESCRIPTION ?>]"
							          id="inpsmf_<?php echo OptionsKeys::DESCRIPTION ?>"
							          cols="40"
							          rows="7"><?php echo esc_attr(
									$this->settings->get(
										OptionsKeys::DESCRIPTION, ''
									)
								); ?></textarea>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="inpsmf_<?php echo OptionsKeys::URL_SLUG ?>"><?php _e( 'Url',
									'inps-multisite-feed' ) ?></label>
						</th>
						<td>
							<input class="regular-text" type="text" value="<?php echo esc_attr(
								$this->settings->get(
									OptionsKeys::URL_SLUG, 'multifeed'
								)
							); ?>" name="<?php echo self::OPTION_KEY ?>[<?php echo OptionsKeys::URL_SLUG ?>]"
							       id="inpsmf_<?php echo OptionsKeys::URL_SLUG ?>">
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="inpsmf_<?php echo OptionsKeys::LANGUAGE_SLUG ?>"><?php _e(
									'RSS Language', 'inps-multisite-feed'
								) ?></label>
						</th>
						<td>
							<input class="regular-text" type="text" value="<?php echo esc_attr(
								$this->settings->get(
									OptionsKeys::LANGUAGE_SLUG, 'en'
								)
							); ?>" name="<?php echo self::OPTION_KEY ?>[<?php echo OptionsKeys::LANGUAGE_SLUG ?>]"
							       id="inpsmf_<?php echo OptionsKeys::LANGUAGE_SLUG ?>">

							<p><?php _e(
									'Language key for the feed. Use the keys from the <a href="http://www.loc.gov/standards/iso639-2/php/code_list.php">ISO-639 language key</a>, not the same as the WPLANG constant.',
									'inps-multisite-feed'
								); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="inpsmf_<?php echo OptionsKeys::MAX_ENTRIES_PER_SITE ?>"><?php _e(
									'Max. entries per site', 'inps-multisite-feed'
								) ?></label>
						</th>
						<td>
							<input class="regular-text" type="text" value="<?php echo (int) $this->settings->get(
								OptionsKeys::MAX_ENTRIES_PER_SITE, 20
							); ?>"
							       name="<?php echo self::OPTION_KEY ?>[<?php echo OptionsKeys::MAX_ENTRIES_PER_SITE ?>]"
							       id="inpsmf_<?php echo OptionsKeys::MAX_ENTRIES_PER_SITE ?>">
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="inpsmf_<?php echo OptionsKeys::MAX_ENTRIES ?>"><?php _e(
									'Max. entries overall', 'inps-multisite-feed'
								) ?></label>
						</th>
						<td>
							<input class="regular-text" type="text" value="<?php echo (int) $this->settings->get(
								'max_entries', 100
							); ?>" name="<?php echo self::OPTION_KEY ?>[<?php echo OptionsKeys::MAX_ENTRIES ?>]"
							       id="inpsmf_<?php echo OptionsKeys::MAX_ENTRIES ?>">
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="inpsmf_<?php echo OptionsKeys::EXCLUDED_BLOGS ?>"><?php _e(
									'Exclude blogs', 'inps-multisite-feed'
								) ?></label>
						</th>
						<td>
							<input
									class="regular-text"
									type="text"
									value="<?php echo esc_attr( $this->settings->get( OptionsKeys::EXCLUDED_BLOGS,
										'' ) ); ?>"
									name="<?php echo self::OPTION_KEY ?>[<?php echo OptionsKeys::EXCLUDED_BLOGS ?>]"
									id="inpsmf_<?php echo OptionsKeys::EXCLUDED_BLOGS ?>"
							>

							<p><?php _e(
									'Blog IDs, separated by comma. Leave empty to include all blogs.',
									'inps-multisite-feed'
								) ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="inpsmf_<?php echo OptionsKeys::ONLY_AUTHORS ?>"><?php _e(
									'Include authors', 'inps-multisite-feed'
								) ?></label>
						</th>
						<td>
							<input
									class="regular-text"
									type="text"
									value="<?php echo esc_attr(
										$this->settings->get(
											OptionsKeys::ONLY_AUTHORS, ''
										)
									); ?>"
									name="<?php echo self::OPTION_KEY ?>[<?php echo OptionsKeys::ONLY_AUTHORS ?>]"
									id="inpsmf_<?php echo OptionsKeys::ONLY_AUTHORS ?>"
							>

							<p><?php _e(
									'Author IDs, separated by comma. Leave empty to include all authors.',
									'inps-multisite-feed'
								) ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="inpsmf_<?php echo OptionsKeys::ONLY_PODCASTS ?>"><?php _e(
									'Only include podcast episodes', 'inps-multisite-feed'
								) ?></label>
						</th>
						<td>
							<input id="inpsmf_<?php echo OptionsKeys::ONLY_PODCASTS ?>"
							       name="<?php echo self::OPTION_KEY ?>[<?php echo OptionsKeys::ONLY_PODCASTS ?>]"
							       type="checkbox"
							       value="1" <?php if ( $this->settings->get(
								OptionsKeys::ONLY_PODCASTS, ''
							)
							) {
								checked( '1', $this->settings->get( OptionsKeys::ONLY_PODCASTS, '' ) );
							} ?> />

							<p><?php _e(
									'Currently supports podPress or Blubrry PowerPress plugin.', 'inps-multisite-feed'
								) ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="inpsmf_<?php echo OptionsKeys::USE_MORE ?>"><?php _e( 'Full Feed',
									'inps-multisite-feed' ) ?></label>
						</th>
						<td>
							<input
									id="inpsmf_<?php echo OptionsKeys::USE_MORE ?>"
									name="<?php echo self::OPTION_KEY ?>[<?php echo OptionsKeys::USE_MORE ?>]"
									type="checkbox"
									value="1"
								<?php checked( '1', $this->settings->get( OptionsKeys::USE_MORE, '' ) ); ?>
							/>

							<p><?php _e( 'For each article in a feed, show full text.', 'inps-multisite-feed' ) ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="inpsmf_<?php echo OptionsKeys::USE_EXCERPT ?>"><?php _e( 'Use excerpt',
									'inps-multisite-feed' ) ?></label>
						</th>
						<td>
							<input
									id="inpsmf_<?php echo OptionsKeys::USE_EXCERPT ?>"
									name="<?php echo self::OPTION_KEY ?>[<?php echo OptionsKeys::USE_EXCERPT ?>]"
									type="checkbox" value="1"
								<?php checked( '1', $this->settings->get( OptionsKeys::USE_EXCERPT, '' ) ); ?>
							/>
							<p><?php _e( 'For each article in a feed, use the excerpt.', 'inps-multisite-feed' ) ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="inpsmf_<?php echo OptionsKeys::CACHE_EXPIRY ?>"><?php _e(
									'Cache duration in minutes', 'inps-multisite-feed'
								) ?></label>
						</th>
						<td>
							<input class="regular-text" type="text" value="<?php echo (int) $this->settings->get(
								'cache_expiry_minutes', 60
							); ?>" name="<?php echo self::OPTION_KEY ?>[<?php echo OptionsKeys::CACHE_EXPIRY ?>]"
							       id="inpsmf_<?php echo OptionsKeys::CACHE_EXPIRY ?>">

							<p><?php _e( 'Set to 0 for deactivate caching.', 'inps-multisite-feed' ) ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<?php _e( 'Your Feed', 'inps-multisite-feed' ) ?>
						</th>
						<td>
							<?php $url = Plugin::get_feed_url() ?>
							<a target="_blank" href="<?php echo esc_url( $url ); ?>"><?php echo esc_url( $url ); ?></a>
						</td>
					</tr>
					</tbody>
				</table>
				<?php submit_button( __( 'Save Changes' ), 'button-primary', 'submit', true ); ?>
			</form>

		</div>
		<?php
	}

	/**
	 * Save settings
	 *
	 * @since   2.0.0  03/26/2012
	 * @version 2019-02-04
	 * @return  void
	 */
	public function save() {

		if ( ! isset( $_POST['action'] ) || 'update' !== $_POST['action'] || self::MENU_SLUG !== $_GET['page'] ) {
			return null;
		}

		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'inpsmf-options' ) ) {
			wp_die( 'Sorry, you failed the nonce test.' );
		}

		$options = \get_site_option( self::OPTION_KEY );
		$request = $_REQUEST[ self::OPTION_KEY ];
		foreach ( $options as $optionKey => $isValue ) {
			$newValue = '';
			if ( array_key_exists( $optionKey, $request ) ) {
				$newValue = $request[ $optionKey ];
			}
			$this->settings->set( $optionKey, $newValue );
		}

		do_action( 'inpsmf_update_settings' );

	}

}
