<?php
/**
 * Plugin Name: GLoo For Elementor
 * Description: GLoo For Elementor
 * Version:     1.3.59
 * Author:      GLoo
 * Author URI:  http://gloo.ooo
 * Text Domain: gloo_for_elementor
 * Elementor tested up to: 3.12.1
 * Elementor Pro tested up to: 3.12.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

final class Gloo_For_Elementor {

	private $appsero_client = null;
	/**
	 * Minimum PHP Version
	 *
	 * @since 1.0.0
	 * @var string Minimum PHP version required to run the plugin.
	 */
	const MINIMUM_PHP_VERSION = '7.0';

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		//call appsero tracker
		$this->appsero_init_tracker_gloo_for_elementor();

		// Load translation
		add_action( 'init', array( $this, 'i18n' ) );

		// Init Plugin
		add_action( 'plugins_loaded', array( $this, 'init' ) );

		// Plugin activation hook
		register_activation_hook(plugin_basename(__FILE__), array($this, 'PluginActivation'));

	}

	/******************************************/
	/***** Plugin activation function **********/
	/******************************************/
	public function PluginActivation() {
		
	}

	/** * Initialize the plugin tracker * * @return void */

	public function appsero_init_tracker_gloo_for_elementor() {

		global $gloo_license;

		if ( ! class_exists( 'AppseroGloo\Client' ) ) {
			require_once __DIR__ . '/includes/appsero/src/Client.php';
		}

		$client = new AppseroGloo\Client( 'db9eab2e-d5da-482b-9cf2-aa1aa84affd4', 'Gloo For Elementor', __FILE__ );
		// Active insights    

		$client->insights()->init();

		// Active automatic updater    

		$client->updater();
		$this->appsero_client = $client;
		// Active license page and checker    

		add_action( 'admin_menu', function () use ( $client ) {

			// Check if gloo has been activated
			$gloo_license_info = get_option( 'gloo_license_info' );

			$args = array(
				'type'        => 'submenu',
				'menu_title'  => 'GLoo License',
				'page_title'  => 'License Settings',
				'menu_slug'   => 'gloo_for_elementor_settings',
				'parent_slug' => 'gloo-dashboard',
			);

			if ( ! $gloo_license_info || ! is_array( $gloo_license_info ) || ! isset( $gloo_license_info['status'] ) || $gloo_license_info['status'] === 'deactivate' ) {
				$args = array(
					'type'       => 'menu',
					'menu_title' => 'GLoo',
					'page_title' => 'License Settings',
					'menu_slug'  => 'gloo_for_elementor_settings',
					'icon_url'   => trailingslashit( plugin_dir_url( __FILE__ ) ) . 'assets/images/admin/gloo-icon.png',
				);
			}

			$gloo_license = $client->license();
			$gloo_license->add_settings_page( $args );

			$gloo_license->set_option_key( 'gloo_license_info' );
		} );
		add_filter( 'site_transient_update_plugins', array($this, 'update_plugins'));   
		add_action( 'admin_head', function () {
			echo '<style>
				 li#toplevel_page_gloo_for_elementor_settings .toplevel_page_gloo_for_elementor_settings img {
				    padding: 0;
				}
  			</style>';
		} );

	}

	/**
	 * Load Textdomain
	 *
	 * Load plugin localization files.
	 * Fired by `init` action hook.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function i18n() {
		load_plugin_textdomain( 'gloo_for_elementor' );
	}

	public function private_gloo() {
		global $wp_list_table;
		$hide       = array( 'gloo-suite/gloo-suite.php' );
		$my_plugins = $wp_list_table->items;
		foreach ( $my_plugins as $key => $val ) {
			if ( in_array( $key, $hide ) ) {
				unset( $wp_list_table->items[ $key ] );
			}
		}
	}

	/**
	 * Initialize the plugin
	 *
	 * Checks for basic plugin requirements, if one check fail don't continue,
	 * if all check have passed include the plugin class.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function init() {

		// Check if Elementor is installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_elementor_plugin' ] );

			return;
		}

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_php_version' ) );

			return;
		}

		// Check if gloo has been activated
		$gloo_license_info = get_option( 'gloo_license_info' );
		if ( ! $gloo_license_info || ! is_array( $gloo_license_info ) || ! isset( $gloo_license_info['status'] ) || $gloo_license_info['status'] === 'deactivate' ) {
			return;
		}

		// Once we get here, We have passed all validation checks so we can safely include our plugin
		require_once( 'gloo.php' );

	}

	/******************************************/
	/***** plugin Uninstall function **********/
	/******************************************/
	public function update_plugins($update_plugins){
		global $pagenow;
		
		$output = get_transient( 'gloo_last_update_checked');
		if($output && is_array($output) && count($output) >= 1 && isset($output['last_checked']) && (int)wp_date('U') < (int)$output['last_checked']){
      		return $update_plugins;
		}else{
			$output = array('last_checked' => (int)wp_date('U') + (int) DAY_IN_SECONDS);
			set_transient( 'gloo_last_update_checked', $output, DAY_IN_SECONDS );
		}

		if ( 'plugins.php' == $pagenow) {
			if($this->appsero_client && is_object($this->appsero_client->license())){
				if($this->appsero_client->license()->is_valid()){
					$appsero_update = $this->appsero_client->updater();
					$new_std_obj = new stdClass();
					$new_std_obj->slug = 'gloo-for-elementor';
					// if(isset($appsero_update->cache_key) && $appsero_update->cache_key)
					// 	set_transient( $appsero_update->cache_key, '', 0 );
					$plugin_info = $appsero_update->plugins_api_filter(array(), 'plugin_information', $new_std_obj);
					if($plugin_info && is_object($plugin_info) && isset($plugin_info->new_version) && isset($plugin_info->download_link) && $plugin_info->download_link){
						if(isset($remote['version']) && version_compare( $this->appsero_client->project_version, $plugin_info->new_version, '<' ))
						{
							$update_plugins->response[plugin_basename( __FILE__ )] = (object)array(
								'slug'         => $plugin_info->slug,
								'plugin'       => plugin_basename( __FILE__ ),
								'new_version'  => $plugin_info->new_version,
								'url'          => $plugin_info->url,
								'package'      => $plugin_info->download_link,
							);
						}
					}
				}
			}
		}
		return $update_plugins;
   }


	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required PHP version.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_minimum_php_version() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
		/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'gloo_for_elementor' ),
			'<strong>' . esc_html__( 'Gloo For Elementor', 'gloo_for_elementor' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'gloo_for_elementor' ) . '</strong>',
			self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	public function admin_notice_missing_elementor_plugin() {

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
		/* translators: 1: Gloo For Elementor for Elementor 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'gloo_for_elementor' ),
			'<strong>' . esc_html__( 'Gloo For Elementor', 'gloo_for_elementor' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'gloo_for_elementor' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

}

// Instantiate Gloo_For_Elementor.
new Gloo_For_Elementor();