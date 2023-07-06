<?php
/**
 * Global Elementor Tags
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Module_Global_Elementor_Tags' ) ) {

	/**
	 * Define Gloo_Module_Global_Elementor_Tags class
	 */
	class Gloo_Module_Global_Elementor_Tags extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'elementor_global_tag';
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Global Elementor Tags', 'gloo_for_elementor' );
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
			require  gloo()->modules_path( 'global-elementor-tags/inc/module.php' );
			$this->instance = \Gloo\Modules\Global_Elementor_Tags\Module::instance();
		}

	}

}
