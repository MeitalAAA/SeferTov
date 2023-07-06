<?php
/**
 * Gloo_Module_Form_Filepond_Upload
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}
if ( ! class_exists( 'Gloo_Module_Form_Filepond_Upload' ) ) {

	/**
	 * Define Gloo_Module_Form_Filepond_Upload class
	 */
	class Gloo_Module_Form_Filepond_Upload extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'gloo_form_filepond_upload';
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
			return __( 'Multiple File Upload', 'gloo_for_elementor' );
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
			$active_modules = $this->get_active_modules_from_db();

			if(in_array('gloo_frontend_post_submission', $active_modules) || in_array('gloo_frontend_post_editing', $active_modules)) {
				require  gloo()->modules_path( 'form-filepond-upload/inc/module.php' );
				$this->instance = \Gloo\Modules\Form_Filepond_Upload\Module::instance();
			}
		}

	}

}
