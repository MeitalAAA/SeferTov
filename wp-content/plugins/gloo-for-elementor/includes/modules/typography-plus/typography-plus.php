<?php

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Gloo_Module_Typography_Plus' ) ) {

	/**
	 * Define Gloo_Module_Typography_Plus class
	 */
	class Gloo_Module_Typography_Plus extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'gloo_typography_plus';
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Typography Plus', 'gloo_for_elementor' );
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
			require  gloo()->modules_path( 'typography-plus/inc/module.php' );
			$this->instance = \Gloo\Modules\TypographyPlus\Module::instance();
		}

	}

}