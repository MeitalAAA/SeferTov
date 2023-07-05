<?php
/**
 * Google Adsense
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Module_Google_Adsense' ) ) {

	/**
	 * Define Gloo_Module_Google_Adsense class
	 */
	class Gloo_Module_Google_Adsense extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'gloo_google_adsense';
		}
 
		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Google Adsense', 'gloo_for_elementor' );
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
		public function create_instance() {
			require  gloo()->modules_path( 'google-adsense/inc/module.php' );
			$this->instance = \Gloo\Modules\Google_Adsense_Widget\Module::instance();
		}

	}

}
