<?php
/**
 * Zapier Connector module
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Module_Zapier_Connector' ) ) {

	/**
	 * Define Gloo_Module_Zapier_Connector class
	 */
	class Gloo_Module_Zapier_Connector extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'zapier_connector';
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Zapier Connector for Interactor', 'gloo_for_elementor' );
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
			require  gloo()->modules_path( 'zapier-connector/inc/module.php' );
			$this->instance = \Gloo\Modules\Zapier_Connector\Module::instance();
		}

	}

}
