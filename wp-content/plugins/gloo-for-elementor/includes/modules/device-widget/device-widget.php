<?php
/**
 * Device Widget
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Module_Device_Widget' ) ) {

	/**
	 * Define Gloo_Module_Device_Widget class
	 */
	class Gloo_Module_Device_Widget extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'device_widget';
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Device Widget', 'gloo_for_elementor' );
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
		public function create_instance() {
			require  gloo()->modules_path( 'device-widget/inc/module.php' );
			$this->instance = \Gloo\Modules\Device_Widget\Module::instance();
		}

	}

}
