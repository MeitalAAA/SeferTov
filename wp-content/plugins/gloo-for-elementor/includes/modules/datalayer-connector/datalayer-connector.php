<?php
/**
 * DataLayer Connector module
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Module_DataLayer_Connector' ) ) {

	/**
	 * Define Gloo_Module_DataLayer_Connector class
	 */
	class Gloo_Module_DataLayer_Connector extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'datalayer_connector';
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'DataLayer Connector for Interactor', 'gloo_for_elementor' );
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
			require  gloo()->modules_path( 'datalayer-connector/inc/module.php' );
			$this->instance = \Gloo\Modules\DataLayer_Connector\Module::instance();
		}

	}

}
