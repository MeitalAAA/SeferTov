<?php
/**
 * Custom Webhook module
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Module_Custom_Webhook' ) ) {

	/**
	 * Define Gloo_Module_Custom_Webhook class
	 */
	class Gloo_Module_Custom_Webhook extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'custom_webhook_connector';
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Custom Webhook Connector', 'gloo_for_elementor' );
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
			require  gloo()->modules_path( 'custom-webhook-connector/inc/module.php' );
			$this->instance = \Gloo\Modules\Custom_Webhook\Module::instance();
		}

	}

}
