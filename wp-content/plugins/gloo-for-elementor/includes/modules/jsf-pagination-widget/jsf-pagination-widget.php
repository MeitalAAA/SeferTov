<?php

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'JSF_Pagination_Widget' ) ) {

	/**
	 * Define JSF_Pagination_Widget class
	 */
	class JSF_Pagination_Widget extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'jsf_pagination_widget';
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Woo Gloo Modules', 'gloo_for_elementor' );
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
			require  gloo()->modules_path( 'jsf-pagination-widget/inc/module.php' );
			$this->instance = \Gloo\Modules\JSF_Pagination_Widget\Module::instance();
		}

	}

}
