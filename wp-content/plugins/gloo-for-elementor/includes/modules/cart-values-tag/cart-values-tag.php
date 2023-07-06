<?php
/**
 * Cart Values Dynamic Tags
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Module_Cart_Values_Dynamic_Tags' ) ) {

	/**
	 * Define Gloo_Module_Time_Span_Dynamic_Tags class
	 */
	class Gloo_Module_Cart_Values_Dynamic_Tags extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'woo_cart_values';
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
			return __( 'Cart Values Dynamic Tags', 'gloo_for_elementor' );
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
			require  gloo()->modules_path( 'cart-values-tag/inc/module.php' );
			$this->instance = \Gloo\Modules\Cart_Values_Dynamic_Tags\Module::instance();
		}

	}

}
