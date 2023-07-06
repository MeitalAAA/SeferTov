<?php

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Gloo_Module_Ajax_Reload_Prevention' ) ) {

	/**
	 * Define Gloo_Module_Ajax_Reload_Prevention class
	 */
	class Gloo_Module_Ajax_Reload_Prevention extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'ajax_reload_prevention';
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
			return __( 'Ajax Reload Prevention', 'gloo' );
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
			require  gloo()->modules_path( 'ajax-reload-prevention/inc/module.php' );
			$this->instance = \Gloo\Modules\AjaxReloadPrevention\Module::instance();
		}

	}

}
