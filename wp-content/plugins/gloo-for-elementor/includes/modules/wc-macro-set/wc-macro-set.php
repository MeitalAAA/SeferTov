<?php
/**
 * WooCommerce Macro Set module
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Module_WC_Macro_Set' ) ) {

	/**
	 * Define Gloo_Module_WC_Macro_Set class
	 */
	class Gloo_Module_WC_Macro_Set extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'wc_macro_set';
		}


		public function module_dependencies() {
			// array of label => plugin file path

			// return [
			// 	'WooCommerce' => 'woocommerce/woocommerce.php'
			// ];

			// or return boolean value
			// in this case checking for Buddypress and JetSmartFilters
			return [
        'jet_engine'      => function_exists( 'jet_engine' ),
        'WooCommerce'      => function_exists( 'WC' ),
			];

		}
		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'WooCommerce Macro Set', 'gloo_for_elementor' );
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
			require  gloo()->modules_path( 'wc-macro-set/inc/module.php' );
			$this->instance = \Gloo\Modules\WC_Macro_Set\Module::instance();
		}

	}

}
