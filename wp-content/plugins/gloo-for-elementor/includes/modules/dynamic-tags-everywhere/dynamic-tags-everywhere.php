<?php
/**
 * Dynamic Tags Everywhere module
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Module_Dynamic_Tags_Everywhere' ) ) {

	/**
	 * Define Gloo_Module_Interactor class
	 */
	class Gloo_Module_Dynamic_Tags_Everywhere extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'dynamic_tags_everywhere';
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Dynamic Tags Everywhere', 'gloo_for_elementor' );
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
			require  gloo()->modules_path( 'dynamic-tags-everywhere/inc/module.php' );
			$this->instance = \Gloo\Modules\Dynamic_Tags_Everywhere\Module::instance();
		}

	}

}
