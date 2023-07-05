<?php
/**
 * Native Dynamic Tags Kit module
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Module_Native_Dynamic_Tags_Kit' ) ) {

	/**
	 * Define Gloo_Module_Native_Dynamic_Tags_Kit class
	 */
	class Gloo_Module_Native_Dynamic_Tags_Kit extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'native_dynamic_tags_kit';
		}


		// public function module_dependencies() {
		// 	// array of label => plugin file path

		// 	// return [
		// 	// 	'WooCommerce' => 'woocommerce/woocommerce.php'
		// 	// ];

		// 	// or return boolean value
		// 	// in this case checking for Buddypress and JetSmartFilters
		// 	return [
       	// 		 'WooCommerce'      => function_exists( 'WC' ),
		// 	];

		// }
		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Native Dynamic Tags Kit', 'gloo_for_elementor' );
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
			require  gloo()->modules_path( 'native-dynamic-tags/inc/module.php' );
			$this->instance = \Gloo\Modules\Native_Dynamic_Tags_Kit\Module::instance();
		}

	}

}
