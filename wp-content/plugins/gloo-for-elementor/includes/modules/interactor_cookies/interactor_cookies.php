<?php
/**
 * Zapier Connector module
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Module_Interactor_Cookies' ) ) {

	/**
	 * Define Gloo_Module_Interactor_Cookies class
	 */
	class Gloo_Module_Interactor_Cookies extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'interactor_cookies';
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Cookies for Interactor', 'gloo_for_elementor' );
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
			require  gloo()->modules_path( 'interactor_cookies/inc/module.php' );
			$this->instance = \Gloo\Modules\Interactor_Cookies\Module::instance();
		}

	}

}
