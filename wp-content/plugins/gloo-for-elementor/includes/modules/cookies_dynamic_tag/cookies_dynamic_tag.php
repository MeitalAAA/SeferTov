<?php

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Gloo_Module_Cookies_Dynamic_Tag' ) ) {

	/**
	 * Define Gloo_Module_Cookies_Dynamic_Tag class
	 */
	class Gloo_Module_Cookies_Dynamic_Tag extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'cookies_dynamic_tag';
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Cookies Dynamic Tag', 'gloo' );
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
			require  gloo()->modules_path( 'cookies_dynamic_tag/inc/module.php' );
			$this->instance = \Gloo\Modules\CookiesDynamicTag\Module::instance();
		}

	}

}