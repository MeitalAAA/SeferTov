<?php
/*
Plugin Name: OTW Woocommerce Price + Widget
Plugin URI: https://otw.design//
Description: Woocommerce price widget for Elementor.
Author: OTW Design
Version: 1.0.0
Author URI: https://otw.design/
Text Domain:       otw-woocommerce-price-widget-td
Domain Path:       /languages
License:           GPL v2 or later
License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
*/


// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Gloo_Module_Woocommerce_Price_Widget' ) ) {

	/**
	 * Define Gloo_Module_Woocommerce_Price_Widget class
	 */
	class Gloo_Module_Woocommerce_Price_Widget extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'gloo_woocommerce_price_widget';
		}

		public function module_dependencies() {
			// array of label => plugin file path

			// return [
			// 	'WooCommerce' => 'woocommerce/woocommerce.php'
			// ];

			// or return boolean value
			// in this case checking for Buddypress and JetSmartFilters
			return [
				'WooCommerce'      => function_exists( 'WC' ),
				'WooGlooModules' => in_array( 'woo_gloo_modules', $this->get_active_modules_from_db()),
			];

		}
		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Woocommerce Price + Widget', 'gloo_for_elementor' );
		}

		/**
		 * Module init
		 *
		 * @return void
		 */
		public function module_init() {
			add_action( 'gloo/init', array( $this, 'create_instance' ) );
		}

		/**
		 * Create module instance
		 *
		 * @return [type] [description]
		 */
		public function create_instance(  ) {
			require  gloo()->modules_path( 'woocommerce-price-widget/inc/module.php' );
			$this->instance = \Gloo\Modules\Woocommerce_Price_Widget\Module::instance();
		}

	}

}
