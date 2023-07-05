<?php
/**
 * Zapier Connector module
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Module_Interactor_Gsap' ) ) {

	/**
	 * Define Gloo_Module_Interactor_Gsap class
	 */
	class Gloo_Module_Interactor_Gsap extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'interactor_gsap';
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'GSAP Events', 'gloo_for_elementor' );
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
			require  gloo()->modules_path( 'interactor_gsap/inc/module.php' );
			$this->instance = \Gloo\Modules\Interactor_Gsap\Module::instance();
		}

	}

}
