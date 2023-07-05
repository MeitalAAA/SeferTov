<?php
/**
 * Dokan Dynamic Tags module
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Module_Dokan_Dynamic_Tags' ) ) {

	/**
	 * Define Gloo_Module_Dokan_Dynamic_Tags class
	 */
	class Gloo_Module_Dokan_Dynamic_Tags extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'dokan_dynamic_tags';
		}


		public function module_dependencies() {
			return [
				'Dokan'      => class_exists( 'WeDevs_Dokan' ),
				'WooGlooModules' => in_array( 'woo_gloo_modules', $this->get_active_modules_from_db()),
			];
		}
		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Dokan Dynamic Tag', 'gloo_for_elementor' );
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
			require  gloo()->modules_path( 'dokan-dynamic-tags/inc/module.php' );
			$this->instance = \Gloo\Modules\Dokan_Dynamic_Tags\Module::instance();
		}

	}

}
