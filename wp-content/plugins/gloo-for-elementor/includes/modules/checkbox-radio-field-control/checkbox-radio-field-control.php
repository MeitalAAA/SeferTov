<?php
/**
 * Gloo_Checkbox_Radio_Field_Control
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}
if ( ! class_exists( 'Gloo_Checkbox_Radio_Field_Control' ) ) {

	/**
	 * Define Gloo_Checkbox_Radio_Field_Control class
	 */
	class Gloo_Checkbox_Radio_Field_Control extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'gloo_checkbox_radio_field_control';
		}

		public function module_dependencies() {
			// array of label => plugin file path
			// or return boolean value
			return [
				'ElementorPro'      => defined( 'ELEMENTOR_PRO_VERSION' ),
				'GlooFormsExtensions'      => $this->is_parent_module_active(),
			];
		}
		
		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Checkbox & Radio Field Control', 'gloo_for_elementor' );
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
		 * is_parent_module_active
		 *
		 * @return true/false
		 */
		public function is_parent_module_active() {
			$output = false;
			$active_modules = $this->get_active_modules_from_db();
			if (in_array('gloo_zoho', $active_modules) || in_array('little_engine', $active_modules))
				$output = true;
			return $output;
		}
		/**
		 * Create module instance
		 *
		 * @return [type] [description]
		 */
		public function create_instance(  ) {
			require  gloo()->modules_path( 'checkbox-radio-field-control/inc/module.php' );
			$this->instance = \Gloo\Modules\Checkbox_Radio_Field_Control\Module::instance();
		}

	}

}
