<?php
/**
 * Facebook Reviews
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Module_Facebook_Reviews' ) ) {

	/**
	 * Define Gloo_Module_Facebook_Reviews class
	 */
	class Gloo_Module_Facebook_Reviews extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'gloo_facebook_review';
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Facebook Reviews Kit', 'gloo_for_elementor' );
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
			require  gloo()->modules_path( 'facebook-reviews/inc/module.php' );
			$this->instance = \Gloo\Modules\Facebook_Reviews\Module::instance();
		}

	}

}
