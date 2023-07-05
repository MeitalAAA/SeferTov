<?php

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Gloo_Module_Checkout_Anything' ) ) {

	/**
	 * Define Gloo_Module_Checkout_Anything class
	 */
	class Gloo_Module_Checkout_Anything extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'checkout_anything';
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Checkout Anything', 'gloo' );
		}

		/**
		 * Module init
		 *
		 * @return void
		 */
		public function module_init() {
			add_action( 'gloo/init', array( $this, 'create_instance' ) );
		}

		public function module_dependencies() {
			return [
				'ElementorPro'      => defined( 'ELEMENTOR_PRO_VERSION' ),
				'GlooFormsExtensions'      => $this->is_parent_module_active(),
			];
		}
		
		/**
		 * is_parent_module_active
		 *
		 * @return true/false
		 */
		public function is_parent_module_active() {
			$output = false;
			$active_modules = $this->get_active_modules_from_db();
			if (in_array('woo_gloo_modules', $active_modules)/* || in_array('little_engine', $active_modules)*/)
				$output = true;
			return $output;
		}
		
		/**
		 * Create module instance
		 *
		 * @return [type] [description]
		 */
		public function create_instance(  ) {
			require  gloo()->modules_path( 'checkout-anything/inc/module.php' );
			$this->instance = \Gloo\Modules\CheckoutAnything\Module::instance();
		}

	}

}






//define('OTW_WOOCOMMERCE_PRICE_WIDGET_FILE', __FILE__);


//include_once plugin_dir_path(OTW_WOOCOMMERCE_PRICE_WIDGET_FILE).'inc/autoload.php';

// add the data sanitization and validation class
//if(!class_exists('BBWPSanitization'))
//  include_once BBWP_FLUID_DYNAMICS_ABS.'inc/classes/BBWPSanitization.php';


