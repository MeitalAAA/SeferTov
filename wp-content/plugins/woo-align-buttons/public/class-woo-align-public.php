<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two example hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @link       https://320up.com
 * @since      3.1.0
 *
 * @package    Woo_Align
 * @subpackage Woo_Align/public
 * @author     320up <support@320up.com>
 */
class Woo_Align_Public {

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
	 * @param      string    $woo_align       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $woo_align, $version ) {

		$this->woo_align = $woo_align;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->woo_align, plugin_dir_url( __FILE__ ) . 'css/woo-align-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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
		 *
		 * false = script in head
		 * true = script in footer
		 *
		 * Only use in head if calling script externally
		 */

		//wp_enqueue_script( $this->woo_align, plugin_dir_url( __FILE__ ) . 'js/woo-align-public-pure.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( $this->woo_align, plugin_dir_url( __FILE__ ) . 'js/woo-align-public.js', array( 'jquery' ), $this->version, true );

	}

	/**
	 * Add wrapper for woo-height script.
	 *
	 * @since    3.1.0
	 */

	public function wooalign_product_link_open() {
		echo '<div class="woo-height">';
	}

	public function wooalign_product_link_close() {
		echo '</div>';
	}

}
