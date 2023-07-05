<?php

/**
 * The file that defines the core plugin class.
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://320up.com
 * @since      3.1.0
 *
 * @package    Woo_Align
 * @subpackage Woo_Align/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      3.1.0
 * @package    Woo_Align
 * @subpackage Woo_Align/includes
 * @author     320up <support@320up.com>
 */
class Woo_Align {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    3.1.0
	 * @access   protected
	 * @var      Woo_Align_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    3.1.0
	 * @access   protected
	 * @var      string    $woo_align    The string used to uniquely identify this plugin.
	 */
	protected $woo_align;

	/**
	 * The current version of the plugin.
	 *
	 * @since    3.1.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    3.1.0
	 */
	public function __construct() {
		if ( defined( 'WOO_ALIGN_VERSION' ) ) {
			$this->version = WOO_ALIGN_VERSION;
		} else {
			$this->version = '3.6.8';
		}
		$this->woo_align = 'woo-align';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Woo_Align_Loader. Orchestrates the hooks of the plugin.
	 * - Woo_Align_i18n. Defines internationalization functionality.
	 * - Woo_Align_Admin. Defines all hooks for the admin area.
	 * - Woo_Align_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    3.1.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woo-align-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woo-align-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-woo-align-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-woo-align-public.php';

		$this->loader = new Woo_Align_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Woo_Align_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    3.1.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Woo_Align_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    3.1.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Woo_Align_Admin( $this->get_woo_align(), $this->get_version() );

		//$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		//$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    3.1.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Woo_Align_Public( $this->get_woo_align(), $this->get_version() );

		//$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'woocommerce_before_shop_loop_item', $plugin_public, 'wooalign_product_link_open' );
		$this->loader->add_action( 'woocommerce_after_shop_loop_item', $plugin_public, 'wooalign_product_link_close' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    3.1.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     3.1.0
	 * @return    string    The name of the plugin.
	 */
	public function get_woo_align() {
		return $this->woo_align;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     3.1.0
	 * @return    Woo_Align_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     3.1.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
