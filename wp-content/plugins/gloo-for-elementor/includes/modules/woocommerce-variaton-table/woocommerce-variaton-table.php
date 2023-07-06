<?php
/**
 * Woocommerce Variation Table
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Module_Woocommerce_Variation_Table' ) ) {

	/**
	 * Define Gloo_Module_Woocommerce_Variation_Table class
	 */
	class Gloo_Module_Woocommerce_Variation_Table extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'woocommerce_variation_table';
		}

		public function module_dependencies() {
			
			return [
				'WooCommerce Product Variations Table' => 'product-variations-table-for-woocommerce/variation-table.php',
				'WooGlooModules' => in_array( 'woo_gloo_modules', $this->get_active_modules_from_db()),
			];

		}
		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Woocommerce Variation Table', 'gloo_for_elementor' );
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
		public function create_instance() {
			require  gloo()->modules_path( 'woocommerce-variaton-table/inc/module.php' );
			$this->instance = \Gloo\Modules\Woo_Variaton_Table\Module::instance();
		}

	}

}
