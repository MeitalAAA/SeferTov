<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Module_Elementor_Select2_Fields' ) ) {

	/**
	 * Define Gloo_Module_Elementor_Select2_Fields class
	 */
	class Gloo_Module_Elementor_Select2_Fields extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'gloo_elementor_select2_fields';
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Elementor Select2 Fields', 'gloo_for_elementor' );
		}

		/**
		 * Module init
		 *
		 * @return void
		 */
		public function module_init() {
			add_action( 'gloo/init', array( $this, 'create_instance' ) );
		}
		
		public function module_dependencies() {
			return [
				'ElementorPro'      => defined( 'ELEMENTOR_PRO_VERSION' ),
				'GlooFormsExtensions'      => $this->is_parent_module_active(),
			];
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
			require  gloo()->modules_path( 'elementor-select2-fields/inc/module.php' );
			$this->instance = \Gloo\Modules\Elementor_Select2_Fields\Module::instance();
		}

	}

}
