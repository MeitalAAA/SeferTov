<?php
/**
 * Gmb Reviews
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Module_Gmb_Review' ) ) {

	/**
	 * Define Gloo_Module_Gmb_Review class
	 */
	class Gloo_Module_Gmb_Review extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'gloo_gmb_review';
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'GMB Reviews Kit', 'gloo_for_elementor' );
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
			require  gloo()->modules_path( 'gmb-reviews/inc/module.php' );
			$this->instance = \Gloo\Modules\Gmb_Reviews\Module::instance();
		}

	}

}
