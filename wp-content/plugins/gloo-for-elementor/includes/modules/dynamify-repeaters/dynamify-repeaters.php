<?php
/**
 * Dynamify Repeaters
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Dynamify_Repeaters' ) ) {

	/**
	 * Define Gloo_Dynamify_Repeaters class
	 */
	class Gloo_Dynamify_Repeaters extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'dynamify_repeaters';
		}


		/**
		 * Module Dependencies
		 */
		public function module_dependencies() {
			return [
				'Elementor Pro'      => defined( 'ELEMENTOR_PRO_VERSION' ),
			];
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Dynamify Repeaters', 'gloo_for_elementor' );
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
			require  gloo()->modules_path( 'dynamify-repeaters/inc/module.php' );
			$this->instance = \Gloo\Modules\Dynamify_Repeaters\Module::instance();
		}

	}

}
