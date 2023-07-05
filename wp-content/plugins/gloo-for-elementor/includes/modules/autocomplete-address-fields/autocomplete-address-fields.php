<?php
/**
 * Autocomplete Address Fields
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Module_Autocomplete_Address_Fields' ) ) {

	/**
	 * Define Gloo_Module_Autocomplete_Address_Fields class
	 */
	class Gloo_Module_Autocomplete_Address_Fields extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'gloo_autocomplete_address_fields';
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Autocomplete Address Fields', 'gloo_for_elementor' );
		}

		/**
		 * Module Dependencies
		 */
		public function module_dependencies() {
			return [
				'GlooFormsExtensions'      => $this->is_parent_module_active(),
			];
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
			require  gloo()->modules_path( 'autocomplete-address-fields/inc/module.php' );
			$this->instance = \Gloo\Modules\Autocomplete_Address_Fields\Module::instance();
		}

	}

}
