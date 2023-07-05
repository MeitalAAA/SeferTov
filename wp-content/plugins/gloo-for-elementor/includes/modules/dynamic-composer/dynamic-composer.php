<?php
/**
 * Dynamic Composer
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Module_Dynamic_Composer' ) ) {

	/**
	 * Define Gloo_Module_Dynamic_Composer class
	 */
	class Gloo_Module_Dynamic_Composer extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'gloo_dynamic_composer';
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Dynamic Composer', 'gloo_for_elementor' );
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
			require  gloo()->modules_path( 'dynamic-composer/inc/module.php' );
			$this->instance = \Gloo\Modules\Dynamic_Composer\Module::instance();
		}

	}

}
