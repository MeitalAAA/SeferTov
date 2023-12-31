<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two example hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @link       https://320up.com
 * @since      3.1.0
 *
 * @package    Woo_Align
 * @subpackage Woo_Align/admin
 * @author     320up <support@320up.com>
 */
class Woo_Align_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    3.1.0
	 * @access   private
	 * @var      string    $woo_align    The ID of this plugin.
	 */
	private $woo_align;

	/**
	 * The version of this plugin.
	 *
	 * @since    3.1.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    3.1.0
	 * @param      string    $woo_align       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $woo_align, $version ) {

		$this->woo_align = $woo_align;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    3.1.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woo_Align_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Align_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->woo_align, plugin_dir_url( __FILE__ ) . 'css/woo-align-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    3.1.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woo_Align_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Align_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->woo_align, plugin_dir_url( __FILE__ ) . 'js/woo-align-admin.js', array( 'jquery' ), $this->version, false );

	}

}
