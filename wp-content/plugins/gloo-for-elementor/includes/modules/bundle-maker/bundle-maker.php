<?php
/**
 * Woocommerce Bundle Maker
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Module_Bundle_Maker' ) ) {

	/**
	 * Define Gloo_Module_Bundle_Maker class
	 */
	class Gloo_Module_Bundle_Maker extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'woocommerce_bundle_maker';
		}

		public function module_dependencies() {

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
			return __( 'Bundle Maker', 'gloo_for_elementor' );
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
			require  gloo()->modules_path( 'bundle-maker/inc/module.php' );
			$this->instance = \Gloo\Modules\Bundle_Maker_Widget\Module::instance();
		}

	}

}
