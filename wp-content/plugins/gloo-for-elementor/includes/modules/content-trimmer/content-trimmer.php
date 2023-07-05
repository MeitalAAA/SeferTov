<?php
/**
 * Content Trimmer
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Module_Content_Trimmer' ) ) {

	/**
	 * Define Gloo_Module_Content_Trimmer class
	 */
	class Gloo_Module_Content_Trimmer extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'gloo_content_trimmer';
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Content Trimmer', 'gloo_for_elementor' );
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
			require  gloo()->modules_path( 'content-trimmer/inc/module.php' );
			$this->instance = \Gloo\Modules\Content_Trimmer\Module::instance();
		}

	}

}
