<?php
/**
 * Gloo_Module_Form_Country_Dial_Code
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}
if ( ! class_exists( 'Gloo_Module_Form_Country_Dial_Code' ) ) {

	/**
	 * Define Gloo_Module_Form_Country_Dial_Code class
	 */
	class Gloo_Module_Form_Country_Dial_Code extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'gloo_form_country_dial_code_field';
		}

		public function module_dependencies() {
			// array of label => plugin file path
			// or return boolean value
			return [
 				'GlooFormsExtensions'      => $this->is_parent_module_active(),
			];
		}
		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Country Dial Code Field', 'gloo_for_elementor' );
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
			
			require  gloo()->modules_path( 'form-country-dial-code/inc/module.php' );
			$this->instance = \Gloo\Modules\Form_Country_Dial_Code\Module::instance();
		}

	}

}
