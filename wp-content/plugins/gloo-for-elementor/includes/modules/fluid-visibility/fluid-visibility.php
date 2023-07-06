<?php
/**
 * Fluid Visibility module
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Module_Fluid_Visibility' ) ) {

	/**
	 * Define Gloo_Module_Fluid_Visibility class
	 */
	class Gloo_Module_Fluid_Visibility extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'fluid_visibility';
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Fluid Logic', 'gloo_for_elementor' );
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
			require  gloo()->modules_path( 'fluid-visibility/inc/module.php' );
			$this->instance = \Gloo\Modules\Fluid_Visibility\Module::instance();
		}

	}

}
