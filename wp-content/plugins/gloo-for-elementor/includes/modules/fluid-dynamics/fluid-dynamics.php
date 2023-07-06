<?php
/**
 * Fluid Dynamics module
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Module_Fluid_Dynamics' ) ) {

	/**
	 * Define Gloo_Module_Interactor class
	 */
	class Gloo_Module_Fluid_Dynamics extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'fluid_dynamics';
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Fluid Dynamics', 'gloo_for_elementor' );
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
			require  gloo()->modules_path( 'fluid-dynamics/inc/module.php' );
			$this->instance = \Gloo\Modules\Fluid_Dynamics\Module::instance();
		}

	}

}