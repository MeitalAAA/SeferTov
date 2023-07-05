<?php
/**
 * Multi Currency Tags module
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Module_Multi_Currency_Tag' ) ) {

	/**
	 * Define Gloo_Module_Multi_Currency_Tag class
	 */
	class Gloo_Module_Multi_Currency_Tag extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'multi_currency_dynamic_tags';
		}


		public function module_dependencies() {
			return [
				'WOOMULTI_CURRENCY'      => class_exists( 'WOOMULTI_CURRENCY' ),
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
			return __( 'Multi Currency Dynamic Tag', 'gloo_for_elementor' );
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
			require  gloo()->modules_path( 'multi-currency-dynamic-tag/inc/module.php' );
			$this->instance = \Gloo\Modules\Multi_Currency_Tag\Module::instance();
		}

	}

}
