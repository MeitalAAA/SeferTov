<?php
/**
 * Relationship Dynamic Tags
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Module_Relationship_Dynamic_Tags' ) ) {

	/**
	 * Define Gloo_Module_Relationship_Dynamic_Tags class
	 */
	class Gloo_Module_Relationship_Dynamic_Tags extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'jetengine_macros_dynamic_tag';
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Relationship Dynamic Tag', 'gloo_for_elementor' );
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
			require  gloo()->modules_path( 'relationship-dynamic-tags/inc/module.php' );
			$this->instance = \Gloo\Modules\Relationship_Dynamic_Tags\Module::instance();
		}

	}

}
